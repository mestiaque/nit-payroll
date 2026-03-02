<!-- Start Sidemenu Area -->
<div class="sidemenu-area">
    <div class="sidemenu-header">
        <a href="{{route('customer.dashboard')}}" class="navbar-brand d-flex align-items-center">
            <img src="{{asset(general()->logo())}}" alt="logo" />
        </a>
        <div class="burger-menu d-none d-lg-block">
            <span class="top-bar"></span>
            <span class="middle-bar"></span>
            <span class="bottom-bar"></span>
        </div>
        <div class="responsive-burger-menu d-block d-lg-none">
            <span class="top-bar"></span>
            <span class="middle-bar"></span>
            <span class="bottom-bar"></span>
        </div>
    </div>
    <div class="sidemenu-body">
        <ul class="sidemenu-nav metisMenu h-100" id="sidemenu-nav" data-simplebar="">
            <li class="nav-item-title">
                Main
            </li>
            <li class="nav-item {{Request::is('employee/dashboard')? 'mm-active' : ''}}">
                <a href="{{route('customer.dashboard')}}" class="nav-link">
                    <span class="icon"><i class='bx bxs-dashboard'></i></span>
                    <span class="menu-title">Dashboard </span>
                </a>
            </li>
            <li class="nav-item {{Request::is('employee/notices')? 'mm-active' : ''}}">
                <a href="{{route('customer.notices')}}" class="nav-link">
                    <span class="icon"><i class='bx bxs-notification'></i></span>
                    <span class="menu-title">Notices </span>
                </a>
            </li>
            <li class="nav-item {{Request::is('employee/profile')? 'mm-active' : ''}}">
                <a href="{{route('customer.myProfile')}}" class="nav-link">
                    <span class="icon"><i class="bx bx-user"></i></span>
                    <span class="menu-title">My Profile </span>
                </a>
            </li>
            <li class="nav-item {{Request::is('employee/leaves')? 'mm-active' : ''}}">
                <a href="{{route('customer.leaves.index')}}" class="nav-link">
                    <span class="icon"><i class="bx bx-calendar"></i></span>
                    <span class="menu-title">My Leaves </span>
                </a>
            </li>
            <li class="nav-item {{Request::is('employee/my-attendance')? 'mm-active' : ''}}">
                <a href="{{route('customer.myAttendance')}}" class="nav-link">
                    <span class="icon"><i class="bx bx-calendar"></i></span>
                    <span class="menu-title">My Attendance </span>
                </a>
            </li>
            <li class="nav-item {{Request::is('employee/share-location')? 'mm-active' : ''}}">
                <a href="{{route('customer.shareLocation')}}" class="nav-link">
                    <span class="icon"><i class="bx bx-map"></i></span>
                    <span class="menu-title">Share Location </span>
                </a>
            </li>

            <li class="nav-item" style="margin:100px 0"></li>
        </ul>

    </div>
</div>
<!-- End Sidemenu Area -->
