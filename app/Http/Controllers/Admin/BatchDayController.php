<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BatchDay;
use App\Models\BatchTime;

class BatchDayController extends Controller
{
    // List all batch days
    public function index()
    {
        $batchDays = BatchDay::with('times')->latest()->paginate(10);
        return view('admin.batch_days.index', compact('batchDays'));
    }

    // Show create form
    public function create()
    {
        return view('admin.batch_days.create');
    }

    // Store new batch day + batch times
    public function store(Request $request)
    {
        $request->validate([
            'days' => 'required|string',
            'times' => 'required|array|min:1',
            'times.*' => 'required|string',
        ]);

        $times = array_map('trim', $request->times);

        // 1. Prevent duplicate times inside the submitted list
        if (count($times) !== count(array_unique($times))) {
            return back()->withErrors([
                'times' => 'Duplicate time slots are not allowed in the form submission.'
            ])->withInput();
        }

        // 2. Prevent creating a day+time combination that ALREADY exists
        foreach ($times as $time) {
            // Check if any existing BatchDay with the same 'days' string already has this time
            $exists = BatchDay::where('days', $request->days)
                ->whereHas('times', function($q) use ($time) {
                    $q->where('time', $time);
                })
                ->exists();

            if ($exists) {
                return back()->withErrors([
                    'times' => "The time slot '$time' already exists for the day '$request->days'."
                ])->withInput();
            }
        }

        // ✔ Create new batch day
        $batchDay = BatchDay::create(['days' => $request->days]);

        // ✔ Insert times safely
        foreach ($times as $time) {
            BatchTime::create([
                'batch_day_id' => $batchDay->id,
                'time' => $time,
            ]);
        }

        return redirect()->route('admin.batch-days.index')
            ->with('success', 'Batch Day created successfully.');
    }


    // Show edit form
    public function edit(BatchDay $batchDay)
    {
        $batchDay->load('times');
        return view('admin.batch_days.edit', compact('batchDay'));
    }

    // Update batch day + batch times
    public function update(Request $request, BatchDay $batchDay)
    {
        $request->validate([
            'days' => 'required|string',
            'times' => 'required|array|min:1',
            'times.*' => 'required|string',
        ]);

        $submittedTimes = array_map('trim', $request->times);

        // 1️⃣ Prevent duplicate times inside form
        if (count($submittedTimes) !== count(array_unique($submittedTimes))) {
            return back()
                ->withErrors(['times' => 'Duplicate time slots are not allowed in the form submission.'])
                ->withInput();
        }

        // 2️⃣ Check if the same day+time exists in ANY BatchDay EXCEPT the current one
        foreach ($submittedTimes as $time) {
            
            // Check if another BatchDay (with the same 'days' string) has this time
            $exists = BatchDay::where('days', $request->days)
                ->where('id', '!=', $batchDay->id) // Exclude the current BatchDay record
                ->whereHas('times', function ($q) use ($time) {
                    $q->where('time', $time);
                })
                ->exists();

            if ($exists) {
                return back()->withErrors([
                    'times' => "The time slot '$time' already exists for the days '{$request->days}' in another batch."
                ])->withInput();
            }
            
            // Additionally, check if the days string is being changed, but a time conflicts with an existing day+time in a DIFFERENT batch.
            if ($request->days != $batchDay->days) {
                $conflict = BatchDay::where('days', $request->days)
                    ->whereHas('times', function ($q) use ($time) {
                        $q->where('time', $time);
                    })
                    ->exists();
                
                if ($conflict) {
                    return back()->withErrors([
                        'times' => "The time slot '$time' conflicts with an existing batch using the days '{$request->days}'."
                    ])->withInput();
                }
            }
        }
        
        // 3️⃣ Update batch day
        $batchDay->update(['days' => $request->days]);

        // 4️⃣ Sync new times: Delete all old times and recreate the new ones
        $batchDay->times()->delete();
        foreach ($submittedTimes as $time) {
            BatchTime::create([
                'batch_day_id' => $batchDay->id,
                'time' => $time,
            ]);
        }

        return redirect()
            ->route('admin.batch-days.index')
            ->with('success', 'Batch Day updated successfully.');
    }


    // Delete batch day + times
    public function destroy(BatchDay $batchDay)
    {
        // Add a check to prevent deletion if there are associated students
        if ($batchDay->students()->count() > 0) {
             return back()->with('error', 'Cannot delete batch day as there are students associated with it.');
        }

        $batchDay->times()->delete();
        $batchDay->delete();

        return redirect()->route('admin.batch-days.index')->with('success', 'Batch Day deleted successfully.');
    }
}