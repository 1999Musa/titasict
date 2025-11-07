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
            'times.*' => 'nullable|string',
        ]);

        $batchDay = BatchDay::create([
            'days' => $request->days,
        ]);

        if ($request->filled('times')) {
            foreach ($request->times as $time) {
                if ($time && !BatchTime::where('batch_day_id', $batchDay->id)->where('time', $time)->exists()) {
                    BatchTime::create([
                        'batch_day_id' => $batchDay->id,
                        'time' => $time,
                    ]);
                }
            }
        }

        return redirect()->route('admin.batch-days.index')->with('success', 'Batch Day created successfully.');
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
            'times.*' => 'nullable|string',
        ]);

        $batchDay->update([
            'days' => $request->days,
        ]);

        $batchDay->times()->delete();

        if ($request->filled('times')) {
            foreach ($request->times as $time) {
                if ($time && !BatchTime::where('batch_day_id', $batchDay->id)->where('time', $time)->exists()) {
                    BatchTime::create([
                        'batch_day_id' => $batchDay->id,
                        'time' => $time,
                    ]);
                }
            }
        }

        return redirect()->route('admin.batch-days.index')->with('success', 'Batch Day updated successfully.');
    }

    // Delete batch day + times
    public function destroy(BatchDay $batchDay)
    {
        $batchDay->times()->delete();
        $batchDay->delete();

        return redirect()->route('admin.batch-days.index')->with('success', 'Batch Day deleted successfully.');
    }
}
