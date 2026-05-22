@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Print Documents') }}</title>
@endsection

@push('css')
<style>
    .bulk-print-card {
        background: #fff;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 1px 4px rgba(0,0,0,.12);
    }
    .filter-section {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .filter-section legend {
        font-weight: 600;
        font-size: 14px;
        color: #495057;
        padding: 0 8px;
        width: auto;
    }
    .doc-type-option {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 8px 14px;
        margin: 4px;
        display: inline-block;
        cursor: pointer;
    }
</style>
@endpush

@section('breadcrumb')
<li class="breadcrumb-item"><a href="#">Documents</a></li>
<li class="breadcrumb-item active">Bulk Print</li>
@endsection

@section('contents')
<div class="container-fluid flex-grow-1 ">
    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">
            <div class="bulk-print-card">
                <h5 class="mb-4 font-weight-bold">
                    <i class="fas fa-print mr-2 text-primary"></i>
                    Bulk Print Documents / বাল্ক প্রিন্ট
                </h5>

                <form method="GET" action="{{ route('admin.documents.bulkPrintPreview') }}" target="_blank">

                    {{-- Print Type --}}
                    <div class="form-group">
                        <label for="doc_type" class="font-weight-semibold">
                            Print Type / প্রিন্টের ধরন <span class="text-danger">*</span>
                        </label>
                        <select name="doc_type" id="doc_type" class="form-control" required>
                            <option value="">-- Select Document Type --</option>
                            <option value="id-card">ID Card (পরিচয়পত্র)</option>
                            <option value="nominee-form">Nominee Form (মনোনয়ন ফরম)</option>
                            <option value="age-verification">Age Verification (বয়স ও সক্ষমতার প্রত্যয়নপত্র)</option>
                            <option value="job-application">Job Application Form (চাকুরীর আবেদন)</option>
                            <option value="appointment-letter">Appointment Letter (নিয়োগ পত্র)</option>
                            {{-- <option value="employment-letter">Employment Letter (চাকরির নিশ্চয়তাপত্র)</option>
                            <option value="employment-notes">Employment Notes (নিয়োগ সংক্রান্ত নোট)</option> --}}
                            <option value="increment-letter">Increment Letter (বেতন বৃদ্ধির পত্র)</option>
                            <option value="job-responsibility">Job Responsibility (দায়িত্ব ও কর্তব্য)</option>
                            <option value="joining-letter">Joining Letter (যোগদান পত্র)</option>
                            <option value="appraisal-letter">Appraisal Letter (মূল্যায়ন ফরম)</option>
                        </select>
                    </div>

                    {{-- Language --}}
                    <div class="form-group">
                        <label class="font-weight-semibold">Language / ভাষা</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="language" id="lang_bn" value="bn" checked>
                                <label class="form-check-label" for="lang_bn">বাংলা (Bengali)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="language" id="lang_en" value="en">
                                <label class="form-check-label" for="lang_en">English</label>
                            </div>
                        </div>
                    </div>

                    {{-- Employee Filter --}}
                    <fieldset class="filter-section">
                        <legend>Employee Filter (choose one method or leave all blank for all employees)</legend>

                        {{-- By Employee IDs --}}
                        <div class="form-group">
                            <label for="employee_ids" class="font-weight-semibold">
                                Select Employees (by ID / Name)
                                <small class="text-muted">— multi-select</small>
                            </label>
                            <select name="employee_ids[]" id="employee_ids" class="form-control select2-employee" multiple>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->employee_id }} — {{ $emp->name }}{{ $emp->bn_name ? ' / '.$emp->bn_name : '' }}
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">If selected, section/department filters are ignored.</small>
                        </div>

                        <div class="text-center my-2 text-muted font-weight-bold">— OR —</div>

                        {{-- By Section --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="section_id" class="font-weight-semibold">Section</label>
                                    <select name="section_id" id="section_id" class="form-control">
                                        <option value="">-- All Sections --</option>
                                        @foreach($sections as $sec)
                                        <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="department_id" class="font-weight-semibold">Department</label>
                                    <select name="department_id" id="department_id" class="form-control">
                                        <option value="">-- All Departments --</option>
                                        @foreach($departments as $dep)
                                        <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Section and department filters apply only when no employees are selected above.</small>
                    </fieldset>

                    <div class="text-right mt-3">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-print mr-1"></i> Preview &amp; Print
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function () {
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-employee').select2({
            placeholder: 'Search by Employee ID or Name...',
            allowClear: true,
            width: '100%',
        });
    }
});
</script>
@endpush
