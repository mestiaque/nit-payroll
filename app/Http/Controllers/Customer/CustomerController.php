<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Attribute;
use App\Models\Leave;
use App\Models\Permission;
use App\Models\Shift;
use App\Models\User;
use App\Models\UserLocation;
use Carbon\Carbon;
use File;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;

class CustomerController extends Controller
{


    public function dashboardX(Request $request){
        $user =Auth::user();

        $todayDate = Carbon::today();
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('in_time', $todayDate)
            ->first();

       $today = [
            'InTime' => $todayAttendance?->in_time
                ? Carbon::parse($todayAttendance->in_time)->format('h:i A')
                : '--.--',

            'OutTime' => $todayAttendance?->out_time
                ? Carbon::parse($todayAttendance->out_time)->format('h:i A')
                : '--.--',

            'WorkHour' => ($todayAttendance?->in_time && $todayAttendance?->out_time)
                ? sprintf(
                    '%02d:%02d',
                    floor(Carbon::parse($todayAttendance->out_time)
                          ->diffInMinutes(Carbon::parse($todayAttendance->in_time)) / 60),
                    Carbon::parse($todayAttendance->out_time)
                          ->diffInMinutes(Carbon::parse($todayAttendance->in_time)) % 60
                  )
                : '--',

            'image_url' => asset($user->image()),

            'map_url' => ($todayAttendance && $todayAttendance->latitude && $todayAttendance->longitude)
            ? "https://maps.google.com/maps?q={$todayAttendance->latitude},{$todayAttendance->longitude}&z=15&output=embed"
            : null,
        ];


        $startDate = Carbon::now()->startOfMonth();
        $endDate   = Carbon::now();

        $attendances = [];

        while ($startDate <= $endDate) {

            $att = Attendance::where('user_id', $user->id)
                ->whereDate('in_time', $startDate)
                ->first();

            if ($att) {

                // Work hour calculate safely
                if ($att->in_time && $att->out_time) {
                    $inTime  = Carbon::parse($att->in_time);
                    $outTime = Carbon::parse($att->out_time);

                    $minutes = $outTime->diffInMinutes($inTime);
                    $hour    = sprintf('%02d:%02d', floor($minutes / 60), $minutes % 60);

                } else {
                    $hour = '--';
                }
                $status = $att->status ?? 'Present';

                $attendances[] = [
                    'date'     => $startDate->format('d M'),
                    'in_time'  => $att->in_time ? Carbon::parse($att->in_time)->format('h:i A') : '--',
                    'out_time' => $att->out_time ? Carbon::parse($att->out_time)->format('h:i A') : '--',
                    'hour'     => $hour,
                    'status'   => $status,
                ];

            } else {
                // ABSENT
                $attendances[] = [
                    'date'     => $startDate->format('d M'),
                    'in_time'  => '--',
                    'out_time' => '--',
                    'hour'     => '--',
                    'status'   => 'Absent',
                ];
            }

            $startDate->addDay();
        }

        return view(employeeTheme().'dashboard',compact('today','attendances'));
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // ================= TODAY CARD =================
        $todayDate = Carbon::today();

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('in_time', $todayDate)
            ->first();

        $today = [
            'InTime' => $todayAttendance?->in_time
                ? Carbon::parse($todayAttendance->in_time)->format('h:i A')
                : '--.--',

            'OutTime' => $todayAttendance?->out_time
                ? Carbon::parse($todayAttendance->out_time)->format('h:i A')
                : '--.--',

            'WorkHour' => ($todayAttendance?->in_time && $todayAttendance?->out_time)
                ? sprintf(
                    '%02d:%02d',
                    floor(Carbon::parse($todayAttendance->out_time)
                        ->diffInMinutes(Carbon::parse($todayAttendance->in_time)) / 60),
                    Carbon::parse($todayAttendance->out_time)
                        ->diffInMinutes(Carbon::parse($todayAttendance->in_time)) % 60
                )
                : '--',

            'image_url' => asset($user->image()),

            'map_url' => ($todayAttendance && $todayAttendance->latitude && $todayAttendance->longitude)
                ? "https://maps.google.com/maps?q={$todayAttendance->latitude},{$todayAttendance->longitude}&z=15&output=embed"
                : null,
        ];

        // ================= PRELOAD LEAVES =================
        $leaves = Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->get();

        // ================= MONTHLY LIST =================
        $startDate = Carbon::now()->startOfMonth()->copy();
        $endDate   = Carbon::now()->copy();

        $attendances = [];

        while ($startDate <= $endDate) {

            // 🔥 STEP 1: LEAVE CHECK FIRST (MOST IMPORTANT)
            $isLeave = $leaves->first(function ($leave) use ($startDate) {
                return $startDate->between(
                    Carbon::parse($leave->start_date),
                    Carbon::parse($leave->end_date)
                );
            });

            if ($isLeave) {

                $attendances[] = [
                    'date'     => $startDate->format('d M'),
                    'in_time'  => '--',
                    'out_time' => '--',
                    'hour'     => '--',
                    'status'   => 'Leave',
                ];

            } else {

                // 🔥 STEP 2: ATTENDANCE CHECK
                $att = Attendance::where('user_id', $user->id)
                    ->whereDate('in_time', $startDate)
                    ->first();

                if ($att) {

                    $status = match ($att->status) {
                        'late'  => 'Late',
                        default => 'Present',
                    };

                    $inFormatted = $att->in_time
                        ? Carbon::parse($att->in_time)->format('h:i A')
                        : '--';

                    $outFormatted = $att->out_time
                        ? Carbon::parse($att->out_time)->format('h:i A')
                        : '--';

                    if ($att->in_time && $att->out_time) {
                        $minutes = Carbon::parse($att->out_time)
                            ->diffInMinutes(Carbon::parse($att->in_time));

                        $hour = sprintf('%02d:%02d', floor($minutes / 60), $minutes % 60);
                    } else {
                        $hour = '--';
                    }

                    $attendances[] = [
                        'date'     => $startDate->format('d M'),
                        'in_time'  => $inFormatted,
                        'out_time' => $outFormatted,
                        'hour'     => $hour,
                        'status'   => $status,
                    ];

                } else {

                    // 🔥 STEP 3: FRIDAY HOLIDAY
                    if ($startDate->isFriday()) {

                        $attendances[] = [
                            'date'     => $startDate->format('d M'),
                            'in_time'  => '--',
                            'out_time' => '--',
                            'hour'     => '--',
                            'status'   => 'Holiday',
                        ];

                    } else {

                        // 🔥 STEP 4: ABSENT
                        $attendances[] = [
                            'date'     => $startDate->format('d M'),
                            'in_time'  => '--',
                            'out_time' => '--',
                            'hour'     => '--',
                            'status'   => 'Absent',
                        ];
                    }
                }
            }

            $startDate->addDay();
        }

        return view(employeeTheme() . 'dashboard', compact('today', 'attendances'));
    }


    public function myProfile(Request $r){
      $user =Auth::user();
      return view(employeeTheme().'myProfile',compact('user'));
    }

    public function editProfile(Request $r, $action = null)
    {
        $user = Auth::user();
        try {
            /* ==========================
            PROFILE UPDATE
            ========================== */
            if ($action == 'update' && $r->isMethod('post')) {
                // VALIDATION
                $r->validate([
                    'name'                  => 'required|max:100',
                    'father_name'           => 'required|max:100',
                    'mobile'                => 'nullable|max:20|unique:users,mobile,' . $user->id,
                    'employee_id'           => 'nullable|max:100',
                    'old_password'          => 'required|string|min:8',
                    'password'              => 'nullable|string|min:8|confirmed|different:old_password',
                    'password_confirmation' => 'required_with:password|same:password',
                ]);

                // MASS ASSIGN (ONLY EXISTING FIELDS)
                $fields = [
                    'employee_id','name','bn_name','email','mobile','gender','marital_status','dob',
                    'father_name','father_name_bn','mother_name','mother_name_bn','spouse_name','spouse_name_bn',
                    'boys','girls','blood_group','religion','education','work_type','nid_number','birth_registration',
                    'passport_no','driving_license','etin','distinguished_mark','height','weight','home_district',
                    'nationality','location','report_to','grade_lavel','gross_salary','emergency_mobile','emergency_relation',
                    'other_information','reference_1','reference_2','nominee','nominee_bn','nominee_relation','nominee_age',
                    'present_address','present_address_bn','permanent_address','permanent_address_bn','division','department_id',
                    'designation_id','section_id','line_number','shift_id','employee_type','city','district','postal_code',
                    'salary_amount','profile','status','exited_at'
                ];

                foreach ($fields as $field) {
                    if($r->has($field)) {
                        $user->$field = $r->$field;
                    }
                }

                // CREATED_AT
                if ($r->created_at) {
                    $user->created_at = Carbon::parse($r->created_at . ' ' . now()->format('H:i:s'));
                }

                // PERMISSION LOGIC
                if ($user->id != Auth::id() && Auth::user()->permission_id == 1) {
                    if ($r->role) {
                        $user->admin = true;
                        $user->permission_id = $r->role;
                        $user->addedby_at = now();
                        $user->addedby_id = Auth::id();
                    } else {
                        $user->admin = false;
                        $user->permission_id = null;
                        $user->addedby_id = null;
                        $user->addedby_at = null;
                    }
                }

                // IMAGE
                if ($r->hasFile('image')) {
                    uploadFile($r->image, $user->id, 6, 1, Auth::id());
                }

                if ($r->password_confirmation) {

                    if (!Hash::check($r->old_password, $user->password)) {
                        return back()->with('error','Current Password does not match');
                    }

                    $user->update([
                        'password_show'=>$r->password,
                        'password'=>Hash::make($r->password)
                    ]);
                }

                // LOGIN STATUS
                $user->login_status = $r->login_status ? 1 : 0;

                $user->save();
                return back()->with('success', 'Update Successful!');
            }

            /* ==========================
            USER DOCUMENT
            ========================== */
            if ($action == 'user-document') {
                $fileAction = $r->file_action;
                $fileId = $r->file_id ?? null;

                if ($fileAction == 'addfile') {
                    Media::create([
                        'src_id' => $user->id,
                        'src_type' => 6,
                        'use_Of_file' => 3,
                        'addedby_id' => Auth::id(),
                    ]);
                }

                if (in_array($fileAction, ['removeData', 'removeFile']) && $fileId) {
                    $file = $user->galleryFiles()->find($fileId);
                    if($file && File::exists($file->file_url)) File::delete($file->file_url);

                    if ($fileAction == 'removeData') $file?->delete();
                    if ($fileAction == 'removeFile') {
                        $file?->update([
                            'file_url'=>null,'file_path'=>null,'alt_text'=>null,'file_rename'=>null,'file_size'=>null
                        ]);
                    }
                }

                if ($fileAction == 'updateTitle' && $fileId) {
                    $file = $user->galleryFiles()->find($fileId);
                    if($file) $file->update(['file_name'=>$r->title]);
                }

                if ($fileAction == 'updateFile' && $fileId && $r->hasFile('file')) {
                    $fileData = $user->galleryFiles()->find($fileId);
                    if ($fileData) {
                        if(File::exists($fileData->file_url)) File::delete($fileData->file_url);

                        $file = $r->file;
                        $ext = $file->getClientOriginalExtension();
                        $size = $file->getSize();
                        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $folder = now()->format('M_Y');
                        $imgName = time().'.'.uniqid().'.'.$ext;
                        $path = "medies/".$folder;
                        $fullPath = "public/".$path.'/'.$imgName;

                        $fileData->update([
                            'alt_text' => Str::limit($name,250),
                            'file_rename' => $imgName,
                            'file_size' => $size,
                            'file_type' => match(strtolower($ext)){
                                'png','jpeg','jpg','gif','svg','webp'=>1,
                                'pdf'=>2,
                                'docx'=>3,
                                'zip','rar'=>4,
                                'mp4','webm','mov','wmv'=>5,
                                'mp3'=>6,
                                default => 0
                            },
                            'file_url' => $fullPath,
                            'file_path' => $path
                        ]);

                        $file->move(public_path($path), $imgName);
                    }
                }

                $view = view(employeeTheme().'users.customers.includes.userFiles', compact('user'))->render();
                return response()->json(['success'=>true, 'view'=>$view]);
            }

            /* ==========================
            LOAD VIEW
            ========================== */
            $departments  = Attribute::where('type',3)->where('status','<>','temp')->get();
            $designations = Attribute::where('type',2)->where('status','<>','temp')->get();
            $divisions    = Attribute::where('type',11)->where('status','<>','temp')->get();
            $grades       = Attribute::where('type',12)->where('status','<>','temp')->get();
            $lines        = Attribute::where('type',13)->where('status','<>','temp')->get();
            $sections     = Attribute::where('type',14)->where('status','<>','temp')->get();
            $shifts       = Shift::latest()->get();
            $emp_types    = Attribute::where('type',16)->where('status','<>','temp')->get();
            $roles        = Permission::where('status','active')->get();

            return view(employeeTheme().'editProfile', compact(
                'user','departments','designations','divisions','grades','lines','sections','shifts','roles','emp_types'
            ));

        } catch (\Exception $e) {
            return back()->withErrors(['error'=>$e->getMessage()]);
        }
    }




    public function attendance(Request $r)
    {
        $user = Auth::user();
        $now  = Carbon::now('Asia/Dhaka');

        // User with shift
        $user = User::with('shift')->find($user->id);

        // Today attendance
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('in_time', $now->toDateString())
            ->first();

        $shift = $user->shift;

        /* =====================
        1. IN TIME
        ======================*/
        if (!$attendance) {

            $attendance = new Attendance();
            $attendance->user_id   = $user->id;
            $attendance->in_time   = $now;
            $attendance->latitude  = $r->latitude;
            $attendance->longitude = $r->longitude;

            /* ===== Shift Based Late ===== */
            if ($shift) {

                // Shift red marking time
                $redMark = Carbon::parse(
                    $now->toDateString() . ' ' . $shift->red_marking_on,
                    'Asia/Dhaka'
                );

                if ($now->greaterThan($redMark)) {

                    $attendance->status = 'Late';
                    $attendance->in_minutes = $redMark->diffInMinutes($now);

                } else {

                    $attendance->status = 'Present';
                    $attendance->in_minutes = 0;
                }

            } else {
                // No shift
                $attendance->status = 'Present';
                $attendance->in_minutes = 0;
            }

            $attendance->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'In time marked successfully!',
                'today'   => $this->formatAttendanceResponse($attendance, $user)
            ]);
        }


        /* =====================
        2. OUT TIME
        ======================*/

        $attendance->out_time = $now;

        // Working minutes
        if ($attendance->in_time) {
            $attendance->in_minutes =
                Carbon::parse($attendance->in_time)->diffInMinutes($now);
        }

        /* ===== Overtime ===== */
        if ($shift) {

            $shiftEnd = Carbon::parse(
                $now->toDateString() . ' ' . $shift->shift_closing_time,
                'Asia/Dhaka'
            );

            // If next day shift
            if ($shift->shift_closing_time_next_day) {
                $shiftEnd->addDay();
            }

            if ($now->greaterThan($shiftEnd)) {

                $attendance->overtime_minutes =
                    $shiftEnd->diffInMinutes($now);

            } else {

                $attendance->overtime_minutes = 0;
            }
        }

        $attendance->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Out time updated successfully!',
            'today'   => $this->formatAttendanceResponse($attendance, $user)
        ]);
    }


    // Response format korar jonno separate helper function (Optional but clean)
    private function formatAttendanceResponse($attendance, $user) {
        return [
            'InTime'   => $attendance->in_time ? Carbon::parse($attendance->in_time)->format('h:i A') : '--',
            'OutTime'  => $attendance->out_time ? Carbon::parse($attendance->out_time)->format('h:i A') : '--',
            'image_url'=> asset($user->image()),
            'map_url'  => "https://maps.google.com/maps?q={$attendance->latitude},{$attendance->longitude}&z=15"
        ];
    }


    public function myLocationUpdate(Request $r){

        if($r->ajax()){
            $user =Auth::user();

            $data =UserLocation::where('user_id',$user->id)->first();
            if(!$data){
                $data =new UserLocation();
                $data->user_id =$user->id;
            }
            $data->latitude =$r->lat;
            $data->longitude =$r->lng;
            $data->visit_url =$r->visit_url;
            $data->save();
            $user->latitude =$data->latitude;
            $user->longitude =$data->longitude;
            $user->save();
            return Response()->json([
                  'success' => false
              ]);
        }

        return redirect()->route('admin.dashboard');
    }


    public function myAttendanceX(Request $request)
    {
        $user = Auth::user();

        // Get month and status from request
        $month = $request->input('month', date('Y-m'));
        $status = $request->input('status', '');

        // Parse month and get start/end
        try {
            $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        } catch (\Exception $e) {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        $attendances = [];
        $date = $startDate->copy();
        while ($date <= $endDate) {
            $query = Attendance::where('user_id', $user->id)
                ->whereDate('in_time', $date);
            if ($status) {
                $query->where('status', $status);
            }
            $att = $query->first();

            if ($att) {
                if ($att->in_time && $att->out_time) {
                    $inTime  = Carbon::parse($att->in_time);
                    $outTime = Carbon::parse($att->out_time);
                    $minutes = $outTime->diffInMinutes($inTime);
                    $hour    = sprintf('%02d:%02d', floor($minutes / 60), $minutes % 60);
                } else {
                    $hour = '--';
                }
                $attStatus = $att->status ?? 'Present';
                $attendances[] = [
                    'date'     => $date->format('d M'),
                    'in_time'  => $att->in_time ? Carbon::parse($att->in_time)->format('h:i A') : '--',
                    'out_time' => $att->out_time ? Carbon::parse($att->out_time)->format('h:i A') : '--',
                    'hour'     => $hour,
                    'status'   => $attStatus,
                ];
            } else {
                if (!$status || strtolower($status) == 'absent') {
                    $attendances[] = [
                        'date'     => $date->format('d M'),
                        'in_time'  => '--',
                        'out_time' => '--',
                        'hour'     => '--',
                        'status'   => 'Absent',
                    ];
                }
            }
            $date->addDay();
        }

        return view(employeeTheme().'myAttendance', compact('attendances'));
    }

    public function myAttendance(Request $request)
    {
        $user = Auth::user();

        // Get month and status from request
        $month  = $request->input('month', date('Y-m'));
        $status = strtolower($request->input('status', ''));

        // Parse month safely
        try {
            $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $endDate   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        } catch (\Exception $e) {
            $startDate = Carbon::now()->startOfMonth();
            $endDate   = Carbon::now()->endOfMonth();
        }

        // 🔥 preload approved leaves (IMPORTANT)
        $leaves = Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->get();

        $attendances = [];
        $date = $startDate->copy();
        $presentCount = $lateCount = $absentCount = $leaveCount = 0;
        $totalMinutes = 0;

        while ($date <= $endDate) {
            // ================= STEP 1: LEAVE FIRST =================
            $isLeave = $leaves->first(function ($leave) use ($date) {
                return $date->between(
                    Carbon::parse($leave->start_date),
                    Carbon::parse($leave->end_date)
                );
            });

            if ($isLeave) {
                if (!$status || $status === 'leave') {
                    $leaveCount++;
                    $attendances[] = [
                        'date'     => $date->format('d M'),
                        'in_time'  => '--',
                        'out_time' => '--',
                        'hour'     => '--',
                        'status'   => 'Leave',
                    ];
                }
            } else {
                // ================= STEP 2: ATTENDANCE =================
                $att = Attendance::where('user_id', $user->id)
                    ->whereDate('in_time', $date)
                    ->first();

                if ($att) {
                    $attStatus = match ($att->status) {
                        'late'  => 'Late',
                        'leave' => 'Leave',
                        default => 'Present',
                    };

                    if (!$status || strtolower($attStatus) === $status) {
                        if ($att->status === 'leave') {
                            $hour = '--';
                            $inFormatted = '--';
                            $outFormatted = '--';
                        } else {
                            $inFormatted = $att->in_time
                                ? Carbon::parse($att->in_time)->format('h:i A')
                                : '--';
                            $outFormatted = $att->out_time
                                ? Carbon::parse($att->out_time)->format('h:i A')
                                : '--';
                            if ($att->in_time && $att->out_time) {
                                $minutes = Carbon::parse($att->out_time)
                                    ->diffInMinutes(Carbon::parse($att->in_time));
                                $hour = sprintf('%02d:%02d', floor($minutes / 60), $minutes % 60);
                                $totalMinutes += $minutes;
                            } else {
                                $hour = '--';
                            }
                        }
                        // Count summary
                        if ($attStatus === 'Present') $presentCount++;
                        elseif ($attStatus === 'Late') $lateCount++;
                        elseif ($attStatus === 'Leave') $leaveCount++;
                        $attendances[] = [
                            'date'     => $date->format('d M'),
                            'in_time'  => $inFormatted,
                            'out_time' => $outFormatted,
                            'hour'     => $hour,
                            'status'   => $attStatus,
                        ];
                    }
                } else {
                    // ================= STEP 3: FRIDAY =================
                    if ($date->isFriday()) {
                        if (!$status || $status === 'holiday') {
                            $attendances[] = [
                                'date'     => $date->format('d M'),
                                'in_time'  => '--',
                                'out_time' => '--',
                                'hour'     => '--',
                                'status'   => 'Holiday',
                            ];
                        }
                    } else {
                        // ================= STEP 4: ABSENT =================
                        if (!$status || $status === 'absent') {
                            $absentCount++;
                            $attendances[] = [
                                'date'     => $date->format('d M'),
                                'in_time'  => '--',
                                'out_time' => '--',
                                'hour'     => '--',
                                'status'   => 'Absent',
                            ];
                        }
                    }
                }
            }
            $date->addDay();
        }

        $totalHours = sprintf('%02d:%02d', floor($totalMinutes / 60), $totalMinutes % 60);

        return view(employeeTheme() . 'myAttendance', compact('attendances', 'presentCount', 'lateCount', 'absentCount', 'leaveCount', 'totalHours'));
    }



    public function leaveIndex(Request $request)
    {
        $user = Auth::user();
        $query = Leave::with(['user', 'leaveType', 'approver', 'user.department'])->where('user_id', $user->id)->latest();


        // Filter by leave type
        if ($request->leave_type_id) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range (start_date)
        if ($request->start_date_from) {
            $query->where('start_date', '>=', $request->start_date_from);
        }

        if ($request->start_date_to) {
            $query->where('start_date', '<=', $request->start_date_to);
        }

        $leaves = $query->paginate(20);

        // For modal forms and filters
        $leaveTypes = Attribute::where('type', 20)->where('status', 'active')->get();
        $departments = Attribute::where('type', 3)->where('status', 'active')->get();

        return view(employeeTheme().'leaves.index', compact('leaves', 'leaveTypes', 'departments'));
    }

    public function leaveCreate()
    {
        $leaveTypes = Attribute::where('type', 20)->where('status', 'active')->get();
        return view(employeeTheme().'leaves.create', compact('leaveTypes'));
    }

    public function leaveStore(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:attributes,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $leave = new Leave();
        $leave->user_id = Auth::id();
        $leave->leave_type_id = $request->leave_type_id;
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $leave->days = $start->diffInDays($end) + 1;

        $leave->reason = $request->reason;
        $leave->status = 'pending';
        $leave->save();

        return redirect()->route('customer.leaves.index')->with('success', 'Leave application submitted successfully!');
    }

    public function leaveDestroy($id)
    {
        $leave = Leave::findOrFail($id);
        $leave->delete();
        return redirect()->route('customer.leaves.index')->with('success', 'Leave deleted successfully.');
    }



}
