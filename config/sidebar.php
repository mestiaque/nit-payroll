<?php

return [

    // Dashboard
    [
        'group_title' => '',
        [
            'title' => 'Dashboard',
            'icon' => 'fa fa-home',
            'route' => 'admin/dashboard',
            'permission' => '',
        ],
    ],


    // Employee
    [
        'group_title' => '',
        [
            'title' => 'Employee Management',
            'icon' => 'fa fa-user-circle',
            'permission' => '',
            'children' => [
                [
                    'title' => 'Employees',
                    'icon' => 'fa fa-user-circle',
                    'route' => 'admin/users/employee',
                    'permission' => 'users',
                ],
                [
                    'title' => 'Employee Report',
                    'icon' => 'fa fa-bar-chart',
                    'route' => 'admin/reports/employees',
                    'permission' => 'employee_report',
                ],
                [
                    'title' => 'Individual Report',
                    'icon' => 'fa fa-user',
                    'route' => 'admin/attendance/individual-report',
                    'permission' => 'attendance_report',
                ],
                [
                    'title' => 'Employee Performance',
                    'icon' => 'fa fa-chart-line',
                    'route' => 'admin/performance',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Probation',
                    'icon' => 'fa fa-user-clock',
                    'route' => 'admin/probations',
                    'permission' => 'general',
                ],
                [
                    'title' => 'Termination',
                    'icon' => 'fa fa-user-times',
                    'route' => 'admin/terminations',
                    'permission' => 'general',
                ],
                [
                    'title' => 'Retirement',
                    'icon' => 'fa fa-user-clock',
                    'route' => 'admin/retirement',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Interviews',
                    'icon' => 'fa fa-user-tie',
                    'route' => 'admin/interviews',
                    'permission' => 'general',
                ],
            ]
        ],
    ],

    // Employee Structure
    [
        'group_title' => '',
        [
            'title' => 'Employee Structure',
            'icon' => 'fa fa-sitemap',
            'permission' => '',
            'children' => [
                [
                    'title' => 'Employee Type',
                    'icon' => 'fa fa-id-badge',
                    'route' => 'admin/hr/employee-types',
                    'permission' => 'general',
                ],
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
            ]
        ],
    ],

    // Payroll
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
                [
                    'title' => 'Salary Summary',
                    'icon' => 'fa fa-file-invoice-dollar',
                    'route' => 'admin/payroll/salary-summary',
                    'permission' => 'payslip',
                ],
                [
                    'title' => 'Payroll Report',
                    'icon' => 'fa fa-bar-chart',
                    'route' => 'admin/reports/payroll',
                    'permission' => 'payroll_report',
                ],
                [
                    'title' => 'Salary Advance',
                    'icon' => 'fa fa-money-bill-wave',
                    'route' => 'admin/salary-advance',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Deductions',
                    'icon' => 'fa fa-minus-circle',
                    'route' => 'admin/deductions',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Bonus',
                    'icon' => 'fa fa-gift',
                    'route' => 'admin/bonus',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Tax',
                    'icon' => 'fa fa-percentage',
                    'route' => 'admin/tax',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Loan',
                    'icon' => 'fa fa-hand-holding-usd',
                    'route' => 'admin/loan',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Provident Fund',
                    'icon' => 'fa fa-piggy-bank',
                    'route' => 'admin/provident-fund',
                    'permission' => 'sms',
                ],
            ]
        ],
    ],

    // Attendance
    [
        'group_title' => '',
        [
            'title' => 'Attendance Management',
            'icon' => 'fa fa-calendar-minus',
            'permission' => '',
            'children' => [
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
                    'title' => 'Monthly Summary',
                    'icon' => 'fa fa-calendar',
                    'route' => 'admin/attendance/monthly-summary',
                    'permission' => 'attendance_report',
                ],
                [
                    'title' => 'Manual Attendance',
                    'icon' => 'fa fa-calendar-plus',
                    'route' => 'admin/attendance/manual',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Roaster Management',
                    'icon' => 'fa fa-clock',
                    'route' => 'admin/attendance/roaster',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Working Hours',
                    'icon' => 'fa fa-hourglass-half',
                    'route' => 'admin/working-hours',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Attendance Approval',
                    'icon' => 'fa fa-check-circle',
                    'route' => 'admin/attendance-approval',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Live Location',
                    'icon' => 'fa fa-map-marker',
                    'route' => 'admin/live-location-tracking',
                    'permission' => 'attendance_report',
                ],
                [
                    'title' => 'Overtime',
                    'icon' => 'fa fa-business-time',
                    'route' => 'admin/overtimes',
                    'permission' => 'sms',
                ],
            ]
        ],
    ],

    // Leave
    [
        'group_title' => '',
        [
            'title' => 'Leave Management',
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
                    'title' => 'Leave Types',
                    'icon' => 'fa fa-calendar-check',
                    'route' => 'admin/leaves/types',
                    'permission' => 'sms',
                ],
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
                [
                    'title' => 'Holiday',
                    'icon' => 'fa fa-calendar-day',
                    'route' => 'admin/holidays',
                    'permission' => 'general',
                ],
            ]
        ],
    ],



    // User Management
    [
        'group_title' => '',
        [
            'title' => 'User Management',
            'icon' => 'fa fa-user',
            'permission' => '',
            'children' => [
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
        ],
    ],

    // Documents
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
                [
                    'title' => 'Job Card',
                    'icon' => 'fa fa-id-card',
                    'route' => 'jobcard.index',
                    'permission' => 'attendance_report',
                ]
            ]
        ],
    ],

    // Letters
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
                ],
            ]
        ],
    ],

    // Assets
    [
        'group_title' => '',
        [
            'title' => 'Assets',
            'icon' => 'fa fa-laptop',
            'permission' => 'sms',
            'children' => [
                [
                    'title' => 'Asset List',
                    'icon' => 'fa fa-list',
                    'route' => 'admin/assetss',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Distribution',
                    'icon' => 'fa fa-share-alt',
                    'route' => 'admin/assets/distribution',
                    'permission' => 'sms',
                ],
            ]
        ]
    ],

    // Policies & Requests
    [
        'group_title' => '',
        [
            'title' => 'Policies & Requests',
            'icon' => 'fa fa-folder-open',
            'permission' => '',
            'children' => [
                [
                    'title' => 'Policy',
                    'icon' => 'fa fa-balance-scale',
                    'route' => 'admin/policy',
                    'permission' => 'sms',
                ],
                [
                    'title' => 'Convenience Request',
                    'icon' => 'fa fa-file-signature',
                    'route' => 'admin/convenience',
                    'permission' => 'sms',
                ]
            ]
        ]
    ],

    // Notice
    [
        'group_title' => '',
        [
            'title' => 'Notice Board',
            'icon' => 'fa fa-bullhorn',
            'permission' => '',
            'children' => [
                [
                    'title' => 'Notices',
                    'icon' => 'fa fa-bullhorn',
                    'route' => 'admin/notices',
                    'permission' => 'general',
                ],
            ]
        ]
    ],

    // System / Other
    [
        'group_title' => '',
        [
            'title' => 'System',
            'icon' => 'fa fa-cogs',
            'permission' => '',
            'children' => [
                [
                    'title' => 'ZKTeco Data Import',
                    'icon' => 'fa fa-file-import',
                    'route' => 'admin/zkteco-data-import',
                    'permission' => '',
                ]
            ]
        ]
    ],

    // Settings
    [
        'group_title' => '',
        [
            'title' => 'Settings',
            'icon' => 'fa-solid fa-cogs',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children' => [
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
                ]
            ]
        ]
    ],
];
