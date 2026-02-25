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
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }
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
        table { width: 100%; border-collapse: collapse; }
        table td { padding: 10px; }
        .signature-area {
            margin-top: 60px;
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
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
    @yield('styles')
</head>
<body>
    @yield('contents')
</body>
</html>
