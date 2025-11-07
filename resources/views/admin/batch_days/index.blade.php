@extends('admin.layout')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-3xl font-bold text-gray-800">Batch Days</h2>
        <a href="{{ route('admin.batch-days.create') }}"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-400 to-yellow-500 text-white font-medium px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg hover:from-amber-500 hover:to-yellow-600 transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Add Batch Day
        </a>
    </div>

    {{-- ✅ Success Message --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-5 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- ✅ Desktop Table (hidden on small screens) --}}
    <div class="hidden md:block overflow-x-auto bg-white rounded-2xl shadow-lg border border-gray-100">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gradient-to-r from-amber-100 to-yellow-50 text-gray-700 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Days</th>
                    <th class="px-6 py-3 text-left">Times</th>
                    <th class="px-6 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($batchDays as $day)
                    <tr class="hover:bg-amber-50 transition-all duration-200">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ $day->days }}
                        </td>
                        <td class="px-6 py-4 flex flex-wrap gap-2">
                            @forelse($day->times as $time)
                                <span class="px-3 py-1 bg-gradient-to-r from-green-100 to-green-200 text-green-800 text-xs font-semibold rounded-full shadow-sm">
                                    {{ $time->time }}
                                </span>
                            @empty
                                <span class="text-gray-400 italic">No times</span>
                            @endforelse
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2 flex-wrap">
                                <a href="{{ route('admin.batch-days.edit', $day) }}"
                                   class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-sm transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.batch-days.destroy', $day) }}" method="POST"
                                      onsubmit="return confirm('Delete this batch day?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg shadow-sm transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-6 text-gray-500 italic">
                            No batch days found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ✅ Mobile Card View (visible only on mobile) --}}
    <div class="block md:hidden space-y-4">
        @forelse($batchDays as $day)
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 space-y-3">
                <div>
                    <p class="text-gray-500 text-xs font-semibold uppercase">Days</p>
                    <p class="text-gray-900 font-medium text-base">{{ $day->days }}</p>
                </div>

                <div>
                    <p class="text-gray-500 text-xs font-semibold uppercase">Times</p>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @forelse($day->times as $time)
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full shadow-sm">
                                {{ $time->time }}
                            </span>
                        @empty
                            <span class="text-gray-400 italic text-sm">No times</span>
                        @endforelse
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.batch-days.edit', $day) }}"
                       class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg shadow-sm transition">
                        Edit
                    </a>
                    <form action="{{ route('admin.batch-days.destroy', $day) }}" method="POST"
                          onsubmit="return confirm('Delete this batch day?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg shadow-sm transition">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 italic">No batch days found.</p>
        @endforelse
    </div>

    {{-- ✅ Pagination --}}
    <div class="mt-6 flex justify-center">
        {{ $batchDays->links('pagination::tailwind') }}
    </div>
</div>
@endsection
