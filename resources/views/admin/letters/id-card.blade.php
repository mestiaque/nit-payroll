

<div class="id-card-sheet">
    <div class="id-card-side id-card-front">
        <div class="id-card-logo-wrap">
            @if(!blank(general()->logo()))
                <img src="{{ asset(general()->logo()) }}" alt="{{ $t('কোম্পানির লোগো', 'Company Logo') }}" class="id-card-logo">
            @endif
        </div>
        <h4 class="id-card-company">{{ $companyName }}</h4>
        <p class="id-card-address">{{ $companyAddress }}</p>

        <div class="id-card-strip">{{ $t('পরিচয়পত্র', 'ID CARD') }}</div>

        <div class="id-card-photo-wrap">
            <img src="{{ asset($employee->image()) }}" alt="{{ $t('কর্মচারীর ছবি', 'Employee Photo') }}" class="id-card-photo">
        </div>

        <table class="id-card-info">
            <tr><td>{{ $t('নাম', 'Name') }}</td><td class="name-cell"><span class="name-value" style="font-size: {{ $nameFontSize }}px;">: {{ $employeeName }}</span></td></tr>
            <tr><td>{{ $t('পদবি', 'Designation') }}</td><td>: {{ $designation }}</td></tr>
            <tr><td>{{ $t('আইডি নং', 'ID No.') }}</td><td>: {{ $idNumber }}</td></tr>
            <tr><td>{{ $t('বিভাগ', 'Dept.') }}</td><td>: {{ $department }}</td></tr>
            <tr><td>{{ $t('সেকশন', 'Section') }}</td><td>: {{ $section }}</td></tr>
            <tr><td>{{ $t('যোগদান', 'Join Date') }}</td><td>: {{ $joinDate }}</td></tr>
            <tr><td>{{ $t('শ্রেণি', 'Classification') }}</td><td>: {{ $classification }}</td></tr>
            <tr><td>{{ $t('ইস্যু তারিখ', 'Issue Date') }}</td><td>: {{ $joinDate }}</td></tr>

        </table>

        <div class="id-sign-row">
            <div>
                <div class="id-sign-line"></div>
                <div class="id-sign-label">{{ $t('কর্মচারীর স্বাক্ষর', 'Staff Signature') }}</div>
            </div>
            <div>
                <div class="id-sign-line"></div>
                <div class="id-sign-label">{{ $t('কর্তৃপক্ষের স্বাক্ষর', 'Authority Signature') }}</div>
            </div>
        </div>
    </div>

    <div class="id-card-side id-card-back">
        <div class="id-card-logo-wrap">
            @if(!blank(general()->logo()))
                <img src="{{ asset(general()->logo()) }}" alt="{{ $t('কোম্পানির লোগো', 'Company Logo') }}" class="id-card-logo">
            @endif
        </div>

        <p class="id-back-head">{{ $t('রক্তের গ্রুপ', 'Blood Group') }} : <strong>{{ $bloodGroup }}</strong></p>
        <p class="id-back-head">{{ $t('স্থায়ী ঠিকানা', 'Permanent Address') }}</p>
        <p class="id-back-text">{{ $permanentAddress }}</p>

        <p class="id-back-head" style="margin-top: 10px;">{{ $t('জরুরি যোগাযোগ নম্বর', 'Emergency Contact No.') }}:</p>
        <p class="id-back-text"><strong>{{ $emergency }}</strong></p>

        <p class="id-back-text" style="margin-top: 8px;">
            {{ $t('কার্ডটি পেলে নিচের ঠিকানায় বা নিকটস্থ অফিসে ফেরত দিন।', 'Please return to the following address or nearest office station.') }}
        </p>
        <p class="id-back-company"><strong>{{ $companyName }}</strong></p>
        <p class="id-back-text">{{ $companyAddress }}</p>
        <p class="id-back-text">{{ $t('যোগাযোগ নম্বর', 'Contact No.') }}: 0</p>

        <div class="id-card-strip id-card-strip-bottom">{{ $t('মেয়াদ: চাকরির শেষ তারিখ পর্যন্ত।', 'Exp. Date: Up to the last date of job.') }}</div>
    </div>
</div>
<style>
    .name-cell {
        white-space: nowrap;
    }

    .name-value {
        display: inline-block;
        max-width: 140px;
        white-space: nowrap;
        line-height: 1.1;
    }
</style>
