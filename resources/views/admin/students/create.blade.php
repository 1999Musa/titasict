@extends('admin.layout')

@section('title', 'Add New Student')

@section('content')

<!-- Header -->
<div class="flex items-center justify-between mb-6">

    <a href="{{ route('admin.students.index') }}" class="flex items-center gap-2 text-m text-gray-900 hover:text-emerald-600 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back 
    </a>
</div>

<!-- Form Card -->
<div class="bg-white rounded-lg shadow-md border border-gray-200 max-w-2xl">
    <form action="{{ route('admin.students.store') }}" method="POST">
        @csrf
        <div class="p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Student Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Student Name <span class="text-red-600">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                           value="{{ old('name') }}"
                           required>
                    @error('name')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mobile Number -->
                <div>
                    <label for="mobile_number" class="block text-sm font-semibold text-gray-700 mb-2">
                        Mobile Number <span class="text-red-600">*</span>
                    </label>
                    <input type="text"
                           id="mobile_number"
                           name="mobile_number"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                           value="{{ old('mobile_number') }}"
                           required>
                    @error('mobile_number')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Guardian Mobile Number -->
                <div>
                    <label for="guardian_mobile_number" class="block text-sm font-semibold text-gray-700 mb-2">
                        Guardian's Mobile <span class="text-red-600">*</span>
                    </label>
                    <input type="text"
                           id="guardian_mobile_number"
                           name="guardian_mobile_number"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                           value="{{ old('guardian_mobile_number') }}"
                           required>
                    @error('guardian_mobile_number')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-semibold text-gray-700 mb-2">
                        Gender <span class="text-red-600">*</span>
                    </label>
                    <select id="gender"
                            name="gender"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                            required>
                        <option value="">-- Select Gender --</option>
                        @foreach($genders as $g)
                            <option value="{{ $g }}" {{ old('gender') == $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                    @error('gender')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Exam Year -->
                <div>
                    <label for="exam_year" class="block text-sm font-semibold text-gray-700 mb-2">
                        Exam Year <span class="text-gray-400">(Optional)</span>
                    </label>
                    <input type="text"
                           id="exam_year"
                           name="exam_year"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                           placeholder="e.g., 2025"
                           value="{{ old('exam_year') }}">
                </div>

                <!-- Joining Month -->
                <div class="md:col-span-2">
                    <label for="joining_month" class="block text-sm font-semibold text-gray-700 mb-2">
                        Joining Month <span class="text-red-600">*</span>
                    </label>
                    <input type="month"
                           id="joining_month"
                           name="joining_month"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                           value="{{ old('joining_month') }}"
                           required>
                    @error('joining_month')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Batch Day -->
                <div class="md:col-span-2 border-t border-gray-200 pt-6">
                    <label for="batch_day_select" class="block text-sm font-semibold text-gray-700 mb-2">
                        Batch Day <span class="text-red-600">*</span>
                    </label>
                    <select id="batch_day_select"
                            name="batch_day_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                            required>
                        <option value="">-- Select Batch Day --</option>
                        @foreach($batchDays as $bd)
                            <option value="{{ $bd->id }}" {{ old('batch_day_id') == $bd->id ? 'selected' : '' }}>
                                {{ $bd->days }}
                            </option>
                        @endforeach
                    </select>
                    @error('batch_day_id')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Batch Time -->
                <div class="md:col-span-2">
                    <label for="batch_time_select" class="block text-sm font-semibold text-gray-700 mb-2">
                        Batch Time <span class="text-red-600">*</span>
                    </label>
                    <select id="batch_time_select"
                            name="batch_time_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                            required>
                        <option value="">-- Select Batch Day First --</option>
                        
                        {{-- Render all times (we'll filter client-side) --}}
                        @foreach($batchTimes as $bt)
                            <option data-batch-day-id="{{ $bt->batch_day_id }}"
                                    value="{{ $bt->id }}"
                                    {{ old('batch_time_id') == $bt->id ? 'selected' : '' }}
                                    style="display: none;"> {{-- Hide all initially --}}
                                {{ $bt->time }}
                            </option>
                        @endforeach
                    </select>
                    @error('batch_time_id')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        <!-- Form Footer -->
        <div class="bg-gray-50 px-6 py-4 rounded-b-lg border-t border-gray-200">
            <div class="flex justify-end items-center gap-4">
                <a href="{{ route('admin.students.index') }}"
                   class="py-2 px-4 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="flex items-center gap-2 py-2 px-4 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors shadow-sm">
                    
                   + Create Student
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const batchDaySelect = document.getElementById('batch_day_select');
    const batchTimeSelect = document.getElementById('batch_time_select');
    const timeOptions = batchTimeSelect.querySelectorAll('option');

    function filterTimes() {
        const selectedDay = batchDaySelect.value;
        
        // Reset to placeholder
        batchTimeSelect.value = '';
        const placeholder = batchTimeSelect.querySelector('option[value=""]');
        
        if (!selectedDay) {
            placeholder.textContent = '-- Select Batch Day First --';
        } else {
             placeholder.textContent = '-- Select Batch Time --';
        }

        // Keep the placeholder, hide/show others
        timeOptions.forEach(opt => {
            if (!opt.value) return; // Skip placeholder
            
            const dayId = opt.getAttribute('data-batch-day-id');
            
            if (!selectedDay || dayId === selectedDay) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });

        // If the old selected option is now hidden, reset selection
        if (batchTimeSelect.selectedOptions.length && batchTimeSelect.selectedOptions[0].style.display === 'none') {
            batchTimeSelect.value = '';
        }
    }

    batchDaySelect.addEventListener('change', filterTimes);

    // Initial filter on page load (in case of validation errors)
    filterTimes();

    @if(old('batch_time_id'))
        batchTimeSelect.value = @json(old('batch_time_id'));
    @endif
});
</script>
@endsection