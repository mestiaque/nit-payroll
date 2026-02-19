# Employee Management System - Sidebar Menu Structure

This document outlines the complete sidebar menu structure for the Employee Management System.

## Menu Structure with Permissions

### 1. EMPLOYEE MANAGEMENT
**Permission Key:** `employee_management`

#### 1.1 Employee List
- **Route:** `admin.employees.index`
- **Permission:** `employee_list`
- **Icon:** `users`

#### 1.2 Add New Employee
- **Route:** `admin.employees.create`
- **Permission:** `employee_create`
- **Icon:** `user-plus`

#### 1.3 Active Employees
- **Route:** `admin.employees.index?employee_status=active`
- **Permission:** `employee_list`
- **Icon:** `check-circle`

#### 1.4 Inactive Employees
- **Route:** `admin.employees.index?employee_status=inactive`
- **Permission:** `employee_list`
- **Icon:** `x-circle`

#### 1.5 Retired Employees
- **Route:** `admin.reports.employees.retired`
- **Permission:** `employee_reports`
- **Icon:** `award`

---

### 2. EMPLOYEE PORTAL
**Permission Key:** `employee_portal`

#### 2.1 Dashboard
- **Route:** `admin.employee.portal.dashboard`
- **Permission:** `employee_portal_dashboard`
- **Icon:** `home`

#### 2.2 Daily Attendance
- **Route:** `admin.employee.portal.attendance`
- **Permission:** `employee_portal_attendance`
- **Icon:** `check-square`

#### 2.3 Online Attendance
- **Route:** `admin.employee.portal.online.attendance`
- **Permission:** `employee_portal_attendance`
- **Icon:** `map-pin`

#### 2.4 Personal Information
- **Route:** `admin.employee.portal.profile`
- **Permission:** `employee_portal_profile`
- **Icon:** `user`

#### 2.5 View Monthly Attendance
- **Route:** `admin.employee.portal.monthly.attendance`
- **Permission:** `employee_portal_attendance`
- **Icon:** `calendar`

---

### 3. ATTENDANCE MANAGEMENT
**Permission Key:** `attendance_management`

#### 3.1 Roaster Management
- **Route:** `admin.attendance.roaster.index`
- **Permission:** `roaster_manage`
- **Icon:** `clock`

##### 3.1.1 Create Roaster
- **Route:** `admin.attendance.roaster.create`
- **Permission:** `roaster_manage`

#### 3.2 Process Attendance
- **Route:** `admin.attendance.process`
- **Permission:** `attendance_process`
- **Icon:** `refresh-cw`

#### 3.3 Daily Attendance Report
**Sub-menu:**
- All Attendance - `admin.attendance.daily.report`
- Present - `admin.attendance.daily.report?status=present`
- Late - `admin.attendance.daily.report?status=late`
- Leave - `admin.attendance.daily.report?status=leave`
- Absent - `admin.attendance.daily.report?status=absent`
- Tour - `admin.attendance.daily.report?status=tour`
- Weekly Off - `admin.attendance.daily.report?status=weekly_off`
- Holiday Present - `admin.attendance.daily.report?status=holiday`
- **Permission:** `attendance_reports`
- **Icon:** `file-text`

#### 3.4 Last 7/10 Days Absent
- **Route:** `admin.attendance.absent.report`
- **Permission:** `attendance_reports`
- **Icon:** `alert-circle`

#### 3.5 Invalid Attendance
- **Route:** `admin.attendance.invalid.report`
- **Permission:** `attendance_reports`
- **Icon:** `alert-triangle`

#### 3.6 Attendance Summary
- **Route:** `admin.attendance.summary`
- **Permission:** `attendance_reports`
- **Icon:** `bar-chart`

#### 3.7 Monthly Attendance Report
- **Route:** `admin.attendance.monthly.report`
- **Permission:** `attendance_reports`
- **Icon:** `calendar`

---

### 4. LEAVE MANAGEMENT
**Permission Key:** `leave_management`

#### 4.1 Leave Applications
- **Route:** `admin.leaves.index`
- **Permission:** `leave_list`
- **Icon:** `calendar-x`

#### 4.2 Apply Leave
- **Route:** `admin.leaves.create`
- **Permission:** `leave_create`
- **Icon:** `plus-square`

#### 4.3 Leave Types
- **Route:** `admin.leaves.types`
- **Permission:** `leave_types`
- **Icon:** `list`

#### 4.4 Leave Balance
- **Route:** `admin.leaves.balance` (to be implemented if needed)
- **Permission:** `leave_balance`
- **Icon:** `pie-chart`

#### 4.5 Leave Reports
**Sub-menu:**
- Month-wise Leave List - `admin.reports.employees.leaveReport`
- Employee-wise Leave - `admin.reports.employees.leaveReport`
- Type-wise Leave - `admin.reports.employees.leaveReport`
- Department-wise Leave - `admin.reports.employees.departmentWise`
- **Permission:** `leave_reports`
- **Icon:** `file-text`

---

### 5. PAYROLL MANAGEMENT
**Permission Key:** `payroll_management`

#### 5.1 Payroll Dashboard
- **Route:** `admin.payroll.index`
- **Permission:** `payroll_view`
- **Icon:** `dollar-sign`

#### 5.2 Process Payroll
- **Route:** `admin.payroll.process`
- **Permission:** `payroll_process`
- **Icon:** `refresh-cw`

#### 5.3 Bank Information
- **Route:** `admin.employees.index` (with bank tab)
- **Permission:** `employee_bank_info`
- **Icon:** `credit-card`

#### 5.4 Daily Salary Sheet
- **Route:** `admin.payroll.dailySalarySheet`
- **Permission:** `payroll_view`
- **Icon:** `file`

#### 5.5 Monthly Salary Sheet
**Sub-menu:**
- All Employees - `admin.payroll.salarySheet`
- Bank Payment - `admin.payroll.salarySheet?payment_method=bank`
- Cash Payment - `admin.payroll.salarySheet?payment_method=cash`
- Active Employees - `admin.payroll.salarySheet?salary_type=regular`
- New Employees - `admin.payroll.salarySheet?salary_type=new_employee`
- Retired Employees - `admin.payroll.salarySheet?salary_type=retired_employee`
- **Permission:** `payroll_sheet`
- **Icon:** `file-text`

#### 5.6 Salary Summary
**Sub-menu:**
- Bank Summary - `admin.payroll.salarySummary?payment_method=bank`
- Cash Summary - `admin.payroll.salarySummary?payment_method=cash`
- Department Summary - `admin.payroll.salarySummary`
- **Permission:** `payroll_summary`
- **Icon:** `pie-chart`

#### 5.7 Pay Slip
- **Route:** `admin.payroll.paySlip`
- **Permission:** `payroll_payslip`
- **Icon:** `file`

#### 5.8 Held Up Salary
- **Route:** `admin.payroll.heldSalary`
- **Permission:** `payroll_held`
- **Icon:** `alert-circle`

---

### 6. EMPLOYEE REPORTS
**Permission Key:** `employee_reports`

#### 6.1 Gender-wise Reports
**Sub-menu:**
- Male Employees - `admin.reports.employees.genderWise?gender=male`
- Female Employees - `admin.reports.employees.genderWise?gender=female`
- **Permission:** `reports_gender`
- **Icon:** `users`

#### 6.2 Status-wise Reports
**Sub-menu:**
- Active Employees - `admin.reports.employees.statusWise?status=active`
- Inactive Employees - `admin.reports.employees.statusWise?status=inactive`
- **Permission:** `reports_status`
- **Icon:** `activity`

#### 6.3 Newly Joined Employees
- **Route:** `admin.reports.employees.newlyJoined`
- **Permission:** `reports_joined`
- **Icon:** `user-check`

#### 6.4 Retired Employees
- **Route:** `admin.reports.employees.retired`
- **Permission:** `reports_retired`
- **Icon:** `user-x`

#### 6.5 Month-wise Increment
- **Route:** `admin.reports.employees.increment`
- **Permission:** `reports_increment`
- **Icon:** `trending-up`

#### 6.6 Service Completed
**Sub-menu:**
- 6 Months Completed - `admin.reports.employees.serviceCompleted?duration=6`
- 1 Year Completed - `admin.reports.employees.serviceCompleted?duration=12`
- **Permission:** `reports_service`
- **Icon:** `check-circle`

#### 6.7 Bengali Employee List
- **Route:** `admin.reports.employees.bengaliList`
- **Permission:** `reports_bengali`
- **Icon:** `list`

#### 6.8 Department/Division Reports
- **Route:** `admin.reports.employees.departmentWise`
- **Permission:** `reports_department`
- **Icon:** `grid`

---

### 7. SYSTEM GENERATED DOCUMENTS
**Permission Key:** `system_documents`

#### 7.1 ID Cards
**Sub-menu:**
- Bengali ID Card - `admin.documents.idCard/{userId}/bengali`
- English ID Card - `admin.documents.idCard/{userId}/english`
- **Permission:** `document_id_card`
- **Icon:** `credit-card`

#### 7.2 Personal Information
- **Route:** `admin.documents.personalInfo/{userId}`
- **Permission:** `document_personal_info`
- **Icon:** `file-text`

#### 7.3 Job Application Form
- **Route:** `admin.documents.jobApplication/{userId}`
- **Permission:** `document_application`
- **Icon:** `file`

#### 7.4 Appointment Letter
- **Route:** `admin.documents.appointmentLetter/{userId}`
- **Permission:** `document_appointment`
- **Icon:** `file-text`

#### 7.5 Joining Letter
- **Route:** `admin.documents.joiningLetter/{userId}`
- **Permission:** `document_joining`
- **Icon:** `file-plus`

#### 7.6 Age Identification Letter
- **Route:** `admin.documents.ageIdentification/{userId}`
- **Permission:** `document_age`
- **Icon:** `file`

#### 7.7 Job Ledger
- **Route:** `admin.documents.jobLedger/{userId}`
- **Permission:** `document_ledger`
- **Icon:** `book`

#### 7.8 Increment Letter
- **Route:** `admin.documents.incrementLetter/{incrementId}`
- **Permission:** `document_increment`
- **Icon:** `arrow-up-circle`

#### 7.9 Nominee Form
- **Route:** `admin.documents.nomineeForm/{userId}`
- **Permission:** `document_nominee`
- **Icon:** `users`

#### 7.10 Service Confirmation
- **Route:** `admin.documents.confirmationLetter/{userId}`
- **Permission:** `document_confirmation`
- **Icon:** `check-square`

#### 7.11 Resign Letter
- **Route:** `admin.documents.resignLetter/{userId}`
- **Permission:** `document_resign`
- **Icon:** `file-minus`

#### 7.12 Commitment Letter
- **Route:** `admin.documents.commitmentLetter/{userId}`
- **Permission:** `document_commitment`
- **Icon:** `file-text`

#### 7.13 Settlement Letter
- **Route:** `admin.documents.settlementLetter/{userId}`
- **Permission:** `document_settlement`
- **Icon:** `file-check`

---

## Permission JSON Structure

Here's the JSON structure for permissions that should be added to the Permission model:

```json
{
  "employee_management": {
    "employee_list": true,
    "employee_create": true,
    "employee_edit": true,
    "employee_delete": true,
    "employee_view": true,
    "employee_bank_info": true,
    "employee_reports": true
  },
  "employee_portal": {
    "employee_portal_dashboard": true,
    "employee_portal_attendance": true,
    "employee_portal_profile": true
  },
  "attendance_management": {
    "roaster_manage": true,
    "attendance_process": true,
    "attendance_reports": true
  },
  "leave_management": {
    "leave_list": true,
    "leave_create": true,
    "leave_edit": true,
    "leave_delete": true,
    "leave_approve": true,
    "leave_types": true,
    "leave_balance": true,
    "leave_reports": true
  },
  "payroll_management": {
    "payroll_view": true,
    "payroll_process": true,
    "payroll_sheet": true,
    "payroll_summary": true,
    "payroll_payslip": true,
    "payroll_held": true,
    "payroll_modify": true
  },
  "employee_reports": {
    "reports_gender": true,
    "reports_status": true,
    "reports_joined": true,
    "reports_retired": true,
    "reports_increment": true,
    "reports_service": true,
    "reports_bengali": true,
    "reports_department": true
  },
  "system_documents": {
    "document_id_card": true,
    "document_personal_info": true,
    "document_application": true,
    "document_appointment": true,
    "document_joining": true,
    "document_age": true,
    "document_ledger": true,
    "document_increment": true,
    "document_nominee": true,
    "document_confirmation": true,
    "document_resign": true,
    "document_commitment": true,
    "document_settlement": true
  }
}
```

## Implementation Notes

1. **Sidebar Implementation:** Add these menu items to your admin sidebar blade template
2. **Permission Check:** Use middleware and blade directives like `@can()` or check permissions in controllers
3. **Icons:** Using Feather Icons or Font Awesome classes as mentioned
4. **Sub-menus:** Implement collapsible sub-menus for better organization
5. **Active State:** Highlight active menu based on current route

## Example Blade Sidebar Code Snippet

```blade
<!-- Employee Management -->
@if(checkPermission('employee_management'))
<li class="nav-item has-sub">
    <a href="#"><i data-feather="users"></i><span class="menu-title">Employee Management</span></a>
    <ul class="menu-content">
        @if(checkPermission('employee_list'))
        <li><a href="{{ route('admin.employees.index') }}"><i data-feather="circle"></i>Employee List</a></li>
        @endif
        
        @if(checkPermission('employee_create'))
        <li><a href="{{ route('admin.employees.create') }}"><i data-feather="circle"></i>Add New Employee</a></li>
        @endif
        
        <li><a href="{{ route('admin.employees.index', ['employee_status' => 'active']) }}"><i data-feather="circle"></i>Active Employees</a></li>
        <li><a href="{{ route('admin.employees.index', ['employee_status' => 'inactive']) }}"><i data-feather="circle"></i>Inactive Employees</a></li>
    </ul>
</li>
@endif

<!-- Add similar blocks for other modules -->
```
