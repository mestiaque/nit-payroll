<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConvenienceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ConvenienceController extends Controller
{
    /**
     * Display a listing of convenience requests.
     */
    public function index(Request $request)
    {
        $pendingRequests = ConvenienceRequest::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $completeRequests = ConvenienceRequest::with('user')
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.convenience.index', compact('pendingRequests', 'completeRequests'));
    }

    /**
     * Show the form for creating a new request.
     */
    public function create()
    {
        $users = User::where('status', 1)->filterBy('employee')->get();
        return view('admin.convenience.create', compact('users'));
    }

    /**
     * Store a newly created request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required',
            'amount' => 'required|numeric|min:0',
            'reason' => 'nullable',
        ]);

        ConvenienceRequest::create([
            'user_id' => $request->user_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.convenience.index')->with('success', 'Request submitted successfully');
    }

    /**
     * Approve or reject the request.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_remark' => 'nullable|required_if:status,rejected|string|max:1000',
        ]);

        $convenience = ConvenienceRequest::findOrFail($id);
        $paymentStatus = $convenience->payment_status;
        if ($request->status === 'approved' && !$paymentStatus) {
            $paymentStatus = 'unpaid';
        }
        $convenience->update([
            'status' => $request->status,
            'admin_remark' => $request->admin_remark,
            'payment_status' => $paymentStatus,
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Request updated successfully');
    }

    /**
     * Remove the request.
     */
    public function destroy($id)
    {
        ConvenienceRequest::findOrFail($id)->delete();
        return redirect()->route('admin.convenience.index')->with('success', 'Request deleted successfully');
    }

    /**
     * Mark approved convenience request as paid.
     */
    public function markPayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,bank,mobile_banking',
            'payment_note' => 'nullable|string|max:1000',
        ]);

        $convenience = ConvenienceRequest::findOrFail($id);

        if ($convenience->status !== 'approved') {
            return back()->with('error', 'Only approved requests can be marked as paid.');
        }

        $convenience->update([
            'payment_status' => 'paid',
            'payment_method' => $request->payment_method,
            'payment_note' => $request->payment_note,
            'paid_by' => auth()->id(),
            'paid_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Convenience request marked as paid.');
    }
}
