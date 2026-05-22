

<div class="letter-box" style="border:none; padding:0; margin-top:0;">
    <div class="company-head" style="margin-bottom:10px;">
        <h3 style="margin:0; font-size:22px;">{{ $companyName }}</h3>
        <div style="font-size:13px;">{{ $companyAddress }}</div>
        <div style="margin-top:4px; font-weight:700; font-size:16px;">{{ $t('চাকরির নিশ্চয়তাপত্র', 'Employment Letter') }}</div>
    </div>

    <p style="font-size:12px; line-height:1.7; margin-bottom:8px;">{{ $t('তারিখ', 'Date') }}: {{ $today }}</p>
    <p style="font-size:12px; line-height:1.7; margin-bottom:8px;">{{ $t('প্রাপক', 'Employee') }}: {{ $employeeName }}</p>

    <p style="font-size:12px; line-height:1.7; text-align:justify; margin-bottom:10px;">
        {{ $t('এই মর্মে প্রত্যয়ন করা যাচ্ছে যে, আপনি আমাদের প্রতিষ্ঠানে', 'This is to certify that you are employed in our organization as') }}
        <strong>{{ $designation }}</strong>
        {{ $t('পদে,', 'in the') }}
        <strong>{{ $section }}</strong>
        {{ $t('সেকশনে,', 'section,') }}
        {{ $t('তারিখ', 'since') }} {{ $joiningDate }}
        {{ $t('থেকে কর্মরত আছেন।', '.') }}
    </p>

    <p style="font-size:12px; line-height:1.7; text-align:justify; margin-bottom:10px;">
        {{ $t('কোম্পানির নীতিমালা ও প্রযোজ্য শ্রম আইন অনুযায়ী আপনার চাকরির শর্তাবলী কার্যকর থাকবে।', 'Your employment remains subject to company policy and applicable labor laws.') }}
    </p>

    <p style="font-size:12px; line-height:1.7; margin-top:24px;">
        {{ $t('কর্তৃপক্ষের স্বাক্ষর', 'Authorized Signature') }}: ______________________
    </p>
</div>
