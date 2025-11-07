<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BatchTime;
use App\Models\BatchDay;

class BatchTimeController extends Controller
{
    public function index()
    {
        $batchTimes = BatchTime::with('batchDay.batch')->latest()->paginate(10);
        return view('admin.batch_times.index', compact('batchTimes'));
    }

    public function create()
    {
        $batchDays = BatchDay::with('batch')->orderBy('id', 'desc')->get();
        return view('admin.batch_times.create', compact('batchDays'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'batch_day_id' => 'required|exists:batch_days,id',
            'time' => 'required|string|max:255|unique:batch_times,time,NULL,id,batch_day_id,' . $request->batch_day_id,
        ]);

        BatchTime::create($data);

        return redirect()->route('admin.batch-times.index')->with('success', 'Batch Time created successfully.');
    }

    public function edit(BatchTime $batchTime)
    {
        $batchDays = BatchDay::with('batch')->orderBy('id', 'desc')->get();
        return view('admin.batch_times.edit', compact('batchTime', 'batchDays'));
    }

    public function update(Request $request, BatchTime $batchTime)
    {
        $data = $request->validate([
            'batch_day_id' => 'required|exists:batch_days,id',
            'time' => 'required|string|max:255|unique:batch_times,time,' . $batchTime->id . ',id,batch_day_id,' . $request->batch_day_id,
        ]);

        $batchTime->update($data);

        return redirect()->route('admin.batch-times.index')->with('success', 'Batch Time updated successfully.');
    }

    public function destroy(BatchTime $batchTime)
    {
        $batchTime->delete();
        return back()->with('success', 'Batch Time deleted successfully.');
    }
}
