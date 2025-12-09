@extends('admin.layout')

@section('title', 'Create Batch Day')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        + Create Batch
    </h1>
    <a href="{{ route('admin.batch-days.index') }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-emerald-600 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back
    </a>
</div>

<div class="bg-white rounded-lg shadow-md border border-gray-200">
    <form action="{{ route('admin.batch-days.store') }}" method="POST">
        @csrf
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
                        value="{{ old('days') }}"
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
                        
                        {{-- Determine which times to show: old input if validation failed, otherwise a single empty field --}}
                        @php $oldTimes = old('times', ['']); @endphp
                        
                        @forelse ($oldTimes as $index => $time)
                        <div class="flex gap-2 time-input-group">
                            <input type="text"
                                name="times[]"
                                placeholder="e.g., 9:00 AM - 10:00 AM"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                value="{{ $time }}"
                                required>

                            {{-- Show remove button if it's not the first (initial) field --}}
                            @if($index > 0)
                            <button type="button" onclick="this.closest('.time-input-group').remove()"
                                class="text-red-500 hover:text-red-700 transition-colors px-3 py-2 text-sm font-medium whitespace-nowrap">
                                Remove
                            </button>
                            @endif
                        </div>
                        @empty
                        {{-- Fallback: Should not be hit if oldTimes is seeded with [''] --}}
                        <div class="flex gap-2 time-input-group">
                            <input type="text"
                                name="times[]"
                                placeholder="e.g., 9:00 AM - 10:00 AM"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                required>
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

                    {{-- ❌ Removed @error('times.*') as the general @error('times') handles the important conflict messages. --}}
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
                    
                    + Create Batch
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function addTimeField() {
    const container = document.getElementById('times-container');
    const newDiv = document.createElement('div');
    newDiv.className = 'flex gap-2 time-input-group';
    
    // Using innerHTML to create both the input and the remove button easily
    newDiv.innerHTML = `
        <input type="text"
            name="times[]"
            placeholder="e.g., 3:00 PM - 4:00 PM"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
            required>
        <button type="button" onclick="this.closest('.time-input-group').remove()"
            class="text-red-500 hover:text-red-700 transition-colors px-3 py-2 text-sm font-medium whitespace-nowrap">
            Remove
        </button>
    `;

    container.appendChild(newDiv);
}
</script>
@endsection