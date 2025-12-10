<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    /**
     * Display a grouped listing of payments.
     * Groups payments created together (same student_id + same created_at timestamp).
     */
   public function index(Request $request)
    {
        // Base query with optional filters
        $query = Payment::with('student')->orderByDesc('created_at');

        // Filter by student mobile number (works with regular GET search)
        if ($request->filled('mobile_number')) {
            $q = $request->mobile_number;
            $query->whereHas('student', function ($q2) use ($q) {
                $q2->where('mobile_number', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%");
            });
        }

        // Filter by payment status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // NOTE: We fetch a reasonable window of records and group in-app.
        $payments = $query->get();

        // Group payments by student_id + created_at exact timestamp
        $grouped = $payments->groupBy(function ($payment) {
            // Use iso format to ensure exact grouping for items created in same DB query (same timestamp)
            return $payment->student_id . '|' . $payment->created_at->format('Y-m-d H:i:s');
        });

        // Build an array of display rows from groups
        $rows = $grouped->map(function ($items, $groupKey) {
            /** @var \Illuminate\Database\Eloquent\Collection $items */
            $first = $items->first();

            $types = $items->pluck('type')->unique()->values()->all(); // ['admission','monthly']
            // collect months for monthly entries
            $months = $items->where('type', 'monthly')->pluck('month')->filter()->unique()->values()->all();

            // *** FIX 1: Use the amount from the first item, assuming it holds the total amount for the grouped transaction. ***
            $amountSum = $items->first()->amount;

            // Determine status: if ANY payment in group is Pending -> group considered Pending
            $status = $items->pluck('status')->unique()->count() === 1
                ? $items->first()->status
                : ($items->contains(function ($p) {
                    return $p->status !== 'Paid'; }) ? 'Pending' : 'Paid');

            // ids for actions
            $ids = $items->pluck('id')->toArray();

            return (object) [
                'student' => $first->student,
                'types' => $types,
                'months' => $months,
                'amount' => $amountSum,
                'status' => $status,
                'created_at' => $first->created_at,
                'ids' => $ids,
                // pick one id to use for edit/delete/print (we use the first)
                'primary_id' => $first->id,
            ];
        })->values();

        // Manual pagination for grouped rows
        $perPage = 12;
        $page = $request->get('page', 1);
        $total = $rows->count();
        $paged = $rows->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $paged,
            $total,
            $perPage,
            $page,
            ['path' => route('admin.payments.index'), 'query' => $request->query()]
        );

        // Monthly earnings calculation (same as before)
        $monthlyEarnings = Payment::where('status', 'Paid')
            ->whereNotNull('month')
            ->where('month', '!=', '—')
            ->select('month', DB::raw('SUM(amount) as total_earnings'))
            ->groupBy('month')
            ->orderBy(DB::raw("STR_TO_DATE(CONCAT('1 ', month), '%d %M %Y')"), 'desc')
            ->get();

        return view('admin.payments.index', [
            'payments' => $paginated,
            'monthlyEarnings' => $monthlyEarnings,
        ]);
    }
    public function create(Request $request)
    {
        $students = Student::orderBy('name')->get();

        $months = collect();
        if ($request->filled('student_id')) {
            $student = Student::find($request->student_id);
            if ($student && $student->joining_month) {
                $joinMonth = Carbon::createFromFormat('Y-m', $student->joining_month);
                $current = $joinMonth->copy();
                $end = now()->startOfMonth();

                while ($current <= $end) {
                    $months->push($current->format('F Y'));
                    $current->addMonth();
                }
            }
        }

        return view('admin.payments.create', compact('students', 'months'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_type' => 'required|array',
            'payment_type.*' => 'string',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|string',
        ]);

        foreach ($data['payment_type'] as $typeOrMonth) {
            Payment::create([
                'student_id' => $data['student_id'],
                'type' => ($typeOrMonth === 'admission') ? 'admission' : 'monthly',
                'month' => ($typeOrMonth === 'admission') ? null : $typeOrMonth,
                'amount' => $data['amount'],
                'status' => $data['status'],
            ]);
        }

        return redirect()->route('admin.payments.index')->with('success', 'Payment(s) added successfully.');
    }

    public function edit(Payment $payment)
    {
        $students = Student::orderBy('name')->get();

        return view('admin.payments.edit', compact('payment', 'students'));
    }

    public function update(Request $request, Payment $payment)
{
    $data = $request->validate([
        'student_id' => 'required|exists:students,id',
        'payment_type' => 'required|array',
        'payment_type.*' => 'string',
        'amount' => 'required|numeric|min:0',
        'status' => 'required|string',
    ]);

    $selected = $data['payment_type'][0];

    $payment->update([
        'student_id' => $data['student_id'],
        'type' => $selected === 'admission' ? 'admission' : 'monthly',
        'month' => $selected === 'admission' ? null : $selected,
        'amount' => $data['amount'],
        'status' => $data['status'],
    ]);

    return redirect()
        ->route('admin.payments.index')
        ->with('success', 'Payment updated successfully.');
}


   public function destroy(Payment $payment, Request $request)
    {
        // *** FIX 2: Check for group_ids from the index page delete button ***
        $group_ids = array_filter(explode(',', $request->input('group_ids', '')));

        if (!empty($group_ids)) {
            // Delete all payments in the group
            $deletedCount = Payment::whereIn('id', $group_ids)->delete();
            return back()->with('success', "{$deletedCount} payment(s) deleted successfully.");
        }

        // Fallback or default behavior: delete the single payment passed by route model binding
        $payment->delete();
        return back()->with('success', 'Payment deleted successfully.');
    }

    /**
     * Handles bulk deletion from checkboxes.
     */
    public function bulkDelete(Request $request)
    {
        $ids = array_filter(explode(',', $request->input('selected_ids', '')));

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No payments selected.');
        }

        // Note: The selected_ids already contain all grouped IDs from the checkboxes (Fix 2)
        $deletedCount = Payment::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', "{$deletedCount} payment(s) deleted successfully.");
    }

    /**
     * AJAX search endpoint for students (live search).
     * Excludes "ex" students.
     */
    public function searchStudents(Request $request)
    {
        $q = $request->get('q', '');

        if (trim($q) === '') {
            return response()->json([]);
        }

        $students = Student::where('status', 'active')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('mobile_number', 'like', "%{$q}%");
            })
            ->select('id', 'name', 'mobile_number', 'joining_month')
            ->limit(12)
            ->get();

        return response()->json($students);
    }

    // In app/Http/Controllers/Admin/PaymentController.php

public function getMonths(Student $student): JsonResponse
{
    try {
        if (!$student->joining_month) {
            return response()->json([
                'admission_paid' => false,
                'months' => [],
                'paid_months' => [],
            ]);
        }

        // Parse joining month
        $join = null;
        $formats = ['Y-m', 'Y-m-d', 'F Y', 'M Y'];
        foreach ($formats as $fmt) {
            try {
                $join = Carbon::createFromFormat($fmt, $student->joining_month);
                break;
            } catch (\Throwable $e) {}
        }
        if (!$join) {
            $join = Carbon::parse($student->joining_month);
        }

        $join = $join->startOfMonth();
        $end = now()->startOfMonth();

        // Get all months between joining and now
        $months = [];
        $temp = $join->copy();
        while ($temp <= $end) {
            $months[] = $temp->format('F Y');
            $temp->addMonth();
        }

        // Get paid data
        $paidPayments = Payment::where('student_id', $student->id)
            ->where('status', 'Paid')
            ->get();

        $admissionPaid = $paidPayments->contains('type', 'admission');

        $paidMonths = $paidPayments
            ->where('type', 'monthly')
            ->pluck('month')
            ->toArray();

        return response()->json([
            'admission_paid' => $admissionPaid,
            'months' => $months,
            'paid_months' => $paidMonths,
        ]);

    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    public function storeAndPrintPdf(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_type' => 'required|array',
            'payment_type.*' => 'string',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|string',
        ]);

        $payments = [];

        foreach ($data['payment_type'] as $typeOrMonth) {
            $payment = Payment::create([
                'student_id' => $data['student_id'],
                'type' => ($typeOrMonth === 'admission') ? 'admission' : 'monthly',
                'month' => ($typeOrMonth === 'admission') ? null : $typeOrMonth,
                'amount' => $data['amount'],
                'status' => $data['status'],
            ]);

            $payments[] = $payment;
        }

        $student = Student::with('payments')->findOrFail($data['student_id']);

        $pdf = Pdf::loadView('admin.payments.pdf', compact('student', 'payments'));
        $fileName = 'Payment_' . str_replace(' ', '_', $student->name) . '.pdf';

        return $pdf->download($fileName);
    }


}
