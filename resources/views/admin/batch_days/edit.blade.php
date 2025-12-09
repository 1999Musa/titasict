@extends('admin.layout')

@section('title', 'Edit Batch Day')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        ✏️ Edit Batch ({{ $batchDay->days }})
    </h1>
    <a href="{{ route('admin.batch-days.index') }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-emerald-600 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back 
    </a>
</div>

<div class="bg-white rounded-lg shadow-md border border-gray-200 max-w-2xl">
    <form action="{{ route('admin.batch-days.update', $batchDay) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="p-6 md:p-8">
            <div class="space-y-6">
                <div>
                    <label for="days" class="block text-sm font-semibold text-gray-700 mb-2">
                        Batch Days <span class="text-red-600">*</span>
                    </label>
                    <input type="text"
                        id="days"
                        name="days"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                        placeholder="e.g., Sat-Mon-Wed"
                        value="{{ old('days', $batchDay->days) }}"
                        required>
                    @error('days')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Batch Times <span class="text-red-600">*</span>
                    </label>

                    {{-- ✅ FIX: Display the general 'times' array error (used for duplicates/conflicts) --}}
                    @error('times')
                        <div class="mb-3 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            <p class="text-sm font-medium">⚠️ {{ $message }}</p>
                        </div>
                    @enderror
                    
                    <div id="times-container" class="space-y-3">
                        @php
                            // Get times from old input (if validation failed) or from the database
                            // Convert the collection of BatchTime objects to an array of time strings for 'old'
                            $dbTimes = $batchDay->times->pluck('time')->toArray();
                            $times = old('times', $dbTimes);
                        @endphp

                        {{-- Loop through existing or old times --}}
                        @forelse($times as $index => $time)
                            <div class="flex items-center gap-3 time-input-group" id="time-field-{{ $index }}">
                                <input type="text"
                                    name="times[]"
                                    value="{{ $time }}"
                                    placeholder="e.g., 9:00 AM - 10:00 AM"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                    required>
                                
                                {{-- Remove button (always show if there's more than one field) --}}
                                <button type="button"
                                        onclick="removeTimeField('time-field-{{ $index }}')"
                                        class="p-2 text-red-500 bg-red-100 rounded-lg hover:bg-red-200 transition-colors flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        @empty
                            {{-- Fallback: Show one empty field if no times exist/were submitted --}}
                            <div class="flex items-center gap-3 time-input-group" id="time-field-0">
                                <input type="text"
                                    name="times[]"
                                    placeholder="e.g., 9:00 AM - 10:00 AM"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                    required>
                                <button type="button"
                                    onclick="removeTimeField('time-field-0')"
                                    class="p-2 text-red-500 bg-red-100 rounded-lg hover:bg-red-200 transition-colors flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        @endforelse
                    </div>
                    
                    <button type="button"
                            onclick="addTimeField()"
                            class="flex items-center gap-2 mt-4 py-2 px-3 bg-emerald-50 text-emerald-700 rounded-lg text-sm font-medium hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Another Time
                    </button>
                    {{-- @error('times.*') is often redundant when @error('times') is used for the complex checks --}}
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 rounded-b-lg border-t border-gray-200">
            <div class="flex justify-end items-center gap-4">
                <a href="{{ route('admin.batch-days.index') }}"
                   class="py-2 px-4 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="flex items-center gap-2 py-2 px-4 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Batch
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Use a safe counter that doesn't conflict with the PHP-generated IDs
let newTimeIndex = 1000; 

function addTimeField() {
    newTimeIndex++;
    const container = document.getElementById('times-container');
    const fieldId = 'new-time-' + newTimeIndex;

    // Create a wrapper div for input and button
    const fieldWrapper = document.createElement('div');
    fieldWrapper.className = 'flex items-center gap-3 time-input-group';
    fieldWrapper.id = fieldId;

    fieldWrapper.innerHTML = `
        <input type="text"
            name="times[]"
            placeholder="e.g., 3:00 PM - 4:00 PM"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
            required>
        <button type="button"
            onclick="removeTimeField('${fieldId}')"
            class="p-2 text-red-500 bg-red-100 rounded-lg hover:bg-red-200 transition-colors flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
    `;

    container.appendChild(fieldWrapper);
}

function removeTimeField(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.parentNode.removeChild(field);
    }
}
</script>
@endsection