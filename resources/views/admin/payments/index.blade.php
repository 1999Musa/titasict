@extends('admin.layout')

@section('title', 'Payments')

@section('content')

    <!-- Monthly Earnings Section -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Earnings Overview</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($monthlyEarnings as $earnings)
                <div
                    class="bg-white p-5 rounded-lg shadow-md border border-gray-200 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="bg-emerald-100 text-emerald-600 p-3 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ $earnings->month }}</p>
                            <p class="text-2xl font-bold text-gray-800">{{ number_format($earnings->total_earnings) }} <span
                                    class="text-lg font-medium text-gray-600">tk</span></p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-6 rounded-lg shadow-md border border-gray-200 text-center">
                    <p class="text-gray-500">No paid earnings recorded yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Header -->


    <!-- Filter Form -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow-md border border-gray-200">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label for="mobile_number" class="block text-sm font-semibold text-gray-700 mb-2">Search by Mobile</label>
                <input type="text" name="mobile_number" id="mobile_number" value="{{ request('mobile_number') }}"
                    placeholder="Enter mobile..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
            </div>
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Filter by Status</label>
                <select name="status" id="status"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                    <option value="">All Status</option>
                    <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="md:self-end">
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 py-2 px-4 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Bulk Delete Button -->

    <div class="flex justify-between mb-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            {{-- <h1 class="text-2xl font-bold text-gray-800">
                All Payments
            </h1> --}}
            <a href="{{ route('admin.payments.create') }}"
                class="w-full md:w-auto flex items-center justify-center gap-2 py-2 px-4 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add New Payment
            </a>
        </div>
        <div id="bulkDeleteWrapper">
    <form id="bulkDeleteForm" method="POST" action="{{ route('admin.payments.bulkDelete') }}">
        @csrf
        @method('DELETE')

        <input type="hidden" name="selected_ids" id="selectedIds">

        <button type="button"
                id="bulkDeleteBtn"
                class="flex items-center gap-2 py-2 px-4 bg-red-600 text-white rounded-lg text-sm font-medium transition-colors shadow-sm disabled:bg-red-300 disabled:cursor-not-allowed"
                disabled>

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>

            Delete Selected
        </button>
    </form>
</div>

    </div>

    <!-- Session Message -->
    @if (session('success'))
        <div class="mb-4 p-4 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-lg shadow-sm" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="p-4">
                            <input type="checkbox" id="selectAll"
                                class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Student
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Mobile</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Type(s)
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Month(s)
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $key => $row)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4">
                                <!-- Checkboxes use the primary id; we delete individual payment IDs in the group using backend -->
                                <input type="checkbox"
                                    class="payment-checkbox h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500"
                                    value="{{ implode(',', $row->ids) }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payments->firstItem() + $key }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ optional($row->student)->name ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ optional($row->student)->mobile_number ?? '--' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 capitalize">
                                {{-- {{ implode(',', $row->types) }} --}}
                                @foreach($row->types as $type)
                                    {{ $type }},<br>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if(count($row->months) > 0)
                                    @foreach($row->months as $month)
                                        @php
                                            // Example: 'October 2025' -> ['October', '2025']
                                            $parts = explode(' ', $month);
                                            $monthName = $parts[0] ?? '';
                                            $year = $parts[1] ?? '';

                                            // Take the first 3 letters of the month
                                            $shortMonth = substr($monthName, 0, 3);

                                            // Take the last 2 digits of the year
                                            $shortYear = substr($year, -2);
                                            
                                            // Combine them: 'Oct 25'
                                            $formattedMonth = trim("{$shortMonth} '{$shortYear}");
                                        @endphp
                                        {{ $formattedMonth }}@if(!$loop->last),@endif<br>
                                    @endforeach
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ number_format($row->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $row->status == 'Paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $row->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex flex-col space-y-2">
                                    <!-- Edit uses primary_id (first payment in group) -->
                                    <a href="{{ route('admin.payments.edit', $row->primary_id) }}"
                                        class="flex w-20 items-center justify-center gap-1 py-1 px-3 bg-yellow-500 text-white rounded-lg text-xs font-medium hover:bg-yellow-600 transition-colors shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>

                                    <!-- Delete: will submit with comma-separated ids in the hidden input -->
                                    <form action="{{ route('admin.payments.destroy', $row->primary_id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete these payment(s)?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="group_ids" value="{{ implode(',', $row->ids) }}">
                                        <button type="submit"
                                            class="flex w-20 items-center justify-center gap-1 py-1 px-3 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700 transition-colors shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete
                                        </button>
                                    </form>

                                    <!-- Print all payments in this group (use storeAndPrintPdf style) -->
                                    <form action="{{ route('admin.payments.savePdf') }}" method="POST" target="_blank">
                                        @csrf
                                        <input type="hidden" name="student_id" value="{{ $row->student->id }}">
                                        @foreach($row->months as $m)
                                            <input type="hidden" name="payment_type[]" value="{{ $m }}">
                                        @endforeach
                                        @if(in_array('admission', $row->types))
                                            <input type="hidden" name="payment_type[]" value="admission">
                                        @endif
                                        <input type="hidden" name="amount" value="{{ $row->amount }}">
                                        <input type="hidden" name="status" value="{{ $row->status }}">
                                        <button type="submit"
                                            class="flex w-20 items-center justify-center gap-1 py-1 px-3 bg-emerald-600 text-white rounded-lg text-xs font-medium hover:bg-emerald-700 transition-colors shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                                            </svg>
                                            Print
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="text-sm text-gray-500">
                                    No payments found matching your criteria.
                                    <a href="{{ route('admin.payments.index') }}"
                                        class="text-emerald-600 hover:text-emerald-700 font-medium ml-1">Clear filters</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 rounded-b-lg">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    {{-- === Scripts === --}}
    <script>
document.addEventListener('DOMContentLoaded', function () {

    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.payment-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedIdsInput = document.getElementById('selectedIds');
    const bulkDeleteWrapper = document.getElementById('bulkDeleteWrapper');

    // Hide button initially
    bulkDeleteWrapper.style.display = "none";

    function updateUI() {
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        selectedIdsInput.value = selected.join(',');

        if (selected.length > 0) {
            bulkDeleteWrapper.style.display = "inline-block";
            bulkDeleteBtn.disabled = false;
            bulkDeleteBtn.textContent = `Delete Selected (${selected.length})`;
        } else {
            bulkDeleteWrapper.style.display = "none";
            bulkDeleteBtn.disabled = true;
            bulkDeleteBtn.textContent = 'Delete Selected';
        }

        // Auto handle "select all"
        selectAll.checked = selected.length === checkboxes.length;
    }

    // Event listeners
    selectAll.addEventListener('change', () => {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateUI();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', updateUI));

    // Confirm delete
    bulkDeleteBtn.addEventListener('click', (e) => {
        e.preventDefault();

        const ids = selectedIdsInput.value.split(',').filter(id => id !== '');

        if (ids.length === 0) return;

        if (confirm(`Are you sure you want to delete ${ids.length} payment(s)?`)) {
            document.getElementById('bulkDeleteForm').submit();
        }
    });
});
</script>

@endsection