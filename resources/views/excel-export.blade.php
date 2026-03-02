<?php
// resources/views/excel-export.blade.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Export Example</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Export Data to Excel</h3>
        <form method="POST" action="{{ route('excel.export') }}">
            @csrf
            <button type="submit" class="btn btn-success">Export to Excel</button>
        </form>
        <hr>
        <div>
            <h5>Sample Data Table</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>John Doe</td>
                        <td>john@example.com</td>
                        <td>HR</td>
                    </tr>
                    <tr>
                        <td>Jane Smith</td>
                        <td>jane@example.com</td>
                        <td>Finance</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
