@extends('admin.layout')

@section('title', 'Add New Payment')

@section('content')

<!-- Header -->
<div class="flex items-center justify-between mb-6">

    <a href="{{ route('admin.payments.index') }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-emerald-600 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back 
    </a>
</div>

<!-- Form Card -->
<div class="bg-white rounded-lg shadow-md border border-gray-200 max-w-2xl">
    <form method="POST" action="{{ route('admin.payments.store') }}">
        @csrf
        <div class="p-6 md:p-8">
            <div class="space-y-6">

                <!-- 1. Search Student -->
                <div>
                    <label for="searchInput" class="block text-sm font-semibold text-gray-700 mb-2">
                        Search Student by Mobile <span class="text-red-600">*</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="text"
                               id="searchInput"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                               placeholder="Enter mobile number...">
                        <button type="button"
                                id="searchBtn"
                                class="flex-shrink-0 flex items-center gap-2 py-2 px-4 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Search
                        </button>
                    </div>
                    <div id="searchError" class="text-xs text-red-600 mt-1 hidden">Error searching students.</div>
                    <div id="searchSpinner" class="hidden mt-2 flex items-center gap-2 text-sm text-gray-500">
                        <svg class="animate-spin h-4 w-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Searching...
                    </div>
                </div>

                <!-- 2. Select Student -->
                <div id="studentSection" class="hidden">
                    <label for="studentSelect" class="block text-sm font-semibold text-gray-700 mb-2">
                        Select Student
                    </label>
                    <select id="studentSelect"
                            name="student_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                            required>
                        <option value="">Select Student</option>
                    </select>
                </div>

                <!-- 3. Payment Options -->
                <div class="border-t border-gray-200 pt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Payment For
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-3" id="monthList">
                        <p class="col-span-full text-sm text-gray-500">Search and select a student to load payable months.</p>
                    </div>
                </div>

                <!-- 4. Amount -->
                <div>
                    <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">
                        Amount <span class="text-red-600">*</span>
                    </label>
                    <input type="number"
                           id="amount"
                           name="amount"
                           min="0"
                           step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                           value="{{ old('amount') }}"
                           required>
                    @error('amount')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 5. Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        Status <span class="text-red-600">*</span>
                    </label>
                    <select id="status"
                            name="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                            required>
                        <option value="Paid" {{ old('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                        <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                    @error('status')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        <!-- Form Footer -->
        <div class="bg-gray-50 px-6 py-4 rounded-b-lg border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-end items-center gap-4">
                <button type="submit"
                        class="w-full sm:w-auto flex items-center justify-center gap-2 py-2 px-4 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Payment
                </button>
                <button type="submit"
                        formaction="{{ route('admin.payments.savePdf') }}"
                        class="w-full sm:w-auto flex items-center justify-center gap-2 py-2 px-4 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Save & Print PDF
                </button>
            </div>
        </div>
    </form>
</div>

{{-- JS Section --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const studentSelect = document.getElementById('studentSelect');
    const studentSection = document.getElementById('studentSection');
    const errorMsg = document.getElementById('searchError');
    const searchSpinner = document.getElementById('searchSpinner');
    const monthList = document.getElementById('monthList');
    
    // Helper function for cancellable fetch with retries
    async function retryableFetch(url, options = {}, retries = 3, delay = 1000) {
        for (let i = 0; i < retries; i++) {
            try {
                const response = await fetch(url, options);
                if (!response.ok) {
                    // Only retry on 5xx server errors
                    if (response.status >= 500) {
                        throw new Error(`Server error: ${response.status}`);
                    }
                    // For client errors (4xx), don't retry, just return the response
                    return response;
                }
                return response; // Success
            } catch (error) {
                if (i === retries - 1) throw error; // Last retry failed, throw
                // Wait with exponential backoff
                await new Promise(resolve => setTimeout(resolve, delay * Math.pow(2, i)));
            }
        }
    }
    
    // Style for dynamically created checkboxes
    const checkboxClasses = "h-5 w-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 transition";
    
    // Function to show a styled message in the month list
    function setMonthListMessage(message, type = 'info') {
        let colorClass = 'text-gray-500';
        if (type === 'error') colorClass = 'text-red-500';
        monthList.innerHTML = `<p class="col-span-full text-sm ${colorClass}">${message}</p>`;
    }
    
    // --- Event Listener for Search Button ---
    searchBtn.addEventListener('click', async function() {
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
        setMonthListMessage('Search for a student to load months.', 'info');

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

        } catch (err) {
            console.error(err);
            errorMsg.textContent = "Error searching students.";
            errorMsg.classList.remove('hidden');
            studentSelect.innerHTML = '<option value="">Search failed</option>';
        } finally {
            searchSpinner.classList.add('hidden');
            searchBtn.disabled = false;
        }
    });

    // --- Event Listener for Student Selection ---
    studentSelect.addEventListener('change', async function() {
        const studentId = this.value;

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

            // Handle Admission Fee
            if (data.include_admission) {
                html += `
                    <label class="col-span-2 sm:col-span-3 flex items-center p-3 bg-emerald-50 rounded-lg border border-emerald-200">
                        <input type="checkbox" name="payment_type[]" value="admission" class="${checkboxClasses}">
                        <span class="ml-3 font-semibold text-emerald-800">Admission Fee</span>
                    </label>
                `;
            }

            // Handle Monthly Fees
            if (Array.isArray(data.months) && data.months.length > 0) {
                data.months.forEach(m => {
                    html += `
                        <label class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <input type="checkbox" name="payment_type[]" value="${m}" class="${checkboxClasses}">
                            <span class="ml-3 text-sm font-medium text-gray-700">${m}</span>
                        </label>
                    `;
                });
            } else if (!data.include_admission) {
                // Only show this if there are no months AND no admission fee
                setMonthListMessage('All months have been paid.', 'info');
                return; // Exit before setting html
            }

            monthList.innerHTML = html;

        } catch (err) {
            console.error(err);
            setMonthListMessage('Error loading months (network/server).', 'error');
        }
    });
});
</script>
@endsection