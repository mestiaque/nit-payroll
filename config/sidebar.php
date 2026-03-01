<?php

return [
    [
        'group_title' => '',
        [
            'title' => 'Dashboard',
            'icon' => 'fa fa-home',
            'route' => 'admin/dashboard',
            'permission' => '',
        ],
    ],
    //payroll
    [
        'group_title' => '',
        [
            'title' => 'Payroll Management',
            'icon' => 'fa fa-users',
            'permission' => '',
            'children' => [
                [
                    'title' => 'Salary Sheet',
                    'icon' => 'fa fa-file-invoice-dollar',
                    'route' => 'admin/payroll/salary-sheet',
                    'permission' => 'salary_sheet',
                ],
                // [
                //     'title' => 'Payroll',
                //     'icon' => 'fa fa-wallet',
                //     'route' => 'admin/payroll',
                //     'permission' => 'payslip',
                // ],
                // [
                //     'title' => 'Daily Salary Sheet',
                //     'icon' => 'fa fa-calendar-day',
                //     'route' => 'admin/payroll/daily-salary-sheet',
                //     'permission' => 'payslip',
                // ],
                [
                    'title' => 'Salary Summary',
                    'icon' => 'fa fa-file-invoice-dollar',
                    'route' => 'admin/payroll/salary-summary',
                    'permission' => 'payslip',
                ],
                // [
                //     'title' => 'Held Up Salary',
                //     'icon' => 'fa fa-file-invoice-dollar',
                //     'route' => 'admin/payroll/index',
                //     'permission' => 'payslip',
                // ],
            ]
        ],
    ],
    //Attendance
    [
        'group_title' => '',
        [
            'title' => 'Attendance Management',
            'icon' => 'fa fa-calendar-minus',
            'permission' => '',
            'children' => [
                [
                    'title' => 'Leave Applications',
                    'icon' => 'fa fa-calendar-minus',
                    'route' => 'admin/leaves',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Roaster Management',
                    'icon' => 'fa fa-clock',
                    'route' => 'admin/attendance/roaster',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Manual Attendance',
                    'icon' => 'fa fa-calendar-plus',
                    'route' => 'admin/attendance/manual',
                    'permission' => 'sms',
                ],
            ]
        ],
    ],
    //reports
    [
        'group_title' => '',
        [
            'title' => 'Reports',
            'icon' => 'fa fa-bar-chart',
            'permission' => '',
            'children' => [
                [
                    'title' => 'Employee Report',
                    'icon' => 'fa fa-bar-chart',
                    'route' => 'admin/reports/employees',
                    'permission' => 'employee_report',
                ],
                [
                    'title' => 'Attendance',
                    'icon' => 'fa fa-bar-chart',
                    'route' => 'admin/daily-attendance',
                    'permission' => 'attendance_report',
                ],
                [
                    'title' => 'Department Attendance',
                    'icon' => 'fa fa-bar-chart',
                    'route' => 'admin/daily-attendance-department-summary',
                    'permission' => 'attendance_report',
                ],
                [
                    'title' => 'Payroll Report',
                    'icon' => 'fa fa-bar-chart',
                    'route' => 'admin/reports/payroll',
                    'permission' => 'payroll_report',
                ],
                // [
                //     'title' => 'Gender-wise Report',
                //     'icon' => 'fa fa-bar-chart',
                //     'route' => 'admin/reports/employees/gender-wise',
                //     'permission' => 'payroll_report',
                // ],
                // [
                //     'title' => 'Status-wise Report',
                //     'icon' => 'fa fa-bar-chart',
                //     'route' => 'admin/reports/employees/status-wise',
                //     'permission' => 'payroll_report',
                // ],
                // [
                //     'title' => 'Newly Joined Employees',
                //     'icon' => 'fa fa-bar-chart',
                //     'route' => 'admin/reports/employees/newly-joined',
                //     'permission' => 'payroll_report',
                // ],
                // [
                //     'title' => 'Retired Employees',
                //     'icon' => 'fa fa-bar-chart',
                //     'route' => 'admin/reports/employees/retired',
                //     'permission' => 'payroll_report',
                // ],
                // [
                //     'title' => 'Month-wise Increment',
                //     'icon' => 'fa fa-bar-chart',
                //     'route' => 'admin/reports/employees/increment',
                //     'permission' => 'payroll_report',
                // ],
                // [
                //     'title' => 'Service Completed',
                //     'icon' => 'fa fa-bar-chart',
                //     'route' => 'admin/reports/employees/service-completed',
                //     'permission' => 'payroll_report',
                // ],
                // [
                //     'title' => 'Bengali Employee List',
                //     'icon' => 'fa fa-bar-chart',
                //     'route' => 'admin/reports/employees/bengali-list',
                //     'permission' => 'payroll_report',
                // ],
                // [
                //     'title' => 'Department/Division Reports',
                //     'icon' => 'fa fa-bar-chart',
                //     'route' => 'admin/reports/employees/department-wise',
                //     'permission' => 'payroll_report',
                // ],
                [
                    'title' => 'Leave Report',
                    'icon' => 'fa fa-bar-chart',
                    'route' => 'admin/leaves/report',
                    'permission' => 'payroll_report',
                ],
                [
                    'title' => 'Leave Summary',
                    'icon' => 'fa fa-bar-chart',
                    'route' => 'admin/leaves/summary',
                    'permission' => 'payroll_report',
                ],
                // [
                //     'title' => 'Absent Report',
                //     'icon' => 'fa fa-user-times',
                //     'route' => 'admin/attendance/absent-report',
                //     'permission' => 'sms',
                // ],
                // [
                //     'title' => 'Invalid Attendance',
                //     'icon' => 'fa fa-exclamation-circle',
                //     'route' => 'admin/attendance/invalid-report',
                //     'permission' => 'sms',
                // ],
                // [
                //     'title' => 'Attendance Summary',
                //     'icon' => 'fa fa-bar-chart',
                //     'route' => 'admin/attendance/summary',
                //     'permission' => 'sms',
                // ],
            ]
        ]

    ],
    //document management
    [
        'group_title' => '',
        [
            'title' => 'Document Management',
            'icon' => 'fa fa-folder',
            'permission' => '',
            'children' => [
                [
                    'title' => 'ID Card',
                    'icon' => 'fa fa-id-card',
                    'route' => 'admin/idcard',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Pay Slip',
                    'icon' => 'fa fa-receipt',
                    'route' => 'admin/documents/pay-slip',
                    'permission' => 'pay_slip',
                ],
                [
                    'title' => 'Personal Info',
                    'icon' => 'fa fa-user',
                    'route' => 'admin/documents/personal-info',
                    'permission' => 'personal_info',
                ],
                // [
                //     'title' => 'Appointment Letter',
                //     'icon' => 'fa fa-envelope',
                //     'route' => 'admin/documents/appointment-letter',
                //     'permission' => 'appointment_letter',
                // ],
                // [
                //     'title' => 'Joining Letter',
                //     'icon' => 'fa fa-sign-in',
                //     'route' => 'admin/documents/joining-letter',
                //     'permission' => 'joining_letter',
                // ],
                // [
                //     'title' => 'Increment Letter',
                //     'icon' => 'fa fa-arrow-up',
                //     'route' => 'admin/documents/increment-letter',
                //     'permission' => 'increment_letter',
                // ],
                // [
                //     'title' => 'Confirmation Letter',
                //     'icon' => 'fa fa-check-circle',
                //     'route' => 'admin/documents/confirmation-letter',
                //     'permission' => 'confirmation_letter',
                // ],
                [
                    'title' => 'Job Card',
                    'icon' => 'fa fa-id-card',
                    'route' => 'admin/jobcard',
                    'permission' => 'sms',
                ]
            ]
        ]
    ],
    // Letters Management
    [
        'group_title' => '',
        [
            'title' => 'Letters Management',
            'icon' => 'fa fa-envelope-open',
            'permission' => '',
            'children' => [
                [
                    'title' => 'Appointment Letters',
                    'icon' => 'fa fa-envelope',
                    'route' => 'admin/letters/appointment',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Joining Letters',
                    'icon' => 'fa fa-sign-in',
                    'route' => 'admin/letters/joining',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Confirmation Letters',
                    'icon' => 'fa fa-check-circle',
                    'route' => 'admin/letters/confirmation',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Salary Increments',
                    'icon' => 'fa fa-arrow-up',
                    'route' => 'admin/letters/increment',
                    'permission' => 'sms',
                ]
            ]
        ]
    ],
    //user management
    [
        'group_title' => '',
        [
            'title' => 'User Management',
            'icon' => 'fa fa-user',
            'permission' => '',
            'children' => [
                [
                    'title' => 'Employees',
                    'icon' => 'fa fa-user-circle',
                    'route' => 'admin/users/employee',
                    'permission' => 'users',
                ],
                [
                    'title' => 'Admins',
                    'icon' => 'fa fa-user-shield',
                    'route' => 'admin/users/admin',
                    'permission' => 'users',
                ],
                [
                    'title' => 'Roles',
                    'icon' => 'fa fa-lock',
                    'route' => 'admin/users/roles',
                    'permission' => 'roles',
                ],
            ]
        ]

    ],
    //hr setup
    [
        'group_title' => '',
        [
            'title' => 'HR Setup',
            'icon' => 'fa fa-user',
            'permission' => '',
            'children' => [
                [
                    'title' => 'Department',
                    'icon' => 'fa fa-building',
                    'route' => 'admin/hr/departments',
                    'permission' => 'general',
                ],
                [
                    'title' => 'Designation',
                    'icon' => 'fa fa-briefcase',
                    'route' => 'admin/hr/designations',
                    'permission' => 'general',
                ],
                [
                    'title' => 'Division',
                    'icon' => 'fa fa-sitemap',
                    'route' => 'admin/hr/divisions',
                    'permission' => 'general',
                ],
                [
                    'title' => 'Grade',
                    'icon' => 'fa fa-graduation-cap',
                    'route' => 'admin/hr/grades',
                    'permission' => 'general',
                ],
                [
                    'title' => 'Line Number',
                    'icon' => 'fa fa-list-ol',
                    'route' => 'admin/hr/line-numbers',
                    'permission' => 'general',
                ],
                [
                    'title' => 'Section',
                    'icon' => 'fa fa-th-large',
                    'route' => 'admin/hr/sections',
                    'permission' => 'general',
                ],
                [
                    'title' => 'Shift',
                    'icon' => 'fa fa-clock',
                    'route' => 'admin/hr/shifts',
                    'permission' => 'general',
                ],
                [
                    'title' => 'Employee Type',
                    'icon' => 'fa fa-id-badge',
                    'route' => 'admin/hr/employee-types',
                    'permission' => 'general',
                ],
                [
                    'title' => 'Leave Types',
                    'icon' => 'fa fa-calendar-check',
                    'route' => 'admin/leaves/types',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Holiday',
                    'icon' => 'fa fa-calendar-day',
                    'route' => 'admin/holidays',
                    'permission' => 'general',
                ]
            ]
        ]

    ],
    //settings
    [
        'group_title' => '',
        [
            'title' => 'General Settings',
            'icon'       => 'fa-solid fa-cogs',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                [
                    'title' => 'General Settings',
                    'icon' => 'fa fa-cog',
                    'route' => 'admin/setting/general',
                    'permission' => 'general',
                ],
                [
                    'title' => 'Mail Settings',
                    'icon' => 'fa fa-envelope',
                    'route' => 'admin/setting/mail',
                    'permission' => 'mail',
                ],
                [
                    'title' => 'SMS Settings',
                    'icon' => 'fa fa-cloud-upload',
                    'route' => 'admin/setting/sms',
                    'permission' => 'sms',
                ],
            ]
        ]
    ],
    //other
    [
        'group_title' => 'Other',
        [
            'title' => 'ZKTeco Data Import',
            'icon' => 'fa fa-file-import',
            'route' => 'admin/zkteco-data-import',
            'permission' => '',
        ],
    ],
];



