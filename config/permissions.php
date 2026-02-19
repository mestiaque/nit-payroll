<?php

return [
    'modules' => [
        'Dashboard' => [
            'dashboard' => [
                'label' => 'Dashboard',
                'permissions' => [
                    'view' => 'View',
                ],
            ],
        ],
        'Employee Management' => [
            'employees' => [
                'label' => 'Employees',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'import' => 'Import',
                    'export' => 'Export',
                    'all'    => 'All',
                ],
            ],
            'attendance' => [
                'label' => 'Attendance',
                'permissions' => [
                    'list'   => 'List',
                    'mark'   => 'Mark',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'leave' => [
                'label' => 'Leave',
                'permissions' => [
                    'list'   => 'List',
                    'apply'  => 'Apply',
                    'approve'=> 'Approve',
                    'reject' => 'Reject',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'departments' => [
                'label' => 'Departments',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'designations' => [
                'label' => 'Designations',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
        ],
        'Payroll' => [
            'payroll_process' => [
                'label' => 'Payroll Process',
                'permissions' => [
                    'process' => 'Process',
                    'view'    => 'View',
                    'edit'    => 'Edit',
                    'delete'  => 'Delete',
                    'all'     => 'All',
                ],
            ],
            'salary_sheet' => [
                'label' => 'Salary Sheet',
                'permissions' => [
                    'view'   => 'View',
                    'export' => 'Export',
                    'all'    => 'All',
                ],
            ],
            'payslip' => [
                'label' => 'Payslip',
                'permissions' => [
                    'view'    => 'View',
                    'download'=> 'Download',
                    'print'   => 'Print',
                    'all'     => 'All',
                ],
            ],
        ],
        'Reports' => [
            'employee_report' => [
                'label' => 'Employee Report',
                'permissions' => [
                    'view'   => 'View',
                    'export' => 'Export',
                    'all'    => 'All',
                ],
            ],
            'attendance_report' => [
                'label' => 'Attendance Report',
                'permissions' => [
                    'view'   => 'View',
                    'export' => 'Export',
                    'all'    => 'All',
                ],
            ],
            'payroll_report' => [
                'label' => 'Payroll Report',
                'permissions' => [
                    'view'   => 'View',
                    'export' => 'Export',
                    'all'    => 'All',
                ],
            ],
        ],
        'Documents' => [
            'id_card' => [
                'label' => 'ID Card',
                'permissions' => [
                    'view'  => 'View',
                    'print' => 'Print',
                    'all'   => 'All',
                ],
            ],
            'pay_slip' => [
                'label' => 'Pay Slip',
                'permissions' => [
                    'view'  => 'View',
                    'print' => 'Print',
                    'all'   => 'All',
                ],
            ],
            'personal_info' => [
                'label' => 'Personal Info',
                'permissions' => [
                    'view'  => 'View',
                    'print' => 'Print',
                    'all'   => 'All',
                ],
            ],
            'appointment_letter' => [
                'label' => 'Appointment Letter',
                'permissions' => [
                    'view'  => 'View',
                    'print' => 'Print',
                    'all'   => 'All',
                ],
            ],
            'joining_letter' => [
                'label' => 'Joining Letter',
                'permissions' => [
                    'view'  => 'View',
                    'print' => 'Print',
                    'all'   => 'All',
                ],
            ],
            'increment_letter' => [
                'label' => 'Increment Letter',
                'permissions' => [
                    'view'  => 'View',
                    'print' => 'Print',
                    'all'   => 'All',
                ],
            ],
            'confirmation_letter' => [
                'label' => 'Confirmation Letter',
                'permissions' => [
                    'view'  => 'View',
                    'print' => 'Print',
                    'all'   => 'All',
                ],
            ],
        ],
        'System Users' => [
            'users' => [
                'label' => 'Users',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'roles' => [
                'label' => 'Roles & Permissions',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'delete' => 'Delete',
                    'assign' => 'Assign',
                    'all'    => 'All',
                ],
            ],
        ],
        'Settings' => [
            'general' => [
                'label' => 'General Settings',
                'permissions' => [
                    'view'   => 'View',
                    'edit'   => 'Edit',
                    'all'    => 'All',
                ],
            ],
            'mail' => [
                'label' => 'Mail Settings',
                'permissions' => [
                    'view'   => 'View',
                    'edit'   => 'Edit',
                    'all'    => 'All',
                ],
            ],
            'sms' => [
                'label' => 'SMS Settings',
                'permissions' => [
                    'view'    => 'View',
                    'create'  => 'Create',
                    'download'=> 'Download',
                    'all'     => 'All',
                ],
            ],
        ],
    ],
];
