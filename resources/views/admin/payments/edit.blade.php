@extends('admin.layout')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-semibold mb-4">Edit Payment</h2>

    <form method="POST" action="{{ route('admin.payments.update', $payment->id) }}" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- Student --}}
        <div>
            <label class="block font-medium">Student</label>
            <input type="text"
                   value="{{ $payment->student->name ?? 'N/A' }}"
                   class="w-full border rounded p-2 bg-gray-100"
                   readonly>
            <input type="hidden" name="student_id" value="{{ $payment->student_id }}">
        </div>

        {{-- Payment Options --}}
        <div id="paymentOptions" class="mt-4">
            <label class="block font-medium mb-2">Payment Options (Paid months are excluded )</label>
            <div class="grid grid-cols-3 gap-2" id="monthList">
                <p class="col-span-3 text-gray-500 text-sm">Loading months…</p>
            </div>
        </div>

        {{-- Amount --}}
        <div>
            <label class="block font-medium">Amount</label>
            <input name="amount" type="number" min="0" step="0.01" class="w-full border rounded p-2"
                value="{{ $payment->amount }}" required>
        </div>

        {{-- Status --}}
        <div>
            <label class="block font-medium">Status</label>
            <select name="status" class="w-full border rounded p-2">
                <option value="Paid" {{ $payment->status === 'Paid' ? 'selected' : '' }}>Paid</option>
                <option value="Pending" {{ $payment->status === 'Pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </div>

        <button class="bg-amber-400 text-white px-4 py-2 rounded hover:bg-amber-500">
            Update Payment
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const studentInput = document.querySelector('input[name="student_id"]');
    const monthList = document.getElementById('monthList');

    // Pass current payment info
    const selectedType = @json($payment->type);
    const selectedMonth = @json($payment->month);

    function loadMonths() {
        const studentId = studentInput.value;
        if (!studentId) {
            monthList.innerHTML = `<p class="col-span-3 text-gray-500 text-sm">No student selected.</p>`;
            return;
        }

        monthList.innerHTML = `<p class="col-span-3 text-gray-500 text-sm">Loading months…</p>`;

        fetch(`/admin/payments/months/${encodeURIComponent(studentId)}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(async (res) => {
            const data = await res.json().catch(() => null);
            if (!res.ok) {
                monthList.innerHTML = `<p class="col-span-3 text-red-500 text-sm">Error loading months.</p>`;
                return;
            }

            let html = '';

            // Always show admission fee checkbox
            html += `
                <label class="inline-flex items-center col-span-3">
                    <input
                        type="checkbox"
                        name="payment_type[]"
                        value="admission"
                        class="form-checkbox"
                        ${selectedType === 'admission' ? 'checked' : ''}
                    >
                    <span class="ml-2">Admission Fee</span>
                </label>
            `;

            // Show all months, keeping the paid one checked
            if (Array.isArray(data.months) && data.months.length > 0) {
                data.months.forEach((m) => {
                    const checked = (selectedType === 'monthly' && selectedMonth === m) ? 'checked' : '';
                    html += `
                        <label class="inline-flex items-center">
                            <input
                                type="checkbox"
                                name="payment_type[]"
                                value="${m}"
                                class="form-checkbox"
                                ${checked}
                            >
                            <span class="ml-2">${m}</span>
                        </label>
                    `;
                });
            } else {
                html += `<p class="col-span-3 text-gray-500 text-sm">No months available.</p>`;
            }

            monthList.innerHTML = html;
        })
        .catch(() => {
            monthList.innerHTML = `<p class="col-span-3 text-red-500 text-sm">Error loading months.</p>`;
        });
    }

    loadMonths();
});
</script>
@endsection
