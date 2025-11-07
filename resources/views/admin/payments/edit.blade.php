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
                <select id="studentSelect" name="student_id" class="w-full border rounded p-2" required>
                    <option value="">Select Student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ $payment->student_id == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Payment Options --}}
            <div id="paymentOptions" class="mt-4">
                <label class="block font-medium mb-2">Payment Options</label>
                <div class="grid grid-cols-3 gap-2" id="monthList">
                    <p class="col-span-3 text-gray-500 text-sm">Select a student to load months...</p>
                </div>
            </div>

            {{-- Amount --}}
            <div>
                <label class="block font-medium">Amount</label>
                <input name="amount" type="number" min="0" step="0.01"
                       class="w-full border rounded p-2"
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
        const studentSelect = document.getElementById('studentSelect');
        const monthList = document.getElementById('monthList');
        const selectedType = @json($payment->type);
        const selectedMonth = @json($payment->month);

        function loadMonths() {
            const studentId = studentSelect.value;
            if (!studentId) {
                monthList.innerHTML = '<p class="col-span-3 text-gray-500 text-sm">Select a student to load months...</p>';
                return;
            }

            monthList.innerHTML = '<p class="col-span-3 text-gray-500 text-sm">Loading months…</p>';

            fetch(`/admin/payments/months/${encodeURIComponent(studentId)}`, {
                headers: { 'Accept': 'application/json' }
            })
                .then(async (res) => {
                    const data = await res.json().catch(() => null);

                    if (!res.ok) {
                        const errMsg = (data && data.error) ? data.error : `Server returned ${res.status}`;
                        monthList.innerHTML = `<p class="col-span-3 text-red-500 text-sm">Error loading months: ${errMsg}</p>`;
                        return;
                    }

                    let html = '';

                    // Admission Fee only if backend says it's available OR current payment is admission
                    if ((data.include_admission && selectedType !== 'admission') || selectedType === 'admission') {
                        html += `
                            <label class="inline-flex items-center col-span-3">
                                <input type="checkbox" name="payment_type[]" value="admission"
                                    class="form-checkbox" ${selectedType === 'admission' ? 'checked' : ''}>
                                <span class="ml-2">Admission Fee</span>
                            </label>
                        `;
                    }

                    // Handle months
                    if (Array.isArray(data.months) && data.months.length > 0) {
                        data.months.forEach((m) => {
                            const checked = (selectedType === 'monthly' && selectedMonth === m) ? 'checked' : '';
                            html += `
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="payment_type[]" value="${m}"
                                        class="form-checkbox" ${checked}>
                                    <span class="ml-2">${m}</span>
                                </label>
                            `;
                        });
                    } else if (selectedType !== 'admission') {
                        html += '<p class="col-span-3 text-gray-500 text-sm">All months already paid.</p>';
                    }

                    monthList.innerHTML = html;
                })
                .catch((err) => {
                    console.error('Fetch error:', err);
                    monthList.innerHTML = '<p class="col-span-3 text-red-500 text-sm">Error loading months (network or server).</p>';
                });
        }

        // Load months on page load
        loadMonths();

        // Reload if student changes
        studentSelect.addEventListener('change', loadMonths);
    });
</script>

@endsection
