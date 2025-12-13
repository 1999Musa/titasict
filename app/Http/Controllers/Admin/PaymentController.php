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
use App\Services\AlphaSmsService;



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
    // If student exists, use id; otherwise use 0 or 'deleted'
    $studentId = $payment->student_id ?? 0;
    return $studentId . '|' . $payment->created_at->format('Y-m-d H:i:s');
});


        // Build an array of display rows from groups
        $rows = $grouped->map(function ($items, $groupKey) {
    $first = $items->first();

    // Skip if student deleted
    if (!$first->student) {
        return null;
    }

    $types = $items->pluck('type')->unique()->values()->all();
    $months = $items->where('type', 'monthly')->pluck('month')->filter()->unique()->values()->all();
    $amountSum = $items->first()->amount;
    $status = $items->pluck('status')->unique()->count() === 1
        ? $items->first()->status
        : ($items->contains(fn($p) => $p->status !== 'Paid') ? 'Pending' : 'Paid');

    return (object) [
        'student' => $first->student,
        'types' => $types,
        'months' => $months,
        'amount' => $amountSum,
        'status' => $status,
        'created_at' => $first->created_at,
        'ids' => $items->pluck('id')->toArray(),
        'primary_id' => $first->id,
    ];
})->filter()->values(); // filter removes nulls


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
        'payment_type' => 'required|array|min:1',
        'payment_type.*' => 'string',
        'amount' => 'required|numeric|min:0',
        'status' => 'required|string',
        'send_sms' => 'nullable|boolean',
    ]);

    $student = Student::findOrFail($data['student_id']);

    foreach ($data['payment_type'] as $typeOrMonth) {
        Payment::create([
            'student_id' => $data['student_id'],
            'type' => ($typeOrMonth === 'admission') ? 'admission' : 'monthly',
            'month' => ($typeOrMonth === 'admission') ? null : $typeOrMonth,
            'amount' => $data['amount'],
            'status' => $data['status'],
        ]);
    }

    $apiKey = env('ALPHA_SMS_API_KEY');
$sender = env('ALPHA_SMS_SENDER_ID');

\Log::info('SMS API KEY', ['key' => $apiKey]);

    \Log::info('SMS checkbox value', [
    'send_sms' => $request->send_sms,
    'status' => $data['status']
]);

\Log::info('SMS API KEY', [
    'key' => config('services.alpha_sms.api_key')
]);

    $student = Student::findOrFail($data['student_id']);

$paidItems = collect($data['payment_type'])
    ->map(fn ($v) => $v === 'admission' ? 'Admission Fee' : $v)
    ->implode(', ');

$smsText = "Dear {$student->name}, your payment for {$paidItems} has been received. Thank you.";


if ($request->send_sms) {
    // Normalize mobile number
    $studentNumber = $this->normalizeMobile($student->mobile_number);


    $studentName = $student->name;
    $monthsPaid = implode(', ', $data['payment_type']);
    $amount = $request->amount;

    $smsText = "Hello {$studentName}, Payment received for: {$monthsPaid}. Amount: {$amount} BDT. Thank you.";

    $apiKey = env('ALPHA_SMS_API_KEY');
    $sender = env('ALPHA_SMS_SENDER_ID');

    try {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.sms.net.bd/sendsms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'api_key' => $apiKey,
                'to' => $studentNumber,
                'msg' => $smsText,
                // 'sender_id' => $sender,
            ],
            CURLOPT_CAINFO => "C:\\wamp64\\php\\extras\\ssl\\cacert.pem", // SSL CA
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($curl);
        if ($err = curl_error($curl)) {
            \Log::error('SMS Exception', ['error' => $err]);
        } else {
            \Log::info('SMS Response', ['response' => $response]);
        }
        curl_close($curl);
    } catch (\Throwable $e) {
        \Log::error('SMS Exception', ['error' => $e->getMessage()]);
    }
}




    return redirect()
        ->route('admin.payments.index')
        ->with('success', 'Payment(s) added successfully.');
}


    public function edit(Payment $payment)
    {
        $students = Student::orderBy('name')->get();

        return view('admin.payments.edit', compact('payment', 'students'));
    }

public function update(Request $request, Payment $payment)
{
    $data = $request->validate([
        'payment_type' => 'required|array|min:1',
        'payment_type.*' => 'string',
        'student_id' => 'required|exists:students,id',
        'amount' => 'required|numeric|min:0',
        'status' => 'required|string',
    ]);

    // 1️⃣ Identify the PAYMENT GROUP of the item being edited
    $groupIds = Payment::where('student_id', $payment->student_id)
                ->where('created_at', $payment->created_at)
                ->pluck('id');

    // 2️⃣ Delete all OLD entries in this group
    Payment::whereIn('id', $groupIds)->delete();

    // 3️⃣ Re-create payments EXACTLY based on updated user selections
    foreach ($data['payment_type'] as $typeOrMonth) {

        Payment::create([
            'student_id' => $data['student_id'],
            'type'       => ($typeOrMonth === 'admission') ? 'admission' : 'monthly',
            'month'      => ($typeOrMonth === 'admission') ? null : $typeOrMonth,
            'amount'     => $data['amount'],
            'status'     => $data['status'],
            'created_at' => $payment->created_at, // keep grouping consistent
        ]);
    }

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

public function getMonths(Student $student, Request $request): JsonResponse
{
    try {
        if (!$student->joining_month) {
            return response()->json([
                'include_admission' => true,
                'unpaid_months' => [],
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

        // All months from joining to now
        $months = [];
        $temp = $join->copy();
        while ($temp <= $end) {
            $months[] = $temp->format('F Y');
            $temp->addMonth();
        }

        // Paid payments
        $paidPayments = Payment::where('student_id', $student->id)
            ->where('status', 'Paid')
            ->get();

        $admissionPaid = $paidPayments->contains('type', 'admission');
        $paidMonths = $paidPayments->where('type', 'monthly')->pluck('month')->toArray();

        // Unpaid months
        $unpaidMonths = array_diff($months, $paidMonths);

        // For edit, return all months but mark paid/unpaid
        if ($request->filled('for_edit')) {
            return response()->json([
                'include_admission' => !$admissionPaid,
                'paid_months' => $paidMonths,
                'unpaid_months' => array_values($unpaidMonths),
            ]);
        }

        return response()->json([
            'include_admission' => !$admissionPaid,
            'unpaid_months' => array_values($unpaidMonths),
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
    ]);

    $student = Student::with(['payments' => function($q) use ($data) {
        $q->whereIn('month', array_filter($data['payment_type'], fn($v) => $v !== 'admission'))
          ->orWhere('type', 'admission');
    }])->findOrFail($data['student_id']);

    $payments = $student->payments
        ->filter(function($p) use ($data) {
            if ($p->type === 'admission') return in_array('admission', $data['payment_type']);
            return in_array($p->month, $data['payment_type']);
        });

    $pdf = Pdf::loadView('admin.payments.pdf', compact('student', 'payments'));
    $fileName = 'Payment_' . str_replace(' ', '_', $student->name) . '.pdf';

    return $pdf->download($fileName);
}

private function buildPaymentSms(Student $student, array $paymentTypes, float $amount, string $status): string
{
    $Company = "TITAS ENTERPRISE"; // <-- Your brand/company name

    $items = [];

    foreach ($paymentTypes as $p) {
        if ($p === 'admission') {
            $items[] = 'Admission Fee';
        } else {
            $items[] = $p;
        }
    }

    $itemsText = implode(', ', $items);

    return "{$Company}: Dear {$student->name}, Payment received successfully for {$itemsText}. "
        . "Amount: {$amount} BDT. Status: {$status}. Thank you!";
}


private function normalizeMobile(string $number): string
{
    // Remove non-numeric characters
    $number = preg_replace('/\D/', '', $number);

    // If number starts with 0, replace with 88
    if (str_starts_with($number, '0')) {
        return '88' . ltrim($number, '0');
    }

    // Already starts with 880
    if (str_starts_with($number, '880')) {
        return $number;
    }

    return $number;
}




}
