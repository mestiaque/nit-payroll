<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Attendance Print</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        .print-container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
        }
        
        /* Header Styles */
        .print-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        
        .company-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .company-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1a1a1a;
            text-transform: uppercase;
        }
        
        .company-address {
            font-size: 13px;
            color: #555;
            margin-top: 5px;
        }
        
        .company-contact {
            font-size: 12px;
            color: #555;
            margin-top: 3px;
        }
        
        /* Report Title */
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0 10px;
            text-transform: uppercase;
            color: #333;
        }
        
        .report-date {
            font-size: 13px;
            color: #555;
            margin-bottom: 15px;
        }
        
        /* Summary Boxes */
        .summary-boxes {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .summary-box {
            padding: 8px 20px;
            border-radius: 4px;
            text-align: center;
            min-width: 120px;
        }
        
        .summary-box.total {
            background: #e3f2fd;
            border: 1px solid #1976d2;
        }
        
        .summary-box.present {
            background: #e8f5e9;
            border: 1px solid #388e3c;
        }
        
        .summary-box.late {
            background: #fff8e1;
            border: 1px solid #f57c00;
        }
        
        .summary-box.absent {
            background: #ffebee;
            border: 1px solid #d32f2f;
        }
        
        .summary-box .label {
            font-size: 11px;
            text-transform: uppercase;
            color: #555;
            display: block;
        }
        
        .summary-box .value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .data-table th {
            background: #333;
            color: #fff;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            border: 1px solid #444;
        }
        
        .data-table td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        
        .data-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .data-table tr:hover {
            background: #f5f5f5;
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-present {
            background: #4caf50;
            color: #fff;
        }
        
        .status-late {
            background: #ff9800;
            color: #fff;
        }
        
        .status-absent {
            background: #f44336;
            color: #fff;
        }
        
        .status-holiday {
            background: #2196f3;
            color: #fff;
        }
        
        .status-leave {
            background: #9c27b0;
            color: #fff;
        }
        
        /* Footer */
        .print-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #666;
        }
        
        .footer-section {
            text-align: center;
        }
        
        .footer-line {
            width: 150px;
            border-top: 1px solid #333;
            margin-top: 30px;
            padding-top: 5px;
        }
        
        /* Print Styles */
        @media print {
            body {
                padding: 0;
            }
            
            .print-container {
                max-width: 100%;
            }
            
            .no-print {
                display: none !important;
            }
            
            .data-table th {
                background: #333 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .status-present {
                background: #4caf50 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .status-late {
                background: #ff9800 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .status-absent {
                background: #f44336 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .status-holiday {
                background: #2196f3 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .status-leave {
                background: #9c27b0 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .summary-box.total {
                background: #e3f2fd !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .summary-box.present {
                background: #e8f5e9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .summary-box.late {
                background: #fff8e1 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .summary-box.absent {
                background: #ffebee !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        
        /* Print Button */
        .print-btn-container {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .print-btn {
            background: #1976d2;
            color: #fff;
            border: none;
            padding: 10px 30px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
        }
        
        .print-btn:hover {
            background: #1565c0;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Print Button (hidden when printing) -->
        <div class="print-btn-container no-print">
            <button class="print-btn" onclick="window.print()">
                🖨️ Print Attendance
            </button>
        </div>
        
        <!-- Header -->
        <div class="print-header">
            <div class="company-info">
                @if($general && $general->logo())
                <img src="{{ asset($general->logo()) }}" alt="Logo" class="company-logo">
                @endif
                <div class="company-name">{{ $general->title ?? 'Company Name' }}</div>
            </div>
            @if($general)
            <div class="company-address">
                {{ $general->address_one ?? '' }}
            </div>
            <div class="company-contact">
                Phone: {{ $general->mobile ?? '' }} | Email: {{ $general->email ?? '' }}
            </div>
            @endif
        </div>
        
        <!-- Report Title -->
        <div class="report-title">Daily Attendance Report</div>
        <div class="report-date">
            Date: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} 
            @if($startDate != $endDate)
             - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            @endif
        </div>
        
        <!-- Summary -->
        <div class="summary-boxes">
            <div class="summary-box total">
                <span class="label">Total</span>
                <span class="value">{{ $total }}</span>
            </div>
            <div class="summary-box present">
                <span class="label">Present</span>
                <span class="value">{{ $present }}</span>
            </div>
            <div class="summary-box late">
                <span class="label">Late</span>
                <span class="value">{{ $late }}</span>
            </div>
            <div class="summary-box absent">
                <span class="label">Absent</span>
                <span class="value">{{ $absent }}</span>
            </div>
        </div>
        
        <!-- Data Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Employee Name</th>
                    <th>ID</th>
                    <th>Designation</th>
                    <th>Department</th>
                    <th>Type</th>
                    <th>In Time</th>
                    <th>Out Time</th>
                    <th>Work Hr.</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($finalData as $key => $row)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['employee_id'] ?? '--' }}</td>
                    <td>{{ $row['designation'] ?? '--' }}</td>
                    <td>{{ $row['department'] ?? '--' }}</td>
                    <td>{{ $row['employee_type'] ?? '--' }}</td>
                    <td>{{ $row['in_time'] }}</td>
                    <td>{{ $row['out_time'] }}</td>
                    <td>{{ $row['work_hr'] }}</td>
                    <td>
                        @if($row['status'] == 'Present')
                            <span class="status-badge status-present">Present</span>
                        @elseif($row['status'] == 'Late')
                            <span class="status-badge status-late">Late</span>
                        @elseif($row['status'] == 'Absent')
                            <span class="status-badge status-absent">Absent</span>
                        @elseif($row['status'] == 'Holiday')
                            <span class="status-badge status-holiday">Holiday</span>
                        @else
                            <span class="status-badge status-leave">{{ $row['status'] }}</span>
                        @endif
                    </td>
                    <td>{{ $row['date'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" style="text-align: center;">No attendance found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Footer -->
        <div class="print-footer">
            <div class="footer-section">
                <div class="footer-line">Prepared By</div>
            </div>
            <div class="footer-section">
                <div>Print Date: {{ date('d M Y, h:i A') }}</div>
            </div>
            <div class="footer-section">
                <div class="footer-line">Authorized By</div>
            </div>
        </div>
    </div>
</body>
</html>
