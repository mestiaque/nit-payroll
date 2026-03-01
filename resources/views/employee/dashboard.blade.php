@extends(employeeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Dashboard')}}</title>
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset(assetLinkAdmin().'/assets/css/fullcalendar.min.css')}}" />
<style type="text/css">
    .todayAttendance{
    background:#ffffff;
    border-radius:14px;
    padding:16px;
    box-shadow:0 6px 18px rgba(0,0,0,0.08);
}

/* 1️⃣ In Time & Out Time inline with bg */
.todayAttendance .intime,
.todayAttendance .outtime{
    display:inline-block;
    padding:6px 14px;
    border-radius:20px;
    font-size:14px;
    font-weight:600;
    color:#fff;
    margin-right:8px;
    width: 130px;
    text-align: center;
}

.todayAttendance .intime{
    background:linear-gradient(135deg,#28a745,#5fd38c);
}

.todayAttendance .outtime{
    background:linear-gradient(135deg,#007bff,#5aa7ff);
}

/* 2️⃣ Image fixed height */
.showImageWithMap{
    display:flex;
    gap:10px;
    margin-top:15px;
}

.showImageWithMap .image,
.showImageWithMap .locationMap{
    width:50%;
    height:150px;
    border-radius:10px;
    overflow:hidden;
    background:#f2f2f2;
}

.showImageWithMap .image img{
    width:100%;
    height:100%;
    object-fit:cover;
}

/* 3️⃣ Map iframe */
.showImageWithMap .locationMap iframe{
    width:100%;
    height:100%;
    border:0;
    border-radius:10px;
}

/* 4️⃣ Button round + press effect */
.attenBtn{
    text-align:center;
    margin-top:18px;
}

.attenBtn .btn{
    width:120px;
    height:70px;
    border-radius:20px;
    background:linear-gradient(135deg,#ff416c,#ff4b2b);
    color:#fff;
    font-weight:700;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    box-shadow:0 8px 18px rgba(255,65,108,0.4);
    transition:all 0.2s ease;
    user-select:none;
    margin:auto;
}

.attenBtn .btn:active{
    transform:scale(0.92);
    box-shadow:0 4px 10px rgba(255,65,108,0.4);
}
.attenceTable tr th{
    padding:5px;
}
.attenceTable tr td{
    padding:5px;
}
</style>
@endpush
@section('contents')

<div class="flex-grow-1">
<!-- Breadcrumb Area -->

<!-- End Breadcrumb Area -->

<!-- Start -->
<div class="row">
    <div class="col-lg-4 col-md-5 col-12">
        <div class="todayAttendance card mb-30">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Today Attendence</h3>
                <a href="{{ route('customer.myAttendance') }}">View All</a>
            </div>
            <div style="display: flex;justify-content: space-between;">
                <span class="intime">IN: <span class="inTimeText">{{$today['InTime']}}</span></span>
                <span class="outtime">OUT: <span class="outTimeText">{{$today['OutTime']}}</span></span>
            </div>
            <div class="showImageWithMap">
                <div class="image">
                    <img class="selfie" src="{{$today['image_url']}}">
                </div>
                <div class="locationMap">
                    <iframe class="mapIframe"
                        src="{{$today['map_url']}}">
                    </iframe>
                </div>
            </div>
            <div class="msg" style="margin-top:10px; padding:10px; border-radius:5px; display:none;"></div>
            <div class="attenBtn">
                <div class="btn" data-url="{{route('customer.attendance')}}">CLICK</div>
            </div>
        </div>
    </div>
    <div class="col-lg-8 col-md-7 col-12">
        <div class="row">
            @php
                $present = collect($attendances)->where('status','Present')->count();
                $late = collect($attendances)->where('status','Late')->count();
                $absent = collect($attendances)->where('status','Absent')->count();
                $leave = collect($attendances)->where('status','Leave')->count();
            @endphp
            <div class="col-md-3">
                <div class="card shadow-sm border-0" style="background: #007bff38">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                            style="width:50px;height:50px;">
                            <i class="fa fa-check"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $present }}</h4>
                            <small class="text-muted">Present (This Month)</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0" style="background: #007bff38">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                            style="width:50px;height:50px;">
                            <i class="fa fa-clock"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $late }}</h4>
                            <small class="text-muted">Late (This Month)</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0" style="background: #007bff38">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                            style="width:50px;height:50px;">
                            <i class="fa fa-times"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $absent }}</h4>
                            <small class="text-muted">Absent (This Month)</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0" style="background: #007bff38">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                            style="width:50px;height:50px;">
                            <i class="fa fa-info"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $leave }}</h4>
                            <small class="text-muted">Leave (This Month)</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header">Leave Summary</div>
                    <div class="card-body">
                        @forelse($leaveSummary as $summary)
                            @php
                                $percentage = $summary['total_days'] > 0 ? ($summary['used_days'] / $summary['total_days']) * 100 : 0;
                                $progressClass = $percentage >= 100 ? 'bg-danger' : ($percentage >= 75 ? 'bg-warning' : 'bg-success');
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="font-weight-semibold">{{ $summary['name'] }}</span>
                                    <span class="text-muted">{{ $summary['used_days'] }}/{{ $summary['total_days'] }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $percentage }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No leave summary found.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</div>
@endsection

@push('js')

<script>
$(document).ready(function() {

    $('.attenBtn .btn').on('click', function(){
        var $btn = $(this);
        var url = $btn.data('url');
        $btn.prop('disabled', true).text('Processing...');
        getLocation()
        .then(function(location){
            var currentTime = getCurrentTime();
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    latitude: location.latitude,
                    longitude: location.longitude,
                    time: currentTime
                },
                success: function(res){

                    if(res.status === 'success'){
                        $('.inTimeText').text(res.today.InTime);
                        $('.outTimeText').text(res.today.OutTime);
                        $('.selfie').attr('src', res.today.image_url);
                        $('.mapIframe').attr('src', res.today.map_url);
                        console.log(res.today);
                    }

                    showMessage(res.message || 'Attendance marked successfully!', 'success');

                },
                error: function(xhr){
                    showMessage('Failed to mark attendance', 'error');
                },
                complete: function(){
                    $btn.prop('disabled', false).text('CLICK');
                }
            });

        })
        .catch(function(error){
            console.warn('Location error:', error);
            showMessage('Your browser is blocked for location access or denied permission!', 'warning');
            $btn.prop('disabled', false).text('CLICK');
        });

    });

    function showMessage(message, type){
        var color = '#28a745';
        if(type === 'error') color = '#dc3545';
        if(type === 'warning') color = '#ffc107';

        $('.msg').css({
            'background': color,
            'color':'#fff',
            'display':'block'
        }).text(message);

        // Hide after 4 sec
        setTimeout(function(){
            $('.msg').fadeOut();
        }, 4000);
    }

    function getLocation() {
        return new Promise(function(resolve, reject){
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position){
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        resolve({ latitude, longitude });
                    },
                    function(error){
                        reject(error);
                    }
                );
            } else {
                reject(new Error('Geolocation is not supported'));
            }
        });
    }

    function getCurrentTime() {
        return new Date().toISOString();
    }




});
</script>


@endpush
