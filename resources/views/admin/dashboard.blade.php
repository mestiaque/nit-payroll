@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Dashboard')}}</title>
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset(assetLinkAdmin().'/assets/css/fullcalendar.min.css')}}" />
<style type="text/css">
    #eventModal {
      display: none;
      position: fixed;
      top: 20%;
      left: 50%;
      transform: translateX(-50%);
      padding: 20px;
      z-index: 1000;
      max-width: 600px;
      width: 100%;
    }

    #eventModal .body{
        background: white;
        padding: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
    }

    #modalOverlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 999;
    }

    #eventModal h3 {
      margin-top: 0;
      font-size: 24px;
    }

    #eventLink {
      color: black;
      display: inline-block;
      margin-top: 10px;
    }

    .close-btn {
      background-color: #f44336;
      color: white;
      border: none;
      padding: 5px 10px;
      cursor: pointer;
      border-radius: 5px;
      float: right;
    }

    .dataex-html5-export {
        width: 100% !important;
        table-layout: fixed;
    }

    .stats-card-box {
        padding: 20px 20px 20px 85px;
    }

    .stats-card-box .icon-box {
        width: 50px;
        height: 50px;
        font-size: 30px;
    }

</style>
@endpush
@section('contents')

<div class="flex-grow-1">
<!-- Breadcrumb Area -->
<div class="breadcrumb-area">
    <h1>Dashboard</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item">Dashboard</li>
    </ol>
</div>
<!-- End Breadcrumb Area -->
@if(Auth::user()->permission && Auth::user()->permission->id==1)
<!-- Start -->
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="stats-card-box">
            <div class="icon-box">
                <i class="bx bx-group"></i>
            </div>
            <span class="sub-title">Employees</span>
            <h3 style="font-size: 20px;">
                {{number_format($reports['totalEmployee'])}}
            </h3>
            <div class="progress-list">
                <p>Total <a href="#">View</a></p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card-box">
            <div class="icon-box">
                <i class="bx bx-radio-circle-marked"></i>
            </div>
            <span class="sub-title">Present</span>
            <h3>
                {{number_format($reports['present'])}}
            </h3>
            <div class="progress-list">
                <p>Today <a href="#">View</a></p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-card-box">
            <div class="icon-box">
                <i class="bx bx-money"></i>
            </div>
            <span class="sub-title">Salary</span>
            <h3>
                {{priceFormat($reports['salary'])}}
            </h3>
            <div class="progress-list">

                <p>Monthly <a href="#">View</a></p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card-box">
            <div class="icon-box">
                <i class="bx bx-user"></i>
            </div>
            <span class="sub-title">Admin</span>
            <h3>
                {{number_format($reports['admin'])}}
            </h3>
            <div class="progress-list">
                <p>Total <a href="#">View</a></p>
            </div>
        </div>
    </div>
</div>
<!-- End -->
<div class="row">
    <div class="col-lg-7 col-md-12">
        <div class="card mb-30">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Attendence [{{Carbon\Carbon::now()->format('d M, Y')}}]</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="min-width: 200px;">Employee</th>
                                <th style="width: 120px;">In Time</th>
                                <th style="width: 120px;">Out Time</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($attendances as $attendane)
                            <tr>
                                <td>{{$attendane->name}}</td>
                                <td>{{$attendane->InTime}}</td>
                                <td>{{$attendane->OutTime}}</td>
                            </tr>
                            @endforeach

                            @if($attendances->count()==0)
                            <tr>
                                <td style="text-align:center;" colspan="3">No Attendance</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5 col-md-12">
        <div class="card mb-30">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Summery</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td style="width: 150px;" >Designation</td>
                                <td>{{number_format($reports['designation'])}}</td>
                            </tr>
                            <tr>
                                <td>Division</td>
                                <td>{{number_format($reports['division'])}}</td>
                            </tr>
                            <tr>
                                <td>Section</td>
                                <td>{{number_format($reports['section'])}}</td>
                            </tr>
                            <tr>
                                <td>Department</td>
                                <td>{{number_format($reports['department'])}}</td>
                            </tr>
                            <tr>
                                <td>Shift</td>
                                <td>{{number_format($reports['shift'])}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endif

</div>
@endsection

@push('js')
<script src="{{asset(assetLinkAdmin().'/assets/js/fullcalendar.min.js')}}"></script>
<script src="https://unpkg.com/popper.js/dist/umd/popper.min.js"></script>
<script src="https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js"></script>
<!--<script src="{{asset(assetLinkAdmin().'/assets/js/calendar-activision.js')}}"></script>-->
<script>
    document.addEventListener('DOMContentLoaded', function () {
      var calendarEl = document.getElementById('calendar');

      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        initialDate: new Date(),
        navLinks: false,
        editable: false,
        selectable: false,




        eventClick: function (info) {
          var event = info.event;

          var title = event.title || 'No Title';
          var description = event.extendedProps.description || 'No description available.';
          var link = event.extendedProps.link || '';

          if(event.extendedProps.type=='task'){
          document.getElementById('eventTitle').innerHTML = '<b>Task:</b> ' + title;
          }else if(event.extendedProps.type=='visit'){
          document.getElementById('eventTitle').innerHTML = '<b>Visit:</b> ' + title;
          }else if(event.extendedProps.type=='commitment'){
          document.getElementById('eventTitle').innerHTML = '<b>Commitment:</b> ' + title;
          }else{
          document.getElementById('eventTitle').innerHTML = '<b>Meeting:</b> ' + title;
          }

          document.getElementById('eventDescription').innerHTML = description;

            const eventLinkEl = document.getElementById('eventLink');
            const spanEl = eventLinkEl.querySelector('span');
            const eventType = event.extendedProps.type;

            // Update the link section based on event type (task or meeting)
            if (link) {
                const bTag = eventLinkEl.querySelector('b');
                if (eventType === 'task' || eventType === 'visit') {
                    // For task, append "Attachment" in <b> tag and create a download link
                    bTag.innerHTML = 'Attachment:';
                    const anchor = document.createElement('a');
                    anchor.href = link;
                    anchor.textContent = 'Download';  // Show "Download" for tasks
                    anchor.download = '';  // Makes it a download link
                    anchor.target = '_blank';  // Open in a new tab
                    spanEl.innerHTML = '';  // Clear any previous content
                    spanEl.appendChild(anchor);
                    eventLinkEl.style.display = 'block';  // Show the link section
                } else if (eventType === 'meeting') {

                    if (/^https?:\/\//i.test(link)) {
                        // If it's a valid URL, create a link element
                        bTag.innerHTML = 'Location:';
                        const anchor = document.createElement('a');
                        anchor.href = link;
                        anchor.textContent = 'Link';  // Show "Link" for meetings
                        anchor.target = '_blank';  // Open in a new tab
                        spanEl.innerHTML = '';  // Clear any previous content
                        spanEl.appendChild(anchor);
                        eventLinkEl.style.display = 'block';  // Show the link section
                    } else {
                        // If it's not a URL, display it as normal text
                        bTag.innerHTML = 'Location:';
                        spanEl.textContent = link;  // Display as normal text if it's not a URL
                        eventLinkEl.style.display = 'block';  // Show the link section
                    }

                }
            } else {
                eventLinkEl.style.display = 'none';  // Hide the link section if no link exists
            }

          document.getElementById('modalOverlay').style.display = 'block';
          document.getElementById('eventModal').style.display = 'block';
        }
      });

      calendar.render();
    });

    function closeModal() {
      document.getElementById('eventModal').style.display = 'none';
      document.getElementById('modalOverlay').style.display = 'none';
    }



    // document.addEventListener('DOMContentLoaded', function () {
    //   var calendarEl = document.getElementById('calendar');
    //   if (calendarEl) {
    //     var calendar = new FullCalendar.Calendar(calendarEl, {
    //     //   headerToolbar: {
    //     //     left: 'prev next today',
    //     //     center: 'title',
    //     //     right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
    //     //   },
    //       initialView: 'dayGridMonth',
    //       initialDate: new Date(),
    //       navLinks: true, // can click day/week names to navigate views
    //       businessHours: true, // display business hours
    //       editable: true,
    //       selectable: true,

    //       events: [
    //         {
    //           title: 'Team Meeting',
    //           start: '2025-04-10',
    //           color: 'blue',
    //           description: 'Discuss project updates and goals.',
    //           link: 'https://example.com/team-meeting' // Optional link
    //         },
    //         {
    //           title: 'Finish Task A',
    //           start: '2025-04-11',
    //           color: 'green',
    //           description: 'Complete Task A before the deadline.',
    //           link: 'https://example.com/task-a-details' // Optional link
    //         },
    //         {
    //           title: 'Project Deadline',
    //           start: '2025-04-12',
    //           color: 'red',
    //           description: '', // No description for this event
    //           link: '' // No link for this event
    //         },
    //         {
    //           start: '2025-04-06',
    //           end: '2025-05-08',
    //           overlap: false,
    //           display: 'background',
    //           color: 'var(--clr-action-warning)',
    //           description: 'Ongoing task until the end of May.',
    //           link: '' // No link for this event
    //         }
    //       ],

    //       eventClick: function(info) {
    //         var event = info.event;
    //         var description = event.extendedProps.description || 'No description available.';
    //         var link = event.extendedProps.link || '#'; // Default to '#' if no link is provided

    //         // Show event details in alert or a modal
    //         alert('Event: ' + event.title + '\nDescription: ' + description + '\nLink: ' + link);
    //       }
    //     });

    //     calendar.render();
    //   }
    // });

</script>

@endpush
