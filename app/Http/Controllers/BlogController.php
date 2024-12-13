<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::where('user_id', auth()->id())->get()->map(function ($blog) {
            if ($blog->image) {
                $blog->image = Storage::url($blog->image);
            }
            return $blog;
        });

        return response()->json($blogs, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
        }

        $blog = Blog::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'image' => $path,
        ]);

        if ($blog->image) {
            $blog->image = Storage::url($blog->image);
        }

        return response()->json($blog, 201);
    }

    public function update(Request $request, Blog $blog)
    {
        if ($blog->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        ]);

        $blog->update($request->only(['title', 'content']));

        if ($request->hasFile('image')) {
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }

            $path = $request->file('image')->store('images', 'public');
            $blog->image = $path;
            $blog->save();
        }

        if ($blog->image) {
            $blog->image = Storage::url($blog->image);
        }

        return response()->json($blog, 200);
    }

    public function destroy(Blog $blog)
    {
        if ($blog->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return response()->json(null, 204);
    }
}
