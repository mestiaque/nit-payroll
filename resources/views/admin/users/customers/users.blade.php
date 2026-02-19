@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Employee Users')}}</title>
@endsection
@push('css')

@endpush
@section('contents')


@include(adminTheme().'alerts')
<div class="flex-grow-1">
<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Employee List</h3>
         <div class="dropdown">

             <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddUser">
                 <i class="bx bx-plus"></i> Employee
             </a>
             <a href="{{route('admin.usersCustomer')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">

        <div class="accordion-box">
            <div class="accordion">
                <div class="accordion-item">
                 <a class="accordion-title" href="javascript:void(0)">
                     <i class="bx bx-filter-alt"></i>
                    Search click Here..
                 </a>
                 <div class="accordion-content" style="border:1px solid #e1000a;border-top:0;">
                     <form action="{{route('admin.usersCustomer')}}">
                        <div class="row">
                            <div class="col-md-5 mb-1">
                                <div class="input-group">
                                    <input type="date" name="startDate" value="{{request()->startDate?:''}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                                    <input type="date" value="{{request()->endDate?:''}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                                </div>
                            </div>
                            <div class="col-md-2 mb-1">
                                <select class="form-control" name="role_id">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                    <option value="{{$role->id}}" {{request()->role_id==$role->id?'selected':''}} >{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5 mb-1">
                                <div class="input-group">
                                    <input type="text" name="search" value="{{request()->search?:''}}" placeholder="User Name, Email, Mobile" class="form-control {{$errors->has('search')?'error':''}}" />
                                    <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                 </div>
                </div>
            </div>
        </div>
        <br>
        <form action="{{route('admin.usersCustomer')}}">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group mb-1">
                        <select class="form-control form-control-sm rounded-0" name="action" required="">
                            <option value="">Select Action</option>
                            <option value="1">Active</option>
                            <option value="2">Inactive</option>
                            <option value="5">Delete</option>
                        </select>
                        <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                    </div>
                </div>
                <div class="col-md-4">

                </div>
                <div class="col-md-4">
                    <ul class="statuslist">
                        <li><a href="{{route('admin.usersCustomer')}}" class="{{request()->status?'':'active'}}" >All ({{$totals->total}})</a></li>
                        <li><a href="{{route('admin.usersCustomer',['status'=>'active'])}}" class="{{request()->status=='active'?'active':''}}" >Active ({{$totals->active}})</a></li>
                        <li><a href="{{route('admin.usersCustomer',['status'=>'inactive'])}}" class="{{request()->status=='inactive'?'active':''}}" >Inactive ({{$totals->inactive}})</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 70px;">SL</th>
                            <th style="min-width: 200px;">Name</th>
                            <th style="min-width: 150px;">Designation</th>
                            <th style="min-width: 150px;">Department</th>
                            <th style="min-width: 150px;">Section</th>
                            <th style="min-width: 100px;">Line</th>
                            <th style="min-width: 120px;">Joining Date</th>
                            <th style="min-width: 120px;">Salary</th>
                            <th style="min-width: 150px;">Email</th>
                            <th style="min-width: 120px;">Mobile</th>
                            <th style="min-width: 80px;">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($users as $i=>$user)
                        <tr>
                            <td>
                                {{ $users->currentpage()==1 ? $i+1 : $i + ($users->perpage()*($users->currentpage() - 1)) + 1 }}
                            </td>

                            <td>
                                <a href="{{route('admin.usersCustomerAction',['view',$user->id])}}" target="_blank" class="invoice-action-view mr-1">
                                    {{ $user->name }}
                                </a>
                            </td>

                            <td>
                                @if($user->designation)
                                    {{ $user->designation->name }}
                                @else
                                    <span style="color:#FF9800;">N/A</span>
                                @endif
                            </td>

                            <td>
                                @if($user->department)
                                    {{ $user->department->name }}
                                @else
                                    <span style="color:#FF9800;">N/A</span>
                                @endif
                            </td>

                            <td>
                                @if($user->section)
                                    {{ $user->section->name }}
                                @else
                                    <span style="color:#FF9800;">N/A</span>
                                @endif
                            </td>

                            <td>
                                @if($user->line)
                                    {{ $user->line->name }}
                                @else
                                    <span style="color:#FF9800;">N/A</span>
                                @endif
                            </td>

                            <td>
                                @if($user->joining_date)
                                    {{ \Carbon\Carbon::parse($user->joining_date)->format('d M Y') }}
                                @else
                                    {{ $user->created_at->format('d M Y') }}
                                @endif
                            </td>

                            <td>
                                @if($user->gross_salary)
                                    {{ number_format($user->gross_salary, 2) }}
                                @else
                                    <span style="color:#FF9800;">Not Set</span>
                                @endif
                            </td>

                            <td>{{ $user->email }}</td>

                            <td>{{ $user->mobile }}</td>

                            <td style="text-align:center;">
                                <a href="{{route('admin.usersCustomerAction',['edit',$user->id])}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>

                                @if($user->id != Auth::id())
                                <a href="{{route('admin.usersCustomerAction',['delete',$user->id])}}"
                                   onclick="return confirm('Are You Want To Delete?')"
                                   class="btn-custom danger">
                                    <i class="bx bx-trash"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
        {{$users->links('pagination')}}
    </div>
</div>
</div>


 <!-- Modal -->
 <div class="modal fade text-left" id="AddUser" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 	<form action="{{route('admin.usersCustomerAction','create')}}" method="post">
	   		@csrf
	   <div class="modal-header">
		 <h4 class="modal-title">Add Employee</h4>
		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		   <span aria-hidden="true">&times; </span>
		 </button>
	   </div>
	   <div class="modal-body">
	   		<div class="form-group">
			 <label for="name">Name* </label>
             <div class="controls">
                 <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Name" required="">
				@if ($errors->has('name'))
				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
				@endif
				</div>
         	</div>
			 <div class="form-group">
				<label for="name">Email* </label>
				<div class="controls">
					<input type="email" class="form-control {{$errors->has('email')?'error':''}}" name="email" placeholder="Enter Email" required="">
					@if ($errors->has('email'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('email') }}</p>
					@endif
				</div>
         	</div>
	   </div>
	   <div class="modal-footer">
		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
		 <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add Employee</button>
	   </div>
	   </form>
	 </div>
   </div>
 </div>




@endsection
@push('js')
@endpush
