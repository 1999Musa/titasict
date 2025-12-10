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
    const studentId = document.querySelector('input[name="student_id"]').value;
    const monthList = document.getElementById("monthList");

    const editingType = @json($payment->type);
    const editingMonth = @json($payment->month);

    function loadMonths() {
        monthList.innerHTML = `<p class="col-span-3">Loading…</p>`;

        fetch(`/admin/payments/months/${studentId}`)
            .then(res => res.json())
            .then(data => {
                let html = "";

                // ============================
                // ADMISSION PAYMENT
                // ============================
                const admissionChecked =
                    editingType === "admission"
                        ? true
                        : data.admission_paid;

                html += `
                    <label class="inline-flex items-center col-span-3">
                        <input type="checkbox" name="payment_type[]" value="admission"
                            class="payment-option"
                            ${admissionChecked ? "checked" : ""}>
                        <span class="ml-2">Admission Fee</span>
                    </label>
                `;

                // ============================
                // MONTHLY PAYMENTS
                // ============================
                data.months.forEach(month => {
                    const isPaid = data.paid_months.includes(month);
                    const isEditing = editingType === "monthly" && editingMonth === month;

                    const checked = isPaid || isEditing ? "checked" : "";

                    html += `
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="payment_type[]" value="${month}"
                                class="payment-option"
                                ${checked}>
                            <span class="ml-2">${month}</span>
                        </label>
                    `;
                });

                monthList.innerHTML = html;

                enforceSingleSelect();
            });
    }

    function enforceSingleSelect() {
        const options = document.querySelectorAll(".payment-option");

        options.forEach(opt => {
            opt.addEventListener("change", function () {
                if (this.checked) {
                    options.forEach(o => {
                        if (o !== this) o.checked = false;
                    });
                }
            });
        });
    }

    loadMonths();
});
</script>



@endsection