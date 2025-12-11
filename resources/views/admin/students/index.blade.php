@extends('admin.layout')

@php
    use Carbon\Carbon;
@endphp

@section('content')
    <div class="p-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
            <h2 class="text-3xl font-bold text-gray-800">
                Students Management
            </h2>
           
            {{-- Total Students Badge (Cool Color) --}}
            <div style="background-color: rgb(5, 49, 131)" class="mt-2 sm:mt-0 text-white text-sm font-semibold px-4 py-2 rounded-lg shadow-md">
                Total Students: {{ $students->total() ?? count($students) }}
            </div>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Filters & Counts Card --}}
<div class="mb-4 p-4 bg-white shadow rounded-lg border border-gray-200">
    <form method="GET" action="{{ route('admin.students.index') }}" class="flex flex-wrap gap-3 items-center">

        <input type="text" name="mobile_number" value="{{ request('mobile_number') }}"
            placeholder="Search by student mobile"
            class="border-gray-300 rounded-lg shadow-sm p-2 text-sm focus:ring-blue-400 focus:border-blue-400 flex-1">

        <select name="payment_status"
            class="border-gray-300 rounded-lg shadow-sm p-2 text-sm focus:ring-blue-400 focus:border-blue-400">
            <option value="">All Payments</option>
            <option value="Paid" {{ request('payment_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
            <option value="Pending" {{ request('payment_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
        </select>

        {{-- Filter by Batch Day --}}
        <select name="batch_day"
            class="border-gray-300 rounded-lg shadow-sm p-2 text-sm focus:ring-blue-400 focus:border-blue-400">
            <option value="">All Days</option>
            @foreach ($batchDays as $day)
                <option value="{{ $day }}" {{ request('batch_day') == $day ? 'selected' : '' }}>{{ $day }}</option>
            @endforeach
        </select>

        {{-- Filter by Batch Time --}}
        <select name="batch_time"
            class="border-gray-300 rounded-lg shadow-sm p-2 text-sm focus:ring-blue-400 focus:border-blue-400">
            <option value="">All Times</option>
            @foreach ($batchTimes as $time)
                <option value="{{ $time }}" {{ request('batch_time') == $time ? 'selected' : '' }}>{{ $time }}</option>
            @endforeach
        </select>

        <button type="submit"
            class="bg-blue-500 text-white px-5 py-2 text-sm font-medium rounded-lg shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 transition">
            Filter
        </button>
    </form>

    {{-- Payment Counts --}}
    <div class="mt-4 text-sm text-gray-600">
        <span class="mr-6">Total Paid: <strong class="text-gray-900">{{ $paidCount ?? 0 }}</strong></span>
        <span>Total Pending: <strong class="text-gray-900">{{ $pendingCount ?? 0 }}</strong></span>
    </div>
</div>


        {{-- Add Button --}}
        <div class="flex justify-between mb-4">
            <form id="moveToExForm" method="POST" action="{{ route('admin.students.moveToEx') }}">
        @csrf
        <button type="submit"
            onclick="return confirm('Move selected students to Ex Students group?')"
            style="background-color: rgb(212, 88, 5)"
            class="text-white px-4 py-2 text-sm font-semibold rounded-lg shadow hover:from-red-600 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-400 transition">
            Move to Ex Students
        </button>
    </form>
            <a href="{{ route('admin.students.create') }}"
                class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 transition">
                + Add New Student
            </a>
        </div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3">
    <div class="flex items-center gap-3 mb-3 sm:mb-0">
        <label class="flex items-center space-x-2 text-sm text-gray-700">
            <input type="checkbox" id="selectAll" class="rounded border-gray-300">
            <span>Select All</span>
        </label>
    </div>

    
</div>

<script>
    // Select All Checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Form submission check
    document.getElementById('moveToExForm').addEventListener('submit', function(e) {
        const selected = Array.from(document.querySelectorAll('.student-checkbox:checked'))
            .map(cb => cb.value);

        if (selected.length === 0) {
            e.preventDefault();
            alert('Please select at least one student.');
            return;
        }

        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'student_ids[]';
            input.value = id;
            this.appendChild(input);
        });
    });
</script>

        {{-- Table --}}
        <div class="overflow-x-auto bg-white shadow rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobile</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">G_Mobile</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Year</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch  and Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($students as $student)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox">
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $student->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $student->mobile_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $student->guardian_mobile_number ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $student->gender }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $student->exam_year ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                                {{ $student->batchDay->days ?? 'N/A' }} <br> 
                                <span class="{{ $student->batchTime ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $student->batchTime->time ?? 'Not Assigned' }}
                                </span>
                            </td>



                            {{-- PAYMENT STATUS --}}
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @php
                                    $joinMonth = $student->joining_month ?? null;
                                    $monthsFromJoin = [];

                                    if ($joinMonth) {
                                        try {
                                            $start = Carbon::createFromFormat('Y-m', $joinMonth)->startOfMonth();
                                            $now = Carbon::now()->startOfMonth();
                                            while ($start <= $now) {
                                                $monthsFromJoin[$start->format('F Y')] = $start->format('M');
                                                $start->addMonth();
                                            }
                                        } catch (\Throwable $e) {
                                            $monthsFromJoin = [
                                                'January' => 'Jan', 'February' => 'Feb', 'March' => 'Mar', 'April' => 'Apr',
                                                'May' => 'May', 'June' => 'Jun', 'July' => 'Jul', 'August' => 'Aug',
                                                'September' => 'Sep', 'October' => 'Oct', 'November' => 'Nov', 'December' => 'Dec'
                                            ];
                                        }
                                    }

                                    $paidMonths = $student->payments->where('type', 'monthly')->where('status', 'Paid')->pluck('month')->toArray();
                                    $totalMonths = count($monthsFromJoin);
                                    $paidCount = 0;
                                    $admissionPaid = $student->payments->where('type', 'admission')->where('status', 'Paid')->first();

                                    foreach ($monthsFromJoin as $full => $short) {
                                        if (in_array($full, $paidMonths)) $paidCount++;
                                    }

                                    $overallStatus = (!$admissionPaid || $paidCount < $totalMonths) ? 'Pending' : 'Paid';
                                @endphp

                                {{-- Overall Status Badge --}}
                                @if ($overallStatus == 'Paid')
                                    <span class="inline-block bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Paid</span>
                                @else
                                    <span class="inline-block bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Pending</span>
                                @endif

                                {{-- Details --}}
                                <div class="mt-1.5 text-xs text-gray-500 space-y-1">
                                    <div>
                                        <span>Admission:</span>
                                        <span class="font-medium {{ $admissionPaid ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $admissionPaid ? 'Paid' : 'Pending' }}
                                        </span>
                                    </div>

                                    <div>
                                        <span>Monthly:</span>
                                        <span class="font-medium text-gray-800">{{ $paidCount }}/{{ $totalMonths }}</span>
                                    </div>

                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @foreach ($monthsFromJoin as $full => $short)
                                            <span class="{{ in_array($full, $paidMonths) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }} text-[10px] font-medium px-1.5 py-0.5 rounded">
                                                {{ $short }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </td>

                            {{-- ACTIONS --}}
                            <td class="px-4 py-3 text-center text-sm font-medium whitespace-nowrap">
                                <a href="{{ route('admin.students.edit', $student->id) }}" class="text-blue-600 hover:text-blue-800 mr-3">✏️ Edit</a>
                                <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Are you sure you want to delete this student? This action cannot be undone.')"
                                        class="text-red-600 hover:text-red-800 mt-2 block">
                                        🗑️ Delete
                                    </button>
                                </form>
                                <a href="{{ route('admin.students.pdf', $student->id) }}"
                                    class="inline-block mt-2 px-3 py-1 bg-blue-400 text-white text-xs font-medium rounded shadow hover:bg-blue-500 transition">
                                    🧾 PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="px-4 py-6 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No students found</h3>
                                    <p class="mt-1 text-sm text-gray-500">Get started by adding a new student.</p>
                                    <div class="mt-4">
                                        <a href="{{ route('admin.students.create') }}"
                                            class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 transition">
                                            + Add New Student
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($students->hasPages())
            <div class="mt-6">
                {{ $students->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
@endsection
