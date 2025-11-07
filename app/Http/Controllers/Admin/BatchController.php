<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Batch;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::latest()->paginate(10);
        return view('admin.batches.index', compact('batches'));
    }

    public function create()
    {
        return view('admin.batches.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        Batch::create($data);

        return redirect()->route('admin.batches.index')->with('success', 'Batch created successfully.');
    }

    public function edit(Batch $batch)
    {
        return view('admin.batches.edit', compact('batch'));
    }

    public function update(Request $request, Batch $batch)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $batch->update($data);

        return redirect()->route('admin.batches.index')->with('success', 'Batch updated successfully.');
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();
        return back()->with('success', 'Batch deleted successfully.');
    }
}
