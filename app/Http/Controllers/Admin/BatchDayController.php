<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BatchDay;
use App\Models\BatchTime;
use Illuminate\Http\Request;

class BatchDayController extends Controller
{
    // -------------------------------
    // Normalize batch name
    // -------------------------------
    private function normalizeName($name)
    {
        return strtolower(preg_replace('/[^a-zA-Z]/', '', $name));
    }

    // -------------------------------
    // Validate "10 AM - 11 AM"
    // -------------------------------
    private function isValidTimeSlot($slot)
    {
        return preg_match('/^\d{1,2}\s?(AM|PM)\s?-\s?\d{1,2}\s?(AM|PM)$/i', $slot);
    }

    // -------------------------------
    // Extract starting time "10 AM"
    // -------------------------------
    private function getStartTime($slot)
    {
        return trim(explode('-', $slot)[0]);
    }

    // -------------------------------
    // INDEX
    // -------------------------------
    public function index()
    {
        $batchDays = BatchDay::with('times')->latest()->paginate(10);
        return view('admin.batch_days.index', compact('batchDays'));
    }

    // -------------------------------
    // CREATE
    // -------------------------------
    public function create()
    {
        return view('admin.batch_days.create');
    }

    // -------------------------------
    // STORE
    // -------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'days' => 'required|string',
            'times' => 'required|array|min:1',
        ]);

        $normalized = $this->normalizeName($request->days);
        $submitted = array_map('trim', $request->times);

        // ❌ Block decimal formats: "10.00 AM"
        foreach ($submitted as $slot) {
            if (preg_match('/\d+\.\d+/', $slot)) {
                return back()->withErrors([
                    'times' => "Invalid format '$slot'. Do NOT use decimal times like '10.00 AM'. Use '10 AM - 11 AM'."
                ])->withInput();
            }

            if (!$this->isValidTimeSlot($slot)) {
                return back()->withErrors([
                    'times' => "The slot '$slot' is invalid. Use format: 10 AM - 11 AM."
                ])->withInput();
            }
        }

        // ❌ Unique start time globally
        // $start = $this->getStartTime($submitted[0]);

        // $existsStart = BatchTime::where('time', 'LIKE', "$start -%")->exists();
        // if ($existsStart) {
        //     return back()->withErrors([
        //         'times' => "Another batch already starts at '$start'. Start times must be unique."
        //     ])->withInput();
        // }

        // ❌ Duplicate batch names (ignoring symbols/spaces)
        foreach (BatchDay::all() as $bd) {
            if ($this->normalizeName($bd->days) === $normalized) {
                return back()->withErrors([
                    'days' => "A batch similar to '{$request->days}' already exists."
                ])->withInput();
            }
        }

        // ❌ Duplicate time slots in same form
        if (count($submitted) !== count(array_unique($submitted))) {
            return back()->withErrors([
                'times' => "Duplicate time slots are not allowed."
            ])->withInput();
        }

        // ✔ Save
        $batch = BatchDay::create(['days' => $request->days]);

        foreach ($submitted as $slot) {
            BatchTime::create([
                'batch_day_id' => $batch->id,
                'time' => $slot
            ]);
        }

        return redirect()
            ->route('admin.batch-days.index')
            ->with('success', 'Batch created successfully.');
    }

    // -------------------------------
    // EDIT
    // -------------------------------
    public function edit(BatchDay $batchDay)
    {
        $batchDay->load('times');
        return view('admin.batch_days.edit', compact('batchDay'));
    }

    // -------------------------------
    // UPDATE
    // -------------------------------
    public function update(Request $request, BatchDay $batchDay)
{
    $request->validate([
        'days'  => 'required|string',
        'times' => 'required|array|min:1',
    ]);

    $submitted = array_map('trim', $request->times);

    // Validate format
    foreach ($submitted as $slot) {
        if (preg_match('/\d+\.\d+/', $slot)) {
            return back()->withErrors([
                'times' => "Invalid slot '$slot'. Remove decimal (use '10 AM', not '10.00 AM')."
            ])->withInput();
        }

        if (!preg_match('/^\d{1,2}\s?(AM|PM)\s?-\s?\d{1,2}\s?(AM|PM)$/i', $slot)) {
            return back()->withErrors([
                'times' => "Invalid format '$slot'. Use: 10 AM - 11 AM."
            ])->withInput();
        }
    }

    // Prevent duplicate batch names
    $normalized = strtolower(preg_replace('/[^a-zA-Z]/', '', $request->days));
    foreach (BatchDay::where('id', '!=', $batchDay->id)->get() as $bd) {
        if (strtolower(preg_replace('/[^a-zA-Z]/', '', $bd->days)) === $normalized) {
            return back()->withErrors([
                'days' => "Another batch already has a similar name '{$request->days}'."
            ])->withInput();
        }
    }

    // Prevent duplicate time slots
    if (count($submitted) !== count(array_unique($submitted))) {
        return back()->withErrors([
            'times' => "Duplicate time slots are not allowed."
        ])->withInput();
    }

    // Update batch day name
    $batchDay->update(['days' => $request->days]);

    // Get existing times [id => time]
    $existingTimes = $batchDay->times()->pluck('time', 'id')->toArray();

    // Track used IDs
    $usedIds = [];

    foreach ($submitted as $index => $slot) {
        // Try to find an existing time to update
        $existingId = array_search($slot, $existingTimes);
        if ($existingId !== false) {
            // Slot unchanged → keep it
            $usedIds[] = $existingId;
            unset($existingTimes[$existingId]);
        } else {
            // Slot might be an edited version → update the first unused existing slot
            if (!empty($existingTimes)) {
                $firstId = array_key_first($existingTimes);
                $batchDay->times()->where('id', $firstId)->update(['time' => $slot]);
                $usedIds[] = $firstId;
                unset($existingTimes[$firstId]);
            } else {
                // New slot → create
                $newTime = BatchTime::create([
                    'batch_day_id' => $batchDay->id,
                    'time' => $slot
                ]);
                $usedIds[] = $newTime->id;
            }
        }
    }

    // Delete removed slots and unassign students
    foreach ($existingTimes as $id => $oldTime) {
        \App\Models\Student::where('batch_time_id', $id)->update(['batch_time_id' => null]);
        $batchDay->times()->where('id', $id)->delete();
    }

    return redirect()
        ->route('admin.batch-days.index')
        ->with('success', 'Batch updated successfully.');
}


    // -------------------------------
    // DELETE
    // -------------------------------
    public function destroy(BatchDay $batchDay)
    {
        if ($batchDay->students()->count() > 0) {
            return back()->with('error', 'Cannot delete this batch. It has enrolled students.');
        }

        $batchDay->times()->delete();
        $batchDay->delete();

        return redirect()
            ->route('admin.batch-days.index')
            ->with('success', 'Batch deleted successfully.');
    }

}
