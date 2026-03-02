<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Layout</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body, html {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
            }
            .print-btn {
                display: none;
            }
            .a4-sheet {
                box-shadow: none !important;
                border: none !important;
            }
        }
        .a4-sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            padding: 32px 24px;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        .company-logo {
            max-height: 60px;
        }
        .signature-box {
            min-height: 60px;
            border-top: 1px solid #333;
            margin-top: 32px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container print-btn mt-4">
        <button class="btn btn-primary" onclick="window.print()">Print</button>
    </div>
    <div class="a4-sheet">
        <div class="row align-items-center mb-3">
            <div class="col-2 text-center">
                <!-- Company Logo -->
                <img src="/path/to/logo.png" alt="Company Logo" class="company-logo">
            </div>
            <div class="col-10">
                <h3 class="mb-0">Company Name</h3>
                <p class="mb-0">Company Address Line 1<br>Company Address Line 2</p>
            </div>
        </div>
        <hr>
        <div class="text-center mb-4">
            <h4 id="report-title">Report Title</h4>
        </div>
        <!-- Data Table or Content -->
        <div id="report-content">
            <!-- Dynamic data goes here -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Column 1</th>
                        <th>Column 2</th>
                        <th>Column 3</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Data 1</td>
                        <td>Data 2</td>
                        <td>Data 3</td>
                    </tr>
                    <!-- More rows as needed -->
                </tbody>
            </table>
        </div>
        <div class="row mt-5">
            <div class="col-4 signature-box">
                <span>Prepared By</span>
            </div>
            <div class="col-4 signature-box">
                <span>Checked By</span>
            </div>
            <div class="col-4 signature-box">
                <span>Approved By</span>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS (optional, for interactivity) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
