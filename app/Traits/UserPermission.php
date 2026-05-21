<?php

namespace App\Traits;

use App\Models\Permission;
use Auth;

trait UserPermission
{
    public function checkRequestPermission(): void
    {
        if (!Auth::check()) {
            abort(401);
        }

        $user = Auth::user();

        if ($user->super_admin || (int) $user->permission_id === 1) {
            return;
        }

        $role = $user->permission ?? Permission::find($user->permission_id);
        if (!$role || empty($role->permission)) {
            return;
        }

        $permissions = json_decode($role->permission, true);
        if (!is_array($permissions)) {
            return;
        }

        $required = $this->resolveRoutePermission();
        if ($required === null) {
            return;
        }

        [$module, $action] = $required;

        if ($this->permissionGranted($permissions, $module, $action)) {
            return;
        }

        abort(403, 'You do not have permission to access this page.');
    }

    protected function resolveRoutePermission(): ?array
    {
        $path = trim(request()->path(), '/');
        $method = strtoupper(request()->method());

        $map = [
            'admin/payroll/process' => ['payroll_process', $method === 'POST' ? 'process' : 'view'],
            'admin/payroll' => ['payroll_process', 'view'],
            'admin/payroll/salary-sheet' => ['salary_sheet', 'view'],
            'admin/payroll/salary-summary' => ['salary_sheet', 'view'],
            'admin/payroll/daily-salary-sheet' => ['salary_sheet', 'view'],
            'admin/payroll/held-salary' => ['salary_sheet', 'view'],
            'admin/payroll/export' => ['salary_sheet', 'export'],
            'admin/payroll/salary-sheet/export' => ['salary_sheet', 'export'],
            'admin/payroll/bulk-pay-slip' => ['payslip', 'print'],
            'admin/payroll/bulk-mark-paid' => ['payroll_process', 'edit'],
            'admin/reports/payroll' => ['payroll_report', 'view'],
            'admin/tax' => ['payroll_process', 'edit'],
            'admin/provident-fund' => ['payroll_process', 'edit'],
        ];

        if (isset($map[$path])) {
            return $map[$path];
        }

        if (preg_match('#^admin/payroll/pay-slip/\d+#', $path)) {
            return ['payslip', 'view'];
        }
        if (preg_match('#^admin/payroll/\d+/(mark-paid|update|mark-held)#', $path)) {
            return ['payroll_process', 'edit'];
        }

        return null;
    }

    protected function permissionGranted(array $permissions, string $module, string $action): bool
    {
        if (!isset($permissions[$module]) || !is_array($permissions[$module])) {
            return false;
        }

        $modulePerms = $permissions[$module];

        if (!empty($modulePerms['all']) && in_array($modulePerms['all'], ['on', '1', true, 1], true)) {
            return true;
        }

        if (!empty($modulePerms[$action]) && in_array($modulePerms[$action], ['on', '1', true, 1], true)) {
            return true;
        }

        return false;
    }
}
