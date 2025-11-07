<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
   
    public function index()
    {
        $categories = Category::latest()->paginate(12);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'hero_image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store('categories', 'public');
            $data['hero_image'] = $path;
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success','Category created.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'title' => 'required|string|unique:categories,title,'.$category->id,
            'description' => 'nullable|string',
            'hero_image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('hero_image')) {
            // delete old if exists
            if ($category->hero_image) \Storage::disk('public')->delete($category->hero_image);
            $path = $request->file('hero_image')->store('categories', 'public');
            $data['hero_image'] = $path;
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success','Category updated.');
    }

    public function destroy(Category $category)
    {
        // deleting category will cascade to products & images due to FK cascade
        if ($category->hero_image) \Storage::disk('public')->delete($category->hero_image);
        $category->delete();
        return back()->with('success','Category deleted.');
    }
}
