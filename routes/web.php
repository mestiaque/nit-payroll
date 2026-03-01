<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Welcome\WelcomeController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\OffdayController;
use App\Http\Controllers\Admin\JobCardController;
use App\Http\Controllers\Admin\IdCardController;
use App\Http\Controllers\Admin\LettersController;
use App\Http\Controllers\Admin\EmployeePortalController;
use App\Http\Controllers\Admin\PayrollManagementController;
use App\Http\Controllers\Admin\AttendanceManagementController;
use App\Http\Controllers\Admin\EmployeeReportController;
use App\Http\Controllers\Api\ZKTecoPushController;

Route::post('/iclock/cdata', [ZKTecoPushController::class, 'receiveData']);

Route::get('/',[WelcomeController::class,'index'])->name('index');

Route::get('/image-view',[WelcomeController::class,'imageView'])->name('imageView');
Route::get('/image/{template?}/{image?}',[WelcomeController::class,'imageView2'])->name('imageView2');
Route::get('/sitemap.xml',[WelcomeController::class,'siteMapXml'])->name('siteMapXml');
Route::get('/search',[WelcomeController::class,'search'])->name('search');
Route::get('/switch/{lang?}',[WelcomeController::class,'language'])->name('language');
Route::get('/geo/filter/{id}',[WelcomeController::class,'geo_filter'])->name('geo_filter');

//Auth Route Start

Route::any('/login',[AuthController::class,'login'])->name('login');
Route::any('/forgot-password',[AuthController::class,'forgotPassword'])->name('forgotPassword');
Route::get('/reset-password/{token}',[AuthController::class,'resetPassword'])->name('resetPassword');
Route::post('/reset-password-check',[AuthController::class,'resetPasswordCheck'])->name('resetPasswordCheck');
Route::any('/register',[AuthController::class,'register'])->name('register');
Route::post('/log-out',[AuthController::class,'logout'])->name('logout');

Route::get('/{slug}',[WelcomeController::class,'pageView'])->name('pageView');

//Customer Route Group Start
Route::group(['prefix'=>'employee', 'as'=>'customer.','middleware'=>['auth','role:customer']], function(){

    Route::get('/dashboard',[CustomerController::class,'dashboard'])->name('dashboard');
    Route::get('/profile',[CustomerController::class,'myProfile'])->name('myProfile');
    Route::any('/edit-profile/{action?}',[CustomerController::class,'editProfile'])->name('editProfile');
    Route::get('/my-location-update',[CustomerController::class,'myLocationUpdate'])->name('myLocationUpdate');
    Route::get('/attendance',[CustomerController::class,'attendance'])->name('attendance');
    Route::get('/my-attendance', [CustomerController::class, 'myAttendance'])->name('myAttendance');

    Route::get('/leaves', [CustomerController::class, 'leaveIndex'])->name('leaves.index');
    Route::get('/leaves/create', [CustomerController::class, 'leaveCreate'])->name('leaves.create');
    Route::post('/leaves', [CustomerController::class, 'leaveStore'])->name('leaves.store');
    Route::get('/leaves/{id}/edit', [CustomerController::class, 'leaveEdit'])->name('leaves.edit');
    Route::put('/leaves/{id}', [CustomerController::class, 'leaveUpdate'])->name('leaves.update');
    Route::delete('/leaves/{id}', [CustomerController::class, 'leaveDestroy'])->name('leaves.destroy');


});


// Admin Route Group Start

Route::group(['prefix'=>'admin', 'as'=>'admin.','middleware'=>['auth','role:admin','permission']], function(){

Route::get('/dashboard',[AdminController::class,'dashboard'])->name('dashboard');
    Route::get('/employee-portal/dashboard', [EmployeePortalController::class, 'dashboard'])->name('employee.portal.dashboard');
    Route::get('/employee-portal/attendance', [EmployeePortalController::class, 'dailyAttendance'])->name('employee.portal.attendance');
    Route::post('/employee-portal/attendance', [EmployeePortalController::class, 'dailyAttendance'])->name('employee.portal.attendance.mark');
    Route::get('/employee-portal/online-attendance', [EmployeePortalController::class, 'onlineAttendance'])->name('employee.portal.online.attendance');
    Route::post('/employee-portal/online-attendance', [EmployeePortalController::class, 'onlineAttendance'])->name('employee.portal.online.attendance.mark');
    Route::get('/employee-portal/profile', [EmployeePortalController::class, 'myProfile'])->name('employee.portal.profile');
    Route::get('/employee-portal/monthly-attendance', [EmployeePortalController::class, 'monthlyAttendance'])->name('employee.portal.monthly.attendance');
    Route::get('/my-location-update',[AdminController::class,'myLocationUpdate'])->name('myLocationUpdate');
    Route::get('/my-profile',[AdminController::class,'myProfile'])->name('myProfile');
    Route::any('/edit-profile',[AdminController::class,'editProfile'])->name('editProfile');

    Route::any('/reminders',[AdminController::class,'reminders'])->name('reminders');

    // Medies Library Route
    Route::get('/medies',[AdminController::class,'medies'])->name('medies');
    Route::post('/medies/create',[AdminController::class,'mediesCreate'])->name('mediesCreate');
    Route::match(['get','post'],'/medies/edit/{id}',[AdminController::class,'mediesEdit'])->name('mediesEdit');
    Route::get('/medies/delete/{id}',[AdminController::class,'mediesDelete'])->name('mediesDelete');
    // Medies Library Route End

    Route::get('/leaves/manual-create', [LeaveController::class, 'manualCreate'])->name('leaves.manual.create');
    Route::post('/leaves/manual-create', [LeaveController::class, 'manualStore'])->name('leaves.manual.store');


Route::get('/daily-attendance',[AdminController::class,'dailyAttendance'])->name('dailyAttendance');
Route::get('/daily-attendance-print',[AdminController::class,'dailyAttendancePrint'])->name('dailyAttendancePrint');
Route::any('/daily-attendance/{action}/{id?}',[AdminController::class,'dailyAttendanceAction'])->name('dailyAttendanceAction');
Route::get('daily-attendance-department-wise', [AdminController::class, 'dailyAttendanceDepartmentWise'])->name('dailyAttendanceDepartmentWise');
Route::get('daily-attendance-department-summary', [AdminController::class,'dailyAttendanceDepartmentSummary'])->name('dailyAttendanceDepartmentSummary');

Route::get('/zkteco-data-import',[ZKTecoPushController::class,'import'])->name('importZkteco');
Route::post('/import-zkteco-data',[ZKTecoPushController::class,'importAction'])->name('importZktecoAction');
Route::get('salary-report', [AdminController::class, 'gradeWiseSalaryReport'])->name('salaryReport');
Route::get('/hr/departments',[AdminController::class,'departments'])->name('departments');
Route::any('/hr/departments/{action}/{id?}',[AdminController::class,'departmentsAction'])->name('departmentsAction');
Route::get('/hr/employee-types',[AdminController::class,'employeeType'])->name('employeeType');
Route::any('/hr/employee-types/{action}/{id?}',[AdminController::class,'employeeTypeAction'])->name('employeeTypeAction');
Route::get('/hr/designations',[AdminController::class,'designations'])->name('designations');
Route::any('/hr/designations/{action}/{id?}',[AdminController::class,'designationsAction'])->name('designationsAction');
Route::get('/hr/divisions',[AdminController::class,'divisions'])->name('divisions');
Route::any('/hr/divisions/{action}/{id?}',[AdminController::class,'divisionsAction'])->name('divisionsAction');
Route::get('/hr/grades',[AdminController::class,'grades'])->name('grades');
Route::any('/hr/grades/{action}/{id?}',[AdminController::class,'gradesAction'])->name('gradesAction');
Route::get('/hr/line-numbers',[AdminController::class,'line_numbers'])->name('line_numbers');
Route::any('/hr/line-numbers/{action}/{id?}',[AdminController::class,'line_numbersAction'])->name('line_numbersAction');
Route::get('/hr/sections',[AdminController::class,'sections'])->name('sections');
Route::any('/hr/sections/{action}/{id?}',[AdminController::class,'sectionsAction'])->name('sectionsAction');
Route::get('/hr/shifts',[AdminController::class,'shifts'])->name('shifts');
Route::any('/hr/shifts/{action}/{id?}',[AdminController::class,'shiftsAction'])->name('shiftsAction');

Route::get('/users/admin/',[AdminController::class,'usersAdmin'])->name('usersAdmin');
Route::any('/users/admin/{action}/{id?}',[AdminController::class,'usersAdminAction'])->name('usersAdminAction');

// Admin Creation Routes
Route::get('/users/admin/create', [AdminController::class, 'createAdmin'])->name('users.admin.create');
Route::post('/users/admin/create', [AdminController::class, 'storeAdmin'])->name('users.admin.store');

// Super Admin Creation Routes
Route::get('/users/super-admin/create', [AdminController::class, 'createSuperAdmin'])->name('users.superadmin.create');
Route::post('/users/super-admin/create', [AdminController::class, 'storeSuperAdmin'])->name('users.superadmin.store');

Route::get('/users/employee/',[AdminController::class,'usersCustomer'])->name('usersCustomer');
Route::any('/users/employee/{action}/{id?}',[AdminController::class,'usersCustomerAction'])->name('usersCustomerAction');
Route::get('/suppliers',[AdminController::class,'usersSuppliers'])->name('usersSuppliers');
Route::any('/suppliers/{action}/{id?}',[AdminController::class,'usersSuppliersAction'])->name('usersSuppliersAction');
Route::get('/users/roles',[AdminController::class,'userRoles'])->name('userRoles');
Route::any('/users/roles/{action}/{id?}',[AdminController::class,'userRoleAction'])->name('userRoleAction');
Route::get('/subscribes',[AdminController::class,'subscribes'])->name('subscribes');

// Leave Management
Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
Route::get('/leaves/{id}/edit', [LeaveController::class, 'edit'])->name('leaves.edit');
Route::put('/leaves/{id}', [LeaveController::class, 'update'])->name('leaves.update');
Route::delete('/leaves/{id}', [LeaveController::class, 'destroy'])->name('leaves.destroy');

// Leave Types
Route::get('/leaves/types', [LeaveController::class, 'types'])->name('leaves.types');
Route::post('/leaves/types', [LeaveController::class, 'typesStore'])->name('leaves.types.store');
Route::put('/leaves/types/{id}', [LeaveController::class, 'typesUpdate'])->name('leaves.types.update');
Route::delete('/leaves/types/{id}', [LeaveController::class, 'typesDestroy'])->name('leaves.types.destroy');

// Holiday Management
Route::get('/holidays', [HolidayController::class, 'index'])->name('holiday.index');
Route::get('/holidays/create', [HolidayController::class, 'create'])->name('holiday.create');
Route::post('/holidays', [HolidayController::class, 'store'])->name('holiday.store');
Route::get('/holidays/{id}/edit', [HolidayController::class, 'edit'])->name('holiday.edit');
Route::put('/holidays/{id}', [HolidayController::class, 'update'])->name('holiday.update');
Route::delete('/holidays/{id}', [HolidayController::class, 'destroy'])->name('holiday.destroy');

// Offday Management
Route::get('/offday', [OffdayController::class, 'index'])->name('offday.index');
Route::put('/offday', [OffdayController::class, 'update'])->name('offday.update');

// Job Card Management
Route::get('/jobcard', [JobCardController::class, 'index'])->name('jobcard.index');

// ID Card Management
Route::get('/idcard', [IdCardController::class, 'index'])->name('idcard.index');
Route::get('/idcard/print', [IdCardController::class, 'print'])->name('idcard.print');

// Attendance Management - Roaster
Route::get('/attendance/roaster', [AttendanceManagementController::class, 'roasterIndex'])->name('attendance.roaster.index');
Route::get('/attendance/roaster/create', [AttendanceManagementController::class, 'roasterCreate'])->name('attendance.roaster.create');
Route::post('/attendance/roaster', [AttendanceManagementController::class, 'roasterStore'])->name('attendance.roaster.store');
Route::put('/attendance/roaster/{id}', [AttendanceManagementController::class, 'roasterUpdate'])->name('attendance.roaster.update');
Route::delete('/attendance/roaster/{id}', [AttendanceManagementController::class, 'roasterDestroy'])->name('attendance.roaster.destroy');
Route::post('/attendance/roaster/bulk-update', [AttendanceManagementController::class, 'roasterBulkUpdate'])->name('attendance.roaster.bulkUpdate');


// Manual Attendance Management (Admin)
Route::get('/attendance/manual', [AttendanceManagementController::class, 'manualIndex'])->name('attendance.manual.index');
Route::get('/attendance/manual/create', [AttendanceManagementController::class, 'manualCreate'])->name('attendance.manual.create');
Route::post('/attendance/manual', [AttendanceManagementController::class, 'manualStore'])->name('attendance.manual.store');
Route::get('/attendance/manual/{id}/edit', [AttendanceManagementController::class, 'manualEdit'])->name('attendance.manual.edit');
Route::put('/attendance/manual/{id}', [AttendanceManagementController::class, 'manualUpdate'])->name('attendance.manual.update');
Route::delete('/attendance/manual/{id}', [AttendanceManagementController::class, 'manualDestroy'])->name('attendance.manual.destroy');
// Attendance Management - Processing
Route::post('/attendance/process', [AttendanceManagementController::class, 'processAttendance'])->name('attendance.process');

// Attendance Management - Reports
Route::get('/attendance/daily-report', [AdminController::class, 'dailyAttendance'])->name('attendance.daily.report');
Route::get('/attendance/summary', [AttendanceManagementController::class, 'attendanceSummary'])->name('attendance.summary');
Route::get('/attendance/monthly-report', [AttendanceManagementController::class, 'monthlyAttendanceReport'])->name('attendance.monthly.report');
Route::get('/attendance/absent-report', [AttendanceManagementController::class, 'absentReport'])->name('attendance.absent.report');
Route::get('/attendance/invalid-report', [AttendanceManagementController::class, 'invalidAttendanceReport'])->name('attendance.invalid.report');

// Payroll Management
Route::get('/payroll', [PayrollManagementController::class, 'index'])->name('payroll.index');
Route::post('/payroll/process', [PayrollManagementController::class, 'processSalary'])->name('payroll.process');
Route::get('/payroll/process', [PayrollManagementController::class, 'processSalary'])->name('payroll.processGet');
Route::get('/payroll/salary-sheet', [PayrollManagementController::class, 'salarySheet'])->name('payroll.salarySheet');
Route::get('/payroll/salary-summary', [PayrollManagementController::class, 'salarySummary'])->name('payroll.salarySummary');
Route::get('/payroll/daily-salary-sheet', [PayrollManagementController::class, 'dailySalarySheet'])->name('payroll.dailySalarySheet');
Route::get('/payroll/pay-slip/{id}', [PayrollManagementController::class, 'paySlip'])->name('payroll.paySlip');
Route::post('/payroll/bulk-pay-slip', [PayrollManagementController::class, 'bulkPaySlip'])->name('payroll.bulkPaySlip');
Route::post('/payroll/bulk-mark-paid', [PayrollManagementController::class, 'bulkMarkPaid'])->name('payroll.bulkMarkPaid');
Route::post('/payroll/{id}/mark-paid', [PayrollManagementController::class, 'markPaid'])->name('payroll.markPaid');
Route::put('/payroll/{id}/update', [PayrollManagementController::class, 'updateSalary'])->name('payroll.updateSalary');
Route::get('/payroll/export', [PayrollManagementController::class, 'exportSalarySheet'])->name('payroll.export');
Route::get('/payroll/salary-sheet/export', [PayrollManagementController::class, 'salarySheetExport'])->name('payroll.salarySheetExport');

// Payroll Reports
Route::get('/reports/payroll', [PayrollManagementController::class, 'payrollReport'])->name('reports.payroll');

// Leave Report
Route::get('/leaves/report', [EmployeeReportController::class, 'leaveReport'])->name('leaves.report');

// Leave Summary
Route::get('/leaves/summary', [LeaveController::class, 'summary'])->name('leaves.summary');

// Employee Reports
Route::get('/reports/employees', [EmployeeReportController::class, 'index'])->name('reports.employees.index');
Route::get('/reports/employees/gender-wise', [EmployeeReportController::class, 'genderWiseReport'])->name('reports.employees.genderWise');
Route::get('/reports/employees/status-wise', [EmployeeReportController::class, 'statusWiseReport'])->name('reports.employees.statusWise');
Route::get('/reports/employees/newly-joined', [EmployeeReportController::class, 'newlyJoinedReport'])->name('reports.employees.newlyJoined');
Route::get('/reports/employees/retired', [EmployeeReportController::class, 'retiredReport'])->name('reports.employees.retired');
Route::get('/reports/employees/increment', [EmployeeReportController::class, 'monthWiseIncrementReport'])->name('reports.employees.increment');
Route::get('/reports/employees/service-completed', [EmployeeReportController::class, 'serviceCompletedReport'])->name('reports.employees.serviceCompleted');
Route::get('/reports/employees/bengali-list', [EmployeeReportController::class, 'bengaliEmployeeList'])->name('reports.employees.bengaliList');
Route::get('/reports/employees/leave-report', [EmployeeReportController::class, 'leaveReport'])->name('reports.employees.leaveReport');
Route::get('/reports/employees/department-wise', [EmployeeReportController::class, 'departmentWiseReport'])->name('reports.employees.departmentWise');

// Employee Documents/Letters Generation (userId is optional - shows selection form first)
Route::get('/documents/id-card', [EmployeeReportController::class, 'idCard'])->name('documents.idCard');
Route::get('/documents/personal-info', [EmployeeReportController::class, 'personalInfoSheet'])->name('documents.personalInfo');
Route::get('/documents/appointment-letter', [EmployeeReportController::class, 'appointmentLetter'])->name('documents.appointmentLetter');
Route::get('/documents/joining-letter', [EmployeeReportController::class, 'joiningLetter'])->name('documents.joiningLetter');
Route::get('/documents/increment-letter', [EmployeeReportController::class, 'incrementLetter'])->name('documents.incrementLetter');
Route::get('/documents/confirmation-letter', [EmployeeReportController::class, 'confirmationLetter'])->name('documents.confirmationLetter');
Route::get('/documents/pay-slip', [EmployeeReportController::class, 'paySlip'])->name('documents.paySlip');
Route::get('/documents/age-identification', [EmployeeReportController::class, 'ageIdentificationLetter'])->name('documents.ageIdentification');
Route::get('/documents/job-ledger', [EmployeeReportController::class, 'jobLedger'])->name('documents.jobLedger');
Route::get('/documents/nominee-form', [EmployeeReportController::class, 'nomineeForm'])->name('documents.nomineeForm');
Route::get('/documents/resign-letter', [EmployeeReportController::class, 'resignLetter'])->name('documents.resignLetter');
Route::get('/documents/commitment-letter', [EmployeeReportController::class, 'commitmentLetter'])->name('documents.commitmentLetter');
Route::get('/documents/settlement-letter', [EmployeeReportController::class, 'settlementLetter'])->name('documents.settlementLetter');
Route::get('/documents/job-application', [EmployeeReportController::class, 'jobApplicationForm'])->name('documents.jobApplication');

// Letter Management - Database Stored
Route::get('/letters/appointment', [LettersController::class, 'appointmentIndex'])->name('letters.appointment.index');
Route::get('/letters/appointment/create', [LettersController::class, 'appointmentCreate'])->name('letters.appointment.create');
Route::post('/letters/appointment', [LettersController::class, 'appointmentStore'])->name('letters.appointment.store');
Route::get('/letters/appointment/{id}/edit', [LettersController::class, 'appointmentEdit'])->name('letters.appointment.edit');
Route::put('/letters/appointment/{id}', [LettersController::class, 'appointmentUpdate'])->name('letters.appointment.update');
Route::get('/letters/appointment/{id}', [LettersController::class, 'appointmentShow'])->name('letters.appointment.show');
Route::get('/letters/appointment/{id}/print', [LettersController::class, 'appointmentPrint'])->name('letters.appointment.print');
Route::delete('/letters/appointment/{id}', [LettersController::class, 'appointmentDestroy'])->name('letters.appointment.destroy');

Route::get('/letters/joining', [LettersController::class, 'joiningIndex'])->name('letters.joining.index');
Route::get('/letters/joining/create', [LettersController::class, 'joiningCreate'])->name('letters.joining.create');
Route::post('/letters/joining', [LettersController::class, 'joiningStore'])->name('letters.joining.store');
Route::get('/letters/joining/{id}/edit', [LettersController::class, 'joiningEdit'])->name('letters.joining.edit');
Route::put('/letters/joining/{id}', [LettersController::class, 'joiningUpdate'])->name('letters.joining.update');
Route::get('/letters/joining/{id}', [LettersController::class, 'joiningShow'])->name('letters.joining.show');
Route::get('/letters/joining/{id}/print', [LettersController::class, 'joiningPrint'])->name('letters.joining.print');
Route::delete('/letters/joining/{id}', [LettersController::class, 'joiningDestroy'])->name('letters.joining.destroy');

Route::get('/letters/confirmation', [LettersController::class, 'confirmationIndex'])->name('letters.confirmation.index');
Route::get('/letters/confirmation/create', [LettersController::class, 'confirmationCreate'])->name('letters.confirmation.create');
Route::post('/letters/confirmation', [LettersController::class, 'confirmationStore'])->name('letters.confirmation.store');
Route::get('/letters/confirmation/{id}/edit', [LettersController::class, 'confirmationEdit'])->name('letters.confirmation.edit');
Route::put('/letters/confirmation/{id}', [LettersController::class, 'confirmationUpdate'])->name('letters.confirmation.update');
Route::get('/letters/confirmation/{id}', [LettersController::class, 'confirmationShow'])->name('letters.confirmation.show');
Route::get('/letters/confirmation/{id}/print', [LettersController::class, 'confirmationPrint'])->name('letters.confirmation.print');
Route::delete('/letters/confirmation/{id}', [LettersController::class, 'confirmationDestroy'])->name('letters.confirmation.destroy');

Route::get('/letters/increment', [LettersController::class, 'incrementIndex'])->name('letters.increment.index');
Route::get('/letters/increment/create', [LettersController::class, 'incrementCreate'])->name('letters.increment.create');
Route::post('/letters/increment', [LettersController::class, 'incrementStore'])->name('letters.increment.store');
Route::get('/letters/increment/{id}/edit', [LettersController::class, 'incrementEdit'])->name('letters.increment.edit');
Route::put('/letters/increment/{id}', [LettersController::class, 'incrementUpdate'])->name('letters.increment.update');
Route::get('/letters/increment/{id}', [LettersController::class, 'incrementShow'])->name('letters.increment.show');
Route::get('/letters/increment/{id}/print', [LettersController::class, 'incrementPrint'])->name('letters.increment.print');
Route::delete('/letters/increment/{id}', [LettersController::class, 'incrementDestroy'])->name('letters.increment.destroy');

// ===========================================
// END EMPLOYEE MANAGEMENT SYSTEM ROUTES
// ===========================================

// Apps Setting
Route::get('/setting/{type}',[AdminController::class,'setting'])->name('setting');
Route::post('/setting/{type}/update',[AdminController::class,'settingUpdate'])->name('settingUpdate');

Route::get('download/zk-installer', function () { $path = resource_path('apps/ZKTimeSyncInstaller.exe'); return response()->download($path); });

});
