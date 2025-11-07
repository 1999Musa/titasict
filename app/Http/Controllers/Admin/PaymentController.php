<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('student')->latest();

        // Filter by student mobile number
        if ($request->filled('mobile_number')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('mobile_number', 'like', '%' . $request->mobile_number . '%');
            });
        }

        $monthlyEarnings = Payment::where('status', 'Paid')
            // Ignore payments where month is null or the '—' placeholder
            ->whereNotNull('month')
            ->where('month', '!=', '—')
            ->select(
                'month', // Select the month string itself (e.g., "October 2025")
                DB::raw('SUM(amount) as total_earnings')
            )
            ->groupBy('month') // Group by the 'month' string
            // Order by converting the 'Month YYYY' string to a real date
            ->orderBy(DB::raw("STR_TO_DATE(CONCAT('1 ', month), '%d %M %Y')"), 'desc')
            ->get();
        // --- End Updated Query ---


        // Filter by payment status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(12)->withQueryString();

        return view('admin.payments.index', compact('payments', 'monthlyEarnings'));
    }

    public function create(Request $request)
    {
        $students = Student::orderBy('name')->get();

        $months = collect();
        if ($request->filled('student_id')) {
            $student = Student::find($request->student_id);
            if ($student && $student->joining_month) {
                $joinMonth = \Carbon\Carbon::createFromFormat('Y-m', $student->joining_month);
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
            'payment_type.*' => 'string', // each selected value
            'amount' => 'required|numeric|min:0',
            'status' => 'required|string',
        ]);

        // Loop through each selected payment type and create a separate payment record
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

        // Only take the first selection for update
        $typeOrMonth = $data['payment_type'][0];

        $payment->update([
            'student_id' => $data['student_id'],
            'type' => ($typeOrMonth === 'admission') ? 'admission' : 'monthly',
            'month' => ($typeOrMonth === 'admission') ? null : $typeOrMonth,
            'amount' => $data['amount'],
            'status' => $data['status'],
        ]);

        return redirect()->route('admin.payments.index')->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return back()->with('success', 'Payment deleted successfully.');
    }

    public function searchStudents(Request $request)
    {
        $query = $request->get('q');

        $students = Student::where('mobile_number', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->select('id', 'name', 'mobile_number')
            ->limit(10)
            ->get();

        return response()->json($students);
    }


    public function getMonths(Student $student): JsonResponse
    {
        try {
            if (!$student->joining_month) {
                return response()->json(['months' => []]);
            }

            // Parse join month safely
            $join = null;
            $jm = trim($student->joining_month);
            $formats = ['Y-m', 'Y-m-d', 'F Y', 'M Y', 'Y'];
            foreach ($formats as $fmt) {
                try {
                    $join = Carbon::createFromFormat($fmt, $jm);
                    break;
                } catch (\Throwable $e) {
                }
            }
            if (!$join) {
                try {
                    $join = Carbon::parse($jm);
                } catch (\Throwable $e) {
                    return response()->json([
                        'error' => 'Unable to parse joining month: "' . $jm . '"'
                    ], 422);
                }
            }

            $join = $join->copy()->startOfMonth();
            $end = Carbon::now()->startOfMonth();

            if ($join > $end) {
                return response()->json(['months' => []]);
            }

            // get already paid types/months for this student
            $paidTypes = Payment::where('student_id', $student->id)->pluck('type')->toArray();
            $paidMonths = Payment::where('student_id', $student->id)
                ->whereNotNull('month')
                ->pluck('month')
                ->toArray();

            $months = [];
            $cursor = $join->copy();
            while ($cursor <= $end) {
                $m = $cursor->format('F Y');
                // only include if not paid
                if (!in_array($m, $paidMonths)) {
                    $months[] = $m;
                }
                $cursor->addMonth();
            }

            // Admission fee — include only if not already paid
            $includeAdmission = !in_array('admission', $paidTypes);

            return response()->json([
                'months' => $months,
                'include_admission' => $includeAdmission,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
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

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.payments.pdf', compact('student', 'payments'));
    $fileName = 'Payment_' . str_replace(' ', '_', $student->name) . '.pdf';

    // Download the file
    return $pdf->download($fileName);
}


public function bulkDelete(Request $request)
{
    $ids = array_filter(explode(',', $request->input('selected_ids', '')));

    if (empty($ids)) {
        return redirect()->back()->with('error', 'No payments selected.');
    }

    $deletedCount = Payment::whereIn('id', $ids)->delete();

    return redirect()->back()->with('success', "{$deletedCount} payment(s) deleted successfully.");
}



}
