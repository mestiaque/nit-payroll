<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConvenienceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class ConvenienceController extends Controller
{
    /**
     * Display a listing of convenience requests.
     */
    public function index(Request $request)
    {
        $hasPaymentStatusColumn = $this->hasColumn('payment_status');
        $filters = $this->adminFilters($request);
        $requests = $this->adminQuery($filters, $hasPaymentStatusColumn)
            ->paginate(20)
            ->appends($request->query());

        $users = User::where('status', 1)
            ->filterBy('employee')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.convenience.index', compact('requests', 'filters', 'users', 'hasPaymentStatusColumn'));
    }

    public function print(Request $request)
    {
        $hasPaymentStatusColumn = $this->hasColumn('payment_status');
        $filters = $this->adminFilters($request);
        $requests = $this->adminQuery($filters, $hasPaymentStatusColumn)->get();

        return view('admin.convenience.print', compact('requests', 'filters', 'hasPaymentStatusColumn'));
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
            'amount' => 'required|numeric|min:0',
            'from_location' => 'nullable|string|max:255',
            'to_location' => 'nullable|string|max:255',
            'travel_by' => 'nullable|string|max:100',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'reason' => 'nullable|string|max:1000',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('conveyance/attachments', 'public');
        }

        ConvenienceRequest::create([
            'user_id' => $request->user_id,
            'type' => 'conveyance',
            'amount' => $request->amount,
            'from_location' => $request->from_location,
            'to_location' => $request->to_location,
            'travel_by' => $request->travel_by,
            'attachment' => $attachmentPath,
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
        $data = [
            'status' => $request->status,
            'admin_remark' => $request->admin_remark,
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ];

        if ($this->hasColumn('payment_status')) {
            $paymentStatus = $convenience->payment_status;
            if ($request->status === 'approved' && !$paymentStatus) {
                $paymentStatus = 'unpaid';
            }
            $data['payment_status'] = $paymentStatus;
        }

        $convenience->update($data);

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
        if (!$this->hasColumn('payment_status')) {
            return back()->with('error', 'Payment status column is not available in database.');
        }

        $request->validate([
            'payment_method' => 'nullable|in:cash,bank,mobile_banking',
            'payment_note' => 'nullable|string|max:1000',
        ]);

        $convenience = ConvenienceRequest::findOrFail($id);

        if ($convenience->status !== 'approved') {
            return back()->with('error', 'Only approved requests can be marked as paid.');
        }

        $data = [
            'payment_status' => 'paid',
        ];

        if ($this->hasColumn('payment_method')) {
            $data['payment_method'] = $request->input('payment_method', 'cash');
        }
        if ($this->hasColumn('payment_note')) {
            $data['payment_note'] = $request->payment_note;
        }
        if ($this->hasColumn('paid_by')) {
            $data['paid_by'] = auth()->id();
        }
        if ($this->hasColumn('paid_at')) {
            $data['paid_at'] = Carbon::now();
        }

        $convenience->update($data);

        return back()->with('success', 'Convenience payment status updated successfully.');
    }

    protected function adminFilters(Request $request): array
    {
        $hasPaymentStatusColumn = $this->hasColumn('payment_status');

        return [
            'employee_id' => $request->input('employee_id'),
            'type' => $request->input('type'),
            'status' => $request->input('status'),
            'payment_status' => $hasPaymentStatusColumn ? $request->input('payment_status') : null,
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];
    }

    protected function adminQuery(array $filters, bool $hasPaymentStatusColumn)
    {
        return ConvenienceRequest::with('user')
            ->when($filters['employee_id'], function ($query, $employeeId) {
                $query->where('user_id', $employeeId);
            })
            ->when($filters['type'], function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($filters['status'], function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($hasPaymentStatusColumn && $filters['payment_status'], function ($query, $paymentStatus) {
                if ($paymentStatus === 'none') {
                    $query->whereNull('payment_status');
                    return;
                }

                $query->where('payment_status', $paymentStatus);
            })
            ->when($filters['date_from'], function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($filters['date_to'], function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest();
    }

    protected function hasColumn(string $column): bool
    {
        return Schema::hasColumn('convenience_requests', $column);
    }
}
