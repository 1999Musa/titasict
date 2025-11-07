<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\BatchDay;
use App\Models\BatchTime;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $mobile = $request->mobile_number;
        $status = $request->payment_status; // Paid, Pending, or null

        $students = Student::with(['batchDay', 'batchTime', 'payments']);

        // Filter by mobile number
        if ($mobile) {
            $students->where('mobile_number', 'like', "%{$mobile}%");
        }

        // Filter by last payment status
        if ($status) {
            $students->whereHas('payments', function ($q) use ($status) {
                $q->where('status', $status)
                    ->whereIn('id', function ($sub) {
                        // Only take latest payment for each student
                        $sub->selectRaw('MAX(id)')
                            ->from('payments')
                            ->groupBy('student_id');
                    });
            });
        }

        // Count Paid and Pending
        $allStudents = Student::with('payments')->get();
        $paidCount = $allStudents->filter(function ($s) {
            $lastPayment = $s->payments->sortByDesc('created_at')->first();
            return $lastPayment && $lastPayment->status === 'Paid';
        })->count();

        $pendingCount = $allStudents->filter(function ($s) {
            $lastPayment = $s->payments->sortByDesc('created_at')->first();
            return !$lastPayment || $lastPayment->status !== 'Paid';
        })->count();

        $students = $students->latest()->paginate(10)->withQueryString();

        return view('admin.students.index', compact('students', 'paidCount', 'pendingCount'));
    }

    public function create()
    {
        // Load batch days with their times (for human-readable labels)
        $batchDays = BatchDay::with('times')->orderBy('id', 'desc')->get();

        // Also pass a flat list of all batch times with their batchDay relation (used by the select)
        $batchTimes = BatchTime::with('batchDay')->orderBy('time')->get();

        $genders = ['Male', 'Female'];

        // Generate list of months for joining_month field
        $months = collect(range(0, 11))->map(function ($i) {
            return now()->startOfYear()->addMonths($i)->format('Y-m');
        });

        return view('admin.students.create', compact('batchDays', 'batchTimes', 'genders', 'months'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|unique:students,mobile_number',
            'guardian_mobile_number' => 'required|string',
            'gender' => 'required|in:Male,Female',
            'exam_year' => 'nullable|string|max:10',
            'batch_day_id' => 'required|exists:batch_days,id',
            'batch_time_id' => 'required|exists:batch_times,id',
            'joining_month' => 'required|date_format:Y-m',
        ]);

        Student::create($data);

        return redirect()->route('admin.students.index')->with('success', 'Student created successfully.');
    }

    public function edit(Student $student)
    {
        $batchDays = BatchDay::with('times')->orderBy('id', 'desc')->get();
        $batchTimes = BatchTime::with('batchDay')->orderBy('time')->get();
        $genders = ['Male', 'Female'];

        // Generate months again for the dropdown
        $months = collect(range(0, 11))->map(function ($i) {
            return now()->startOfYear()->addMonths($i)->format('Y-m');
        });

        return view('admin.students.edit', compact('student', 'batchDays', 'batchTimes', 'genders', 'months'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'batch_day_id' => 'required|exists:batch_days,id',
            'batch_time_id' => 'required|exists:batch_times,id',
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|unique:students,mobile_number,' . $student->id,
            'guardian_mobile_number' => 'required|string|max:20',
            'gender' => 'required|in:Male,Female',
            'exam_year' => 'nullable|string|max:10',
            'joining_month' => 'required|date_format:Y-m',
        ]);

        $student->update($data);

        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return back()->with('success', 'Student deleted successfully.');
    }

    public function generatePdf(Student $student)
{
    $student->load(['batchDay', 'batchTime', 'payments']); // eager load relations

    // Load the Blade view into PDF
    $pdf = Pdf::loadView('admin.students.pdf', compact('student'));

    $fileName = 'Student_' . str_replace(' ', '_', $student->name) . '.pdf';

    return $pdf->download($fileName);
}
}



















// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\Student;
// use App\Models\BatchDay;
// use App\Models\BatchTime;

// class StudentController extends Controller
// {
//     public function index(Request $request)
//     {
//         $mobile = $request->mobile_number;
//         $status = $request->payment_status; // Paid, Pending, or null

//         $students = Student::with(['batchDay', 'batchTime', 'payments']);

//         // Filter by mobile number
//         if ($mobile) {
//             $students->where('mobile_number', 'like', "%{$mobile}%");
//         }

//         // Filter by last payment status
//         if ($status) {
//             $students->whereHas('payments', function ($q) use ($status) {
//                 $q->where('status', $status)
//                     ->whereIn('id', function ($sub) {
//                         // Only take latest payment for each student
//                         $sub->selectRaw('MAX(id)')
//                             ->from('payments')
//                             ->groupBy('student_id');
//                     });
//             });
//         }

//         // Count Paid and Pending
//         $allStudents = Student::with('payments')->get();
//         $paidCount = $allStudents->filter(function ($s) {
//             $lastPayment = $s->payments->sortByDesc('created_at')->first();
//             return $lastPayment && $lastPayment->status === 'Paid';
//         })->count();

//         $pendingCount = $allStudents->filter(function ($s) {
//             $lastPayment = $s->payments->sortByDesc('created_at')->first();
//             return !$lastPayment || $lastPayment->status !== 'Paid';
//         })->count();

//         $students = $students->latest()->paginate(10)->withQueryString();

//         return view('admin.students.index', compact('students', 'paidCount', 'pendingCount'));
//     }



//     public function create()
//     {
//         // load batch days with their times (for human-readable labels)
//         $batchDays = BatchDay::with('times')->orderBy('id', 'desc')->get();

//         // also pass a flat list of all batch times with their batchDay relation (used by the select)
//         $batchTimes = BatchTime::with('batchDay')->orderBy('time')->get();

//         $genders = ['Male', 'Female'];

//         return view('admin.students.create', compact('batchDays', 'batchTimes', 'genders'));
//     }

//     public function store(Request $request)
//     {
//         $data = $request->validate([
//             'name' => 'required|string|max:255',
//             'mobile_number' => 'required|string|unique:students,mobile_number',
//             'guardian_mobile_number' => 'required|string',
//             'gender' => 'required|in:Male,Female',
//             'exam_year' => 'nullable|string|max:10',
//             'batch_day_id' => 'required|exists:batch_days,id',
//             'batch_time_id' => 'required|exists:batch_times,id',
//         ]);


//         Student::create($data);

//         return redirect()->route('admin.students.index')->with('success', 'Student created successfully.');
//     }

//     public function edit(Student $student)
//     {
//         $batchDays = BatchDay::with('times')->orderBy('id', 'desc')->get();
//         $batchTimes = BatchTime::with('batchDay')->orderBy('time')->get();
//         $genders = ['Male', 'Female'];

//         return view('admin.students.edit', compact('student', 'batchDays', 'batchTimes', 'genders'));
//     }
//     public function update(Request $request, Student $student)
//     {
//         $data = $request->validate([
//             'batch_day_id' => 'required|exists:batch_days,id',
//             'batch_time_id' => 'required|exists:batch_times,id',
//             'name' => 'required|string|max:255',
//             'mobile_number' => 'required|string|unique:students,mobile_number,' . $student->id,
//             'guardian_mobile_number' => 'required|string|max:20',
//             'gender' => 'required|in:Male,Female',
//             'exam_year' => 'nullable|string|max:10',
//         ]);

//         $student->update($data);

//         return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
//     }

//     public function destroy(Student $student)
//     {
//         $student->delete();
//         return back()->with('success', 'Student deleted successfully.');
//     }
// }
