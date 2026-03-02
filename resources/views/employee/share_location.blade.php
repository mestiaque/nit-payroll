
@extends(employeeTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Notices') }}</title>
@endsection


@push('css')
<style>
    .location-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        padding: 30px;
        max-width: 600px;
        margin: 0 auto;
    }
    .location-icon {
        font-size: 60px;
        color: #4ecdc4;
        margin-bottom: 20px;
    }
    .btn-location {
        padding: 15px 40px;
        font-size: 18px;
        border-radius: 30px;
        transition: all 0.3s;
    }
    .btn-location:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(78, 205, 196, 0.4);
    }
    .location-status {
        margin-top: 20px;
        padding: 15px;
        border-radius: 8px;
    }
    .status-sharing {
        background: #d4edda;
        color: #155724;
    }
    .status-success {
        background: #cce5ff;
        color: #004085;
    }
    .location-info {
        margin-top: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }
    .location-coords {
        font-family: monospace;
        font-size: 14px;
    }
    #map {
        height: 300px;
        width: 100%;
        border-radius: 8px;
        margin-top: 20px;
        display: none;
    }
    .pulse {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
</style>
@endpush

@section('contents')
@include(adminTheme().'alerts')

<div class="content-wrapper flex-grow-1">
    <div class="row">
        <div class="col-md-12">
            <div class="location-card text-center">
                <div class="location-icon">
                    <i class="bx bx-map"></i>
                </div>

                <h3>Share Your Live Location</h3>
                <p class="text-muted">
                    Share your current location with your employer.
                    This helps with attendance tracking and employee safety.
                </p>

                <div id="permissionStatus" class="location-status status-sharing">
                    <i class="bx bx-info-circle"></i> Click the button below to share your location
                </div>

                <button type="button" id="shareLocationBtn" class="btn btn-primary btn-location mt-4" onclick="getLocation()">
                    <i class="bx bx-current-location"></i> Share My Location
                </button>

                <div id="locationInfo" class="location-info" style="display: none;">
                    <h5><i class="bx bx-check-circle text-success"></i> Location Shared!</h5>
                    <div class="location-coords">
                        <p><strong>Latitude:</strong> <span id="latDisplay">-</span></p>
                        <p><strong>Longitude:</strong> <span id="lngDisplay">-</span></p>
                        <p><strong>Time:</strong> <span id="timeDisplay">-</span></p>
                    </div>
                </div>

                <div id="map"></div>

                <form id="locationForm" method="POST" action="{{ route('customer.shareLocation.post') }}" style="display: none;">
                    @csrf
                    <input type="hidden" name="latitude" id="latInput">
                    <input type="hidden" name="longitude" id="lngInput">
                    <input type="hidden" name="full_address" id="addressInput">
                    <button type="submit" class="btn btn-success btn-location mt-3">
                        <i class="bx bx-check"></i> Confirm Location
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Privacy Information</h3>
                </div>
                <div class="card-body">
                    <ul>
                        <li>Your location is only shared with your employer/admin</li>
                        <li>Location data is used for attendance and security purposes only</li>
                        <li>You can stop sharing location at any time</li>
                        <li>Your previous location data may still be visible to admin</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('app.google_maps_key', env('GOOGLE_MAPS_API_KEY', '')) }}" async defer></script>
<script>
    var map;
    var marker;

    function getLocation() {
        var btn = document.getElementById('shareLocationBtn');

        if (navigator.geolocation) {
            btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Getting Location...';
            btn.classList.add('pulse');

            navigator.geolocation.getCurrentPosition(showPosition, showError, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            });
        } else {
            alert("Geolocation is not supported by this browser.");
            btn.innerHTML = '<i class="bx bx-map"></i> Share My Location';
        }
    }

    function showPosition(position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;

        // Update UI
        document.getElementById('latDisplay').innerText = lat;
        document.getElementById('lngDisplay').innerText = lng;
        document.getElementById('timeDisplay').innerText = new Date().toLocaleString();

        document.getElementById('latInput').value = lat;
        document.getElementById('lngInput').value = lng;

        document.getElementById('permissionStatus').innerHTML = '<i class="bx bx-check-circle text-success"></i> Location permission granted!';
        document.getElementById('permissionStatus').className = 'location-status status-success';

        document.getElementById('locationInfo').style.display = 'block';
        document.getElementById('locationForm').style.display = 'block';

        var btn = document.getElementById('shareLocationBtn');
        btn.innerHTML = '<i class="bx bx-check"></i> Location Found!';
        btn.classList.remove('pulse');
        btn.disabled = true;

        // Show map
        initMap(lat, lng);
    }

    function showError(error) {
        var btn = document.getElementById('shareLocationBtn');
        btn.innerHTML = '<i class="bx bx-map"></i> Share My Location';
        btn.classList.remove('pulse');

        switch(error.code) {
            case error.PERMISSION_DENIED:
                alert("Location permission denied. Please enable location access in your browser settings.");
                document.getElementById('permissionStatus').innerHTML = '<i class="bx bx-x-circle text-danger"></i> Permission denied. Please enable location access.';
                break;
            case error.POSITION_UNAVAILABLE:
                alert("Location information is unavailable.");
                break;
            case error.TIMEOUT:
                alert("The request to get location timed out.");
                break;
            default:
                alert("An unknown error occurred.");
                break;
        }
    }

    function initMap(lat, lng) {
        var mapDiv = document.getElementById('map');
        mapDiv.style.display = 'block';

        map = new google.maps.Map(mapDiv, {
            center: { lat: lat, lng: lng },
            zoom: 15
        });

        marker = new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map: map,
            title: 'Your Location',
            animation: google.maps.Animation.DROP
        });
    }
</script>
@endpush

@endsection
