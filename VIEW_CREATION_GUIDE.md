# Quick Start Guide for Views

This file shows you how to quickly create the remaining views for the Employee Management System.

## View Templates Structure

All views extend the admin layout: `@extends(adminTheme().'layouts.app')`

## Example Views Created

✅ **employees/index.blade.php** - Complete employee list with filters and actions

## Views You Need to Create

### Priority 1 (Critical for basic operations)

1. **employees/create.blade.php** - Form to add new employee
   - Include fields from EmployeeInfo model
   - Photo and signature upload
   - Department, designation, shift dropdowns

2. **employees/show.blade.php** - View employee details
   - Use tabs for different sections:
     - Personal Info tab
     - Education tab (list + add form)
     - Training tab (list + add form)
     - Experience tab (list + add form)
     - Bank Info tab (list + add form)
     - Increments history tab

3. **employees/edit.blade.php** - Edit employee form
   - Similar to create.blade.php
   - Pre-fill with existing data

### Priority 2 (Attendance & Payroll)

4. **attendance/daily_report.blade.php** - Daily attendance with statistics
5. **payroll/index.blade.php** - Payroll dashboard with process button
6. **payroll/salary_sheet.blade.php** - Monthly salary sheet table

### Priority 3 (Reports)

7. **reports/gender_wise.blade.php** - Male/female employee lists
8. **reports/status_wise.blade.php** - Active/inactive lists
9. **reports/newly_joined.blade.php** - New employees report

### Priority 4 (Documents - PDF templates)

10. **documents/id_card_english.blade.php** - English ID card layout
11. **documents/pay_slip.blade.php** - Salary pay slip
12. Other PDF templates as needed

## Quick Copy-Paste Template Structure

```blade
@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Your Page Title')}}</title>
@endsection

@section('contents')
<div class="container-fluid">
    <!-- Your content here -->
</div>
@endsection

@push('js')
<script>
    feather.replace();
</script>
@endpush
```

## Common Components

### Alert Messages
```blade
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
```

### Form Structure
```blade
<form method="POST" action="{{ route('admin.employees.store') }}" enctype="multipart/form-data">
    @csrf
    
    <div class="form-group">
        <label>Name *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>
    
    <!-- More fields -->
    
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
```

### Data Table
```blade
<table class="table table-striped">
    <thead>
        <tr>
            <th>Column 1</th>
            <th>Column 2</th>
            <!-- More columns -->
        </tr>
    </thead>
    <tbody>
        @forelse($items as $item)
        <tr>
            <td>{{ $item->field }}</td>
            <!-- More fields -->
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No data found</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{ $items->links() }}
```

## Tips

1. **Use existing views as reference**: Check views in `resources/views/admin/users/` or `resources/views/admin/leads/` for structure
2. **Permissions**: Add permission checks: `@if(checkPermission('employee_create'))`
3. **Icons**: Use Feather icons: `<i data-feather="icon-name"></i>`
4. **Bootstrap classes**: Use Bootstrap 4/5 classes for styling
5. **Date formatting**: Use Carbon: `{{ $employee->created_at->format('d M, Y') }}`
6. **Check null values**: Always check if relationship exists before accessing

## Testing Views

1. Start with simple list/index views
2. Then create forms
3. Test CRUD operations
4. Finally work on complex reports and PDFs

## Next Steps

1. Create the Priority  1 views first
2. Test employee CRUD operations
3. Create attendance and payroll views
4. Build report views  
5. Design PDF templates last

Good luck! 🚀
