@extends('admin.layout')

@section('title', 'Add New Payment')

@section('content')

    <div class="flex items-center justify-between mb-6">

        <a href="{{ route('admin.payments.index') }}"
            class="flex items-center gap-2 text-sm text-gray-600 hover:text-emerald-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md border border-gray-200 max-w-2xl">
        <form method="POST" action="{{ route('admin.payments.store') }}">
            @csrf
            <div class="p-6 md:p-8">
                <div class="space-y-6">

                    <div id="search-student-form"> {{-- Added an ID for the container --}}
                        <label for="searchInput" class="block text-sm font-semibold text-gray-700 mb-2">
                            Search Student by Mobile <span class="text-red-600">*</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="text" id="searchInput"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                placeholder="Enter mobile number...">
                            <button type="button" id="searchBtn"
                                class="flex-shrink-0 flex items-center gap-2 py-2 px-4 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Search
                            </button>
                        </div>
                        <div id="searchError" class="text-xs text-red-600 mt-1 hidden">Error searching students.</div>
                        <div id="searchSpinner" class="hidden mt-2 flex items-center gap-2 text-sm text-gray-500">
                            <svg class="animate-spin h-4 w-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Searching...
                        </div>
                    </div>

                    <div id="studentSection" class="hidden">
                        <label for="studentSelect" class="block text-sm font-semibold text-gray-700 mb-2">
                            Select Student
                        </label>
                        <select id="studentSelect" name="student_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                            required>
                            <option value="">Select Student</option>
                        </select>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Payment For
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-3" id="monthList">
                            <p class="col-span-full text-sm text-gray-500">Search and select a student to load payable
                                months.</p>
                        </div>
                    </div>

                    <div>
                        <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">
                            Amount <span class="text-red-600">*</span>
                        </label>
                        <input type="number" id="amount" name="amount" min="0" step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                            value="{{ old('amount') }}" required>
                        @error('amount')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                            Status <span class="text-red-600">*</span>
                        </label>
                        <select id="status" name="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                            required>
                            <option value="Paid" {{ old('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                            <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                        @error('status')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-center pt-2">
                        <input id="send_sms" name="send_sms" type="checkbox" value="1" 
                               {{ old('send_sms', true) ? 'checked' : '' }} {{-- Default to checked or use old value --}}
                               class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                        <label for="send_sms" class="ml-2 block text-sm font-medium text-gray-700">
                            # Send SMS
                        </label>
                    </div>

                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 rounded-b-lg border-t border-gray-200">
                <div class="flex flex-col sm:flex-row justify-end items-center gap-4">
                    <button type="submit"
                        class="w-full sm:w-auto flex items-center justify-center gap-2 py-2 px-4 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Payment
                    </button>
                    <button type="button" id="savePrintBtn"
                        class="w-full sm:w-auto flex items-center justify-center gap-2 py-2 px-4 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Save & Print PDF
                    </button>

                </div>
            </div>
        </form>
        <script>
            document.getElementById('savePrintBtn').addEventListener('click', async function () {
                const form = this.closest('form');
                const formData = new FormData(form);

                // Send the request to save + get PDF
                const res = await fetch("{{ route('admin.payments.savePdf') }}", {
                    method: "POST",
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });

                if (res.ok) {
                    const blob = await res.blob();

                    // ✅ Get the selected student's name
                    const studentSelect = document.getElementById('studentSelect');
                    const selectedOption = studentSelect.options[studentSelect.selectedIndex];
                    const studentName = selectedOption ? selectedOption.textContent.split('(')[0].trim().replace(/\s+/g, '_') : 'student';

                    // ✅ Construct filename like "John_Doe_payment.pdf"
                    const filename = `${studentName}_payment.pdf`;

                    // Create and trigger download
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement("a");
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);

                    // ✅ Redirect after download
                    window.location.href = "{{ route('admin.payments.index') }}";
                } else {
                    alert("Error generating PDF. Please try again.");
                }

            });
        </script>

    </div>

    {{-- JS Section --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const studentSelect = document.getElementById('studentSelect');
            const studentSection = document.getElementById('studentSection');
            const errorMsg = document.getElementById('searchError');
            const searchSpinner = document.getElementById('searchSpinner');
            const monthList = document.getElementById('monthList');
            
            // Style for dynamically created checkboxes
            const checkboxClasses = "h-5 w-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 transition";

            // Helper function for cancellable fetch with retries (kept as is)
            async function retryableFetch(url, options = {}, retries = 3, delay = 1000) {
                for (let i = 0; i < retries; i++) {
                    try {
                        const response = await fetch(url, options);
                        if (!response.ok) {
                            if (response.status >= 500) {
                                throw new Error(`Server error: ${response.status}`);
                            }
                            return response;
                        }
                        return response;
                    } catch (error) {
                        if (i === retries - 1) throw error;
                        await new Promise(resolve => setTimeout(resolve, delay * Math.pow(2, i)));
                    }
                }
            }

            // Function to show a styled message in the month list (kept as is)
            function setMonthListMessage(message, type = 'info') {
                let colorClass = 'text-gray-500';
                if (type === 'error') colorClass = 'text-red-500';
                monthList.innerHTML = `<p class="col-span-full text-sm ${colorClass}">${message}</p>`;
            }

            // Function to load months for a given studentId (extracted for reuse)
           // Function to load months for a given studentId (MODIFIED)
async function loadMonths(studentId) {
    if (!studentId) {
        setMonthListMessage('Select a student to load months...', 'info');
        return;
    }

    setMonthListMessage('Loading months…', 'info');

    try {
        const res = await retryableFetch(`/admin/payments/months/${encodeURIComponent(studentId)}`, {
            headers: { 'Accept': 'application/json' }
        });

        const data = await res.json().catch(() => null);

        if (!res.ok || !data) {
            setMonthListMessage('Error loading months.', 'error');
            return;
        }

        let html = '';

        // Handle Admission Fee (Use the new property include_admission)
        if (data.include_admission) { // Check if admission fee is not yet paid
            html += `
                <label class="col-span-2 sm:col-span-3 flex items-center p-3 bg-emerald-50 rounded-lg border border-emerald-200">
                    <input type="checkbox" name="payment_type[]" value="admission" class="${checkboxClasses}">
                    <span class="ml-3 font-semibold text-emerald-800">Admission Fee</span>
                </label>
            `;
        }

        // Handle Monthly Fees (Use the new property unpaid_months)
        if (Array.isArray(data.unpaid_months) && data.unpaid_months.length > 0) {
            data.unpaid_months.forEach(m => {
                html += `
                    <label class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <input type="checkbox" name="payment_type[]" value="${m}" class="${checkboxClasses}">
                        <span class="ml-3 text-sm font-medium text-gray-700">${m}</span>
                    </label>
                `;
            });
        } else if (!data.include_admission) {
            // If no unpaid months AND admission is paid
            setMonthListMessage('All payments have been made.', 'info');
            return; 
        }

        monthList.innerHTML = html;

    } catch (err) {
        console.error(err);
        setMonthListMessage('Error loading months (network/server).', 'error');
    }
}

            // Function to perform the student search (MODIFIED)
            async function performSearch() {
                const query = searchInput.value.trim();
                if (!query) {
                    errorMsg.textContent = "Please enter a mobile number.";
                    errorMsg.classList.remove('hidden');
                    return;
                }

                errorMsg.classList.add('hidden');
                searchSpinner.classList.remove('hidden');
                searchBtn.disabled = true;
                studentSelect.innerHTML = '<option value="">Searching...</option>';
                studentSection.classList.remove('hidden');
                setMonthListMessage('Searching for student...', 'info'); // Updated message

                try {
                    const res = await retryableFetch(`/admin/payments/search-students?q=${encodeURIComponent(query)}`);

                    if (!res.ok) {
                        throw new Error('Search failed');
                    }

                    const students = await res.json();

                    if (students.length === 0) {
                        studentSelect.innerHTML = '<option value="">No student found</option>';
                        setMonthListMessage('No student found.', 'info');
                        return;
                    }

                    studentSelect.innerHTML = '<option value="">Select Student</option>';
                    students.forEach(s => {
                        const option = document.createElement('option');
                        option.value = s.id;
                        option.textContent = `${s.name} (${s.mobile_number})`;
                        studentSelect.appendChild(option);
                    });
                    
                    // NEW LOGIC: If exactly one student is found, auto-select them and trigger month loading
                    if (students.length === 1) {
                        const studentId = students[0].id;
                        studentSelect.value = studentId;
                        loadMonths(studentId); // Automatically load months
                    } else {
                        // If multiple students, prompt user to select
                        setMonthListMessage('Multiple students found. Please select one above.', 'info');
                    }

                } catch (err) {
                    console.error(err);
                    errorMsg.textContent = "Error searching students.";
                    errorMsg.classList.remove('hidden');
                    studentSelect.innerHTML = '<option value="">Search failed</option>';
                    setMonthListMessage('Search failed.', 'error');
                } finally {
                    searchSpinner.classList.add('hidden');
                    searchBtn.disabled = false;
                }
            }
            
            // --- Event Listener for Search Button ---
            searchBtn.addEventListener('click', performSearch);

            // --- Event Listener for Enter Key in Search Input ---
            searchInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    performSearch();
                }
            });

            // --- Event Listener for Student Selection (MODIFIED to use loadMonths function) ---
            studentSelect.addEventListener('change', function () {
                loadMonths(this.value);
            });
        });
    </script>
@endsection