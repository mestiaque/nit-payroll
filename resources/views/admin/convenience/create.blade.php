@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Add Convenience Request') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Add Convenience Request</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.convenience.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Employee</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">Select Employee</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>From</label>
                        <input type="text" name="from_location" class="form-control" placeholder="From location">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>To</label>
                        <input type="text" name="to_location" class="form-control" placeholder="To location">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Travel By</label>
                        <select name="travel_by" class="form-control">
                            <option value="">-- Select --</option>
                            <option value="Rikshaw">Rikshaw</option>
                            <option value="Bus">Bus</option>
                            <option value="Ride Sharing: Car">Ride Sharing: Car</option>
                            <option value="Ride Sharing: Bike">Ride Sharing: Bike</option>
                            <option value="Personal Vehicle">Personal Vehicle</option>
                            <option value="Metro Rail">Metro Rail</option>
                            <option value="Bus & Rikshaw">Bus & Rikshaw</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Attachment</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label>Reason</label>
                        <textarea name="reason" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Amount</label>
                        <input type="number" name="amount" class="form-control" step="0.01" required>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('admin.convenience.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@endsection
