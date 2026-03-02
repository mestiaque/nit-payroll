<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            width: 100%;
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background: white;
        }
        .container {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 15px;
        }
        .no-print {
            text-align: center;
            padding: 15px;
            background: #333;
            margin-bottom: 15px;
        }
        .no-print button {
            padding: 8px 20px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            color: white;
        }
        .btn-print {
            background: #28a745;
        }
        .btn-close {
            background: #dc3545;
        }
        .btn-print:hover { background: #218838; }
        .btn-close:hover { background: #c82333; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mb-20 { margin-bottom: 20px; }
        .mb-30 { margin-bottom: 30px; }
        .mt-30 { margin-top: 30px; }
        .mt-50 { margin-top: 50px; }
        h2 { font-size: 24px; margin-bottom: 10px; }
        h3 { font-size: 18px; margin: 20px 0; }
        p { margin-bottom: 10px; }
        ul { margin-left: 20px; margin-bottom: 20px; }
        li { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table td, table th { padding: 8px; border: 1px solid #333; }
        table th { background: #f5f5f5; }
        .signature-area {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }
        .gray-bg {
            background: #f5f5f5;
            padding: 20px;
        }
        @page {
            margin: 0.5in;
        }
        @media print {
            body { 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact;
                margin: 0;
            }
            .no-print { display: none !important; }
            .container { padding: 0; }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">
            <i class="fa fa-print"></i> Print
        </button>
        <button class="btn-close" onclick="window.close()">
            <i class="fa fa-times"></i> Close
        </button>
    </div>
    @yield('contents')
</body>
</html>
