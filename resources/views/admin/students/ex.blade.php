@extends('admin.layout')

@section('title', 'Ex Students')

@section('content')
    <div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-md p-6 md:p-8">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Ex Students</h2>
                <p class="text-sm text-gray-500">List of students who are no longer active.</p>
            </div>

            <a href="{{ route('admin.students.index') }}"
                class="mt-3 md:mt-0 inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 shadow transition-all">
                🧑‍🎓 Active Students
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded-md mb-4 border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if($students->isEmpty())
            <div class="text-center py-10 text-gray-500 text-lg">No ex-students found.</div>
        @else
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Name</th>
                            <th class="px-4 py-3 text-left font-medium">Mobile</th>
                            <th class="px-4 py-3 text-left font-medium">Guardian Mobile</th>
                            <th class="px-4 py-3 text-left font-medium">Batch (Day & Time)</th>
                            <th class="px-4 py-3 text-left font-medium">Exam Year</th>
                            <th class="px-4 py-3 text-left font-medium">Joining Month</th>
                            <th class="px-4 py-3 text-left font-medium">Status</th>
                            <th class="px-4 py-3 text-center font-medium">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach($students as $student)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $student->name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $student->mobile_number }}</td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $student->guardian_mobile_number ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    @if($student->batchDay && $student->batchTime)
                                        <span class="font-medium">{{ $student->batchDay->days }}</span> –
                                        <span class="text-gray-600">{{ $student->batchTime->time }}</span>
                                    @else
                                        <span class="text-gray-400 italic">Not Assigned</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $student->exam_year ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $student->joining_month }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded">Ex Student</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex flex-col items-center space-y-2">

                                        {{-- Restore to Active --}}
                                        <form action="{{ route('admin.students.restore', $student->id) }}" method="POST"
                                            onsubmit="return confirm('Move this student back to Active?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                Make Active
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this ex-student?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $students->links() }}
            </div>
        @endif
    </div>
@endsection