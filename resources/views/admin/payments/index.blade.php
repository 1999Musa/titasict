@extends('admin.layout')



@section('content')
    <div class="p-6">
        <h2 class="text-2xl font-semibold mb-4">Payments</h2>

        {{-- === New Monthly Earnings Section === --}}
        <div class="mb-6 p-4 bg-white rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-3 text-gray-800">Total Earnings by Month</h3>
            <div class="flex flex-wrap gap-4">
                @forelse($monthlyEarnings as $earnings)
                    <div class="p-3 bg-gray-100 rounded-md shadow-sm">
                        <span class="font-semibold text-gray-700">{{ $earnings->month }}:</span>
                        <span class="text-green-700 font-bold ml-2">
                            {{ number_format($earnings->total_earnings) }} tk
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500">No paid earnings recorded yet.</p>
                @endforelse
            </div>
        </div>
        {{-- === End New Section === --}}

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Search & Filter Form --}}
        <form method="GET" action="{{ route('admin.payments.index') }}" class="mb-4 flex gap-2 flex-wrap">
            <input type="text" name="mobile_number" value="{{ request('mobile_number') }}"
                placeholder="Search by student mobile number" class="border rounded p-2 flex-1 min-w-[200px]">

            <select name="status" class="border rounded p-2 min-w-[150px]">
                <option value="">All Status</option>
                <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
            </select>

            <button type="submit" class="bg-amber-400 text-white px-4 py-2 rounded hover:bg-amber-500">
                Filter
            </button>
        </form>

        <div class="flex justify-between mb-4 items-center">
            <a href="{{ route('admin.payments.create') }}"
                class="bg-amber-400 text-white px-4 py-2 rounded shadow hover:bg-amber-500 transition">
                + Add Payment
            </a>

            {{-- Bulk Delete Button --}}
            <form id="bulkDeleteForm" method="POST" action="{{ route('admin.payments.bulkDelete') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="selected_ids" id="selectedIds">
                <button type="button" id="bulkDeleteBtn"
                    class="bg-red-500 text-white px-4 py-2 rounded shadow hover:bg-red-600 transition"
                    disabled>
                    Delete Selected
                </button>
            </form>
        </div>

        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border-b text-left">
                            <input type="checkbox" id="selectAll" class="form-checkbox">
                        </th>
                        <th class="px-4 py-2 border-b text-left">Srl</th>
                        <th class="px-4 py-2 border-b text-left">Student</th>
                        <th class="px-4 py-2 border-b text-left">Mobile</th>
                        <th class="px-4 py-2 border-b text-left">Type</th>
                        <th class="px-4 py-2 border-b text-left">Month</th>
                        <th class="px-4 py-2 border-b text-left">Amount</th>
                        <th class="px-4 py-2 border-b text-left">Status</th>
                        <th class="px-4 py-2 border-b text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $key => $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border-b">
                                <input type="checkbox" class="payment-checkbox form-checkbox"
                                    value="{{ $payment->id }}">
                            </td>
                            <td class="px-4 py-2 border-b">{{ $payments->firstItem() + $key }}</td>
                            <td class="px-4 py-2 border-b">{{ optional($payment->student)->name ?? '—' }}</td>
                            <td class="px-4 py-2 border-b">{{ $payment->student->mobile_number ?? '--' }}</td>
                            <td class="px-4 py-2 border-b capitalize">{{ $payment->type }}</td>
                            <td class="px-4 py-2 border-b">{{ $payment->month ?? '—' }}</td>
                            <td class="px-4 py-2 border-b">{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-4 py-2 border-b">
                                <span
                                    class="px-2 py-1 rounded text-xs
                                            {{ $payment->status == 'Paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $payment->status }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border-b text-center">
                                <a href="{{ route('admin.payments.edit', $payment->id) }}" class="text-blue-500 mr-2">Edit</a>
                                <form action="{{ route('admin.payments.destroy', $payment->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Delete this payment?')" class="text-red-500">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">No payments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="mt-4">{{ $payments->links('pagination::tailwind') }}</div>
        @endif
    </div>

    {{-- === Scripts === --}}
    <script>
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.payment-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const selectedIdsInput = document.getElementById('selectedIds');

        // Select/Deselect All
        selectAll.addEventListener('change', () => {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleBulkDeleteButton();
        });

        // Update button on checkbox change
        checkboxes.forEach(cb => cb.addEventListener('change', toggleBulkDeleteButton));

        function toggleBulkDeleteButton() {
            const selected = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
            selectedIdsInput.value = selected.join(',');
            bulkDeleteBtn.disabled = selected.length === 0;
        }

        // Confirm delete
        bulkDeleteBtn.addEventListener('click', () => {
            if (confirm('Are you sure you want to delete the selected payments?')) {
                document.getElementById('bulkDeleteForm').submit();
            }
        });
    </script>
@endsection
