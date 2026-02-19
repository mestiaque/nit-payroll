@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Visits List')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Visits List</h3>
         <div class="dropdown">
             @isset(json_decode(Auth::user()->permission->permission, true)['visits']['add'])
             <!--<a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddVisit" style="padding:5px 15px;">-->
             <!--    <i class="bx bx-plus"></i> Visit-->
             <!--</a>-->
             @endisset
             <a href="{{route('admin.visits')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.visits')}}">
            <div class="row">
                <div class="col-md-4 mb-1">
                    <div class="input-group">
                        <input type="date" name="startDate" value="{{request()->startDate}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                        <input type="date" value="{{request()->endDate}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                    </div>
                </div>
                <div class="col-md-4 mb-1">
                    <select  class="form-control" name="employee">
                        <option value="">Select Employee</option>
                        @foreach(App\Models\User::where('admin',true)->where('status',1)->orderBy('name')->get() as $data)
                        <option value="{{$data->id}}" {{$data->id==request()->employee?'selected':''}}>{{$data->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-0">
                    <div class="input-group">
                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search company name, visitor name" class="form-control {{$errors->has('search')?'error':''}}" />
                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                    </div>
                </div>
            </div>
        </form>
        <br>
        <form action="{{route('admin.visits')}}">
            <div class="row">
                <div class="col-md-4">
                    <!--<div class="input-group mb-1">-->
                    <!--    <select class="form-control form-control-sm rounded-0" name="action" required="">-->
                    <!--        <option value="">Select Action</option>-->
                    <!--        <option value="1">Pending</option>-->
                    <!--        <option value="2">In progress</option>-->
                    <!--        <option value="3">Review</option>-->
                    <!--        <option value="4">Completed</option>-->
                    <!--        <option value="5">On Hold</option>-->
                    <!--        <option value="6">Canceled</option>-->
                    <!--        <option value="7">Delete</option>-->
                    <!--    </select>-->
                    <!--    <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>-->
                    <!--</div>-->
                </div>
                <div class="col-md-8">
                    <ul class="statuslist">
                        <li><a href="{{route('admin.visits')}}">All ({{$totals->total}})</a></li>
                        <li><a href="{{route('admin.visits',['status'=>'Not Potential'])}}">Not Potential ({{$totals->nonPotential}})</a></li>
                        <li><a href="{{route('admin.visits',['status'=>'Potential'])}}">Potential ({{$totals->potential}})</a></li>
                        <li><a href="{{route('admin.visits',['status'=>'Very Potential'])}}">Very Potential ({{$totals->veryPotential}})</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 100px;width: 100px;padding-right:0;">
                                <!--<div class="checkbox mr-3">-->
                                <!-- <input class="inp-cbx" id="checkall" type="checkbox" style="display: none;" />-->
                                <!-- <label class="cbx" for="checkall">-->
                                <!--     <span>-->
                                <!--         <svg width="12px" height="10px" viewbox="0 0 12 10">-->
                                <!--             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>-->
                                <!--         </svg>-->
                                <!--     </span>-->
                                <!--     All <span class="checkCounter"></span> -->
                                <!-- </label>-->
                                <!--</div>-->
                                SL
                            </th>
                            <th style="min-width: 200px;">Visit Date</th>
                            <th style="min-width: 100px;">Location</th>
                            <th style="min-width: 100px;">Company/Lead</th>
                            <th style="min-width: 100px;">Description</th>
                            <th style="min-width: 100px;">Visit By</th>
                            <th style="min-width: 100px;">Status</th>
                            <th style="min-width: 100px;width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visits as $i=>$visit)
                        <tr>
                            <td>
                                <!--<div class="checkbox">-->
                                <!--     <input class="inp-cbx" id="cbx_{{$visit->id}}" type="checkbox" name="checkid[]" value="{{$visit->id}}" style="display: none;" />-->
                                <!--     <label class="cbx" for="cbx_{{$visit->id}}">-->
                                <!--         <span>-->
                                <!--             <svg width="12px" height="10px" viewbox="0 0 12 10">-->
                                <!--                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>-->
                                <!--             </svg>-->
                                <!--         </span>-->
                                <!--     </label>-->
                                <!-- </div>-->
                                <span style="margin:0 5px;">{{$visits->currentpage()==1?$i+1:$i+($visits->perpage()*($visits->currentpage() - 1))+1}}</span>
                            </td>
                            <td>
                                {{$visit->visit_date?Carbon\Carbon::parse($visit->visit_date)->format('d-m-Y'):''}}
                                @if($visit->imageFile)
                                <a href="{{asset($visit->image())}}" download="" style="margin-left: 5px;color: #e1000a;"><i class="bx bx-file"></i></a>
                                @endif
                            </td>
                            <td>{{$visit->location}}</td>
                            <td>
                                @if($visit->type==1)
                                <span>{{$visit->company?$visit->company->name:''}}</span>
                                @else
                                {{$visit->company?$visit->company->factory_name:''}}
                                @endif
                            </td>
                            <td>{{$visit->description}}</td>
                            <td>{{$visit->assinee?$visit->assinee->name:''}}</td>
                            <td>
                                @if($visit->status=='Not Potential')
                                <span class="badge" style="background: #9baaff;font-size: 14px;color: white;" >Not Potential</span>
                                @elseif($visit->status=='Potential')
                                <span class="badge" style="background: #5970f3;font-size: 14px;color: white;" >Potential</span>
                                @elseif($visit->status=='Very Potential')
                                <span class="badge" style="background: #0829e5;font-size: 14px;color: white;" >Very Potential</span>
                                @endif
                                
                                <!--@if($visit->status=='In progress')-->
                                <!--<span class="badge" style="background: #ff108c;font-size: 14px;color: white;" >{{ucfirst($visit->status)}}</span>-->
                                <!--@elseif($visit->status=='Completed')-->
                                <!--<span class="badge" style="background: #13c238;font-size: 14px;color: white;" >{{ucfirst($visit->status)}}</span>-->
                                <!--@elseif($visit->status=='Canceled')-->
                                <!--<span class="badge" style="background: #ff2e37;font-size: 14px;color: white;" >{{ucfirst($visit->status)}}</span>-->
                                <!--@elseif($visit->status=='Rescheduled')-->
                                <!--<span class="badge" style="background: #f326eb;font-size: 14px;color: white;" >{{ucfirst($visit->status)}}</span>-->
                                <!--@else-->
                                <!--<span class="badge" style="background: #2c66cb;font-size: 14px;color: white;" >{{ucfirst($visit->status)}}</span>-->
                                <!--@endif-->
                            </td>
                            
                            <td class="center">
                                @isset(json_decode(Auth::user()->permission->permission, true)['visits']['add'])
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#EditDesignations_{{$visit->id}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                @endisset
                                
                                @isset(json_decode(Auth::user()->permission->permission, true)['visits']['delete'])
                                <a href="{{route('admin.visitsAction',['delete',$visit->id])}}" class="btn-custom danger" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                                @endisset
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$visits->links('pagination')}}
            </div>
        </form>
        
        
    </div>
</div>
</div>

<!-- Add Modal -->
 <div class="modal fade text-left" id="AddVisit" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.visitsAction','create')}}" method="post" enctype="multipart/form-data">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Add Visit</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	        <div class="row">
    	            <div class="col-md-6 form-group">
                      <label>Visit Date*</label>
                      <input type="datetime-local" class="form-control {{$errors->has('visit_date')?'error':''}}" name="visit_date" required="">
                      @if ($errors->has('visit_date'))
            			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('visit_date') }}</p>
            		  @endif
                    </div>
    	            <div class="col-md-6 form-group">
                        <label>Location*</label>
                        <input type="text" class="form-control {{$errors->has('location')?'error':''}}" name="location" placeholder="(In office or Factory visit)" required="">
                        @if ($errors->has('location'))
            			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('location') }}</p>
            		  @endif
                    </div>
                    <div class="col-md-6 form-group">
        			    <label for="host">Assignee*</label>
                        <select class="select2" data-placeholder="Select Assignee" name="assignee" required="">
                            <option></option>
                            @foreach($users as $user)
                            <option value="{{$user->id}}" >{{$user->name}}</option>
                            @endforeach
                        </select>
        				@if ($errors->has('assignee'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('assignee') }}</p>
        				@endif
                 	</div>
        	   		<div class="col-md-6 form-group">
        			    <label for="host">Company*</label>
                        <select class="select22" data-placeholder="Select Company" name="company" required="">
                            <option></option>
                            @foreach($companies as $company)
                            <option value="{{$company->id}}" >{{$company->factory_name}}</option>
                            @endforeach
                        </select>
        				@if ($errors->has('company'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('company') }}</p>
        				@endif
                 	</div>
             	</div>
             	<div class="form-group">
    			<label for="name">Description</label>
    			<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description"></textarea>
    			@if ($errors->has('description'))
    			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
    			@endif
         	</div>
             	<div class="row">
                    <div class="col-md-6 form-group">
        			    <label for="status">Status* </label>
                        <select class="form-control" name="status" required="">
                            <option value="">Select Type</option>
                            <option value="Not Potential">Not Potential</option>
                            <option value="Potential">Potential</option>
                            <option value="Very Potential">Very Potential</option>
                        </select>
        				@if ($errors->has('status'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('status') }}</p>
        				@endif
                 	</div>
                 	<div class="col-md-6 form-group">
                        <label for="name">Attachment(Image)</label>
            	        <input type="file" name="attachment" class="form-control" accept="image/*" style="padding: 3px;">
                        @if ($errors->has('attachment'))
            			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('attachment') }}</p>
            		  @endif
                    </div>
                </div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Visit</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>

<!--Edit Modal -->
@foreach($visits as $i=>$data)
 <div class="modal fade text-left" id="EditDesignations_{{$data->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.visitsAction',['update',$data->id])}}" method="post" enctype="multipart/form-data">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Edit Visit</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
             	<div class="row">
             	    <div class="col-md-6 form-group">
                      <label>Visit Date*</label>
                      <input type="datetime-local" class="form-control {{$errors->has('visit_date')?'error':''}}" name="visit_date"  required="" value="{{ old('visit_date', Carbon\Carbon::parse($data->visit_date)->format('Y-m-d\TH:i')) }}" > 
                      @if ($errors->has('visit_date'))
            			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('visit_date') }}</p>
            		  @endif
                    </div>
    	            <div class="col-md-6 form-group">
                        <label>Location*</label>
                        <input type="text" class="form-control {{$errors->has('location')?'error':''}}" name="location" placeholder="(In office or Factory visit)" required="" value="{{ old('location', $data->location) }}" >
                        @if ($errors->has('location'))
            			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('location') }}</p>
            		  @endif
                    </div>
        	   		<div class="col-md-6 form-group">
        			    <label for="host">Assignee*</label>
                        <select class="select2_{{$data->id}}" data-placeholder="Select Assignee" name="assignee" required="">
                            <option></option>
                            @foreach($users as $user)
                            <option value="{{$user->id}}" {{ old('assignee', $data->assignby_id ?? '') == $user->id ? 'selected' : '' }} >{{$user->name}}</option>
                            @endforeach
                        </select>
        				@if ($errors->has('assignee'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('assignee') }}</p>
        				@endif
                 	</div>
        	   		<div class="col-md-6 form-group">
        			    <label for="host">Company*</label>
                        <select class="select22_{{$data->id}}" data-placeholder="Select Company" name="company" required="">
                            <option></option>
                            @foreach($companies as $company)
                            <option value="{{$company->id}}" {{ old('company', $data->src_id ?? '') == $company->id ? 'selected' : '' }} >{{$company->factory_name}}</option>
                            @endforeach
                        </select>
        				@if ($errors->has('company'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('company') }}</p>
        				@endif
                 	</div>
             	</div>
    			 <div class="form-group">
    				<label for="name">Description</label>
					<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description">{{old('description', $data->description ?? '')}}</textarea>
					@if ($errors->has('description'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
					@endif
             	</div>
             	<div class="row">
             	    <div class="col-md-6 form-group">
        			    <label for="status">Status* </label>
                        <select class="form-control" name="status">
                            <option value="">Select Status</option>
                            <option value="Not Potential" {{$visit->status=='Not Potential'?'selected':''}} >Not Potential</option>
                            <option value="Potential" {{$visit->status=='Potential'?'selected':''}} >Potential</option>
                            <option value="Very Potential" {{$visit->status=='Very Potential'?'selected':''}} >Very Potential</option>
                        </select>
        				@if ($errors->has('status'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('status') }}</p>
        				@endif
                 	</div>
                 	<div class="col-md-6 form-group">
        				<label for="name">Attachment</label>
        			    <input type="file" name="attachment" class="form-control" accept="image/*" style="padding: 3px;">
        			    @if ($errors->has('attachment'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('attachment') }}</p>
        				@endif
        			</div>
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Update Visit</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>
@endforeach



@endsection 
@push('js') 

<script>
    $(document).ready(function(){
        
        $('.select2').select2({
            dropdownParent: $('#AddDesignations'),
            placeholder: $('.select2').data('placeholder'),
            width: '100%',
            allowClear: true
        });
        $('.select22').select2({
            dropdownParent: $('#AddDesignations'),
            placeholder: $('.select22').data('placeholder'),
            width: '100%',
            allowClear: true
        });
        @foreach($visits as $i=>$data)
        $('.select2_{{$data->id}}').select2({
            dropdownParent: $('#EditDesignations_{{$data->id}}'),
            placeholder: $('.select2_{{$data->id}}').data('placeholder'),
            width: '100%',
            allowClear: true
        });
        $('.select22_{{$data->id}}').select2({
            dropdownParent: $('#EditDesignations_{{$data->id}}'),
            placeholder: $('.select22_{{$data->id}}').data('placeholder'),
            width: '100%',
            allowClear: true
        });
        @endforeach
    });
</script>

@endpush

