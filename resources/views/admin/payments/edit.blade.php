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
                <input type="text" value="{{ $payment->student->name ?? 'N/A' }}"
                    class="w-full border rounded p-2 bg-gray-100" readonly>
                <input type="hidden" name="student_id" value="{{ $payment->student_id }}">
            </div>

            {{-- Payment Options --}}
            <div id="paymentOptions" class="mt-4">
                <label class="block font-medium mb-2">Payment Options (Paid months are ticked )</label>
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

            <button class="bg-emerald-700 text-white px-4 py-2 rounded hover:bg-emerald-500">
                + Update Payment
            </button>
        </form>
    </div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const monthList = document.getElementById("monthList");
    const studentId = document.querySelector('input[name="student_id"]').value; // Hidden input

    // Tailwind checkbox classes
    const checkboxClasses = "h-5 w-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 transition";

    async function loadMonthsForEdit(studentId) {
        if (!studentId) return;

        monthList.innerHTML = '<p class="col-span-full text-sm text-gray-500">Loading months…</p>';

        try {
            const res = await fetch(`/admin/payments/months/${studentId}?for_edit=1`, {
                headers: { 'Accept': 'application/json' }
            });

            const data = await res.json();

            if (!res.ok || !data) {
                monthList.innerHTML = '<p class="col-span-full text-sm text-red-500">Error loading months.</p>';
                return;
            }

            let html = '';

            // Admission Fee
            html += `
                <label class="col-span-2 sm:col-span-3 flex items-center p-3 bg-emerald-50 rounded-lg border border-emerald-200">
                    <input type="checkbox" name="payment_type[]" value="admission" class="${checkboxClasses}" ${data.include_admission ? '' : 'checked'}>
                    <span class="ml-3 font-semibold text-emerald-800">Admission Fee</span>
                </label>
            `;

            // Monthly Fees
            const allMonths = [...data.unpaid_months, ...data.paid_months];
            allMonths.sort((a, b) => new Date(a) - new Date(b));

            allMonths.forEach(month => {
                const checked = data.paid_months.includes(month) ? 'checked' : '';
                html += `
                    <label class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <input type="checkbox" name="payment_type[]" value="${month}" class="${checkboxClasses}" ${checked}>
                        <span class="ml-3 text-sm font-medium text-gray-700">${month}</span>
                    </label>
                `;
            });

            monthList.innerHTML = html;

        } catch (err) {
            console.error(err);
            monthList.innerHTML = '<p class="col-span-full text-sm text-red-500">Error loading months (network/server).</p>';
        }
    }

    // Initial load
    if (studentId) {
        loadMonthsForEdit(studentId);
    }

    // Prevent form submit if nothing is checked
    document.querySelector('form').addEventListener('submit', function(e) {
        const checked = this.querySelectorAll('input[name="payment_type[]"]:checked');
        if (checked.length === 0) {
            e.preventDefault();
            alert('Please select at least one payment option (admission or monthly).');
            return false;
        }
    });

});
</script>



@endsection