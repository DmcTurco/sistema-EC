<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $post = Post::where('Staff_id',Auth::id())
                ->latest()
                ->get();

        return view('staff.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $products = Product::where('status', 1)->get();
        return view('staff.posts.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'intro_video' => 'required|file|mimes:mp4,mov,avi|max:51200',
        ]);

        $path = $request->file('intro_video')->store('posts', 'public');

        Post::create([
            'staff_id' => Auth::id(),
            'product_id' => $request->product_id,
            'intro_video_path' => $path,
            'status' => $request->status ? 1 : 0,
        ]);

        return redirect()->route('staff.posts.index')->with('success', 'Post creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        abort_if($post->staff_id !== Auth::id(), 403);
        $products = Product::where('status', 1)->get();
        return view('staff.posts.edit', compact('post', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        abort_if($post->staff_id !== Auth::id(), 403);

        $request->validate([
            'product_id' => 'required|integer',
            'intro_video' => 'nullable|file|mimes:mp4,mov,avi|max:51200',
        ]);

        if ($request->hasFile('intro_video')) {
            Storage::disk('public')->delete($post->intro_video_path);
            $post->intro_video_path = $request->file('intro_video')->store('posts', 'public');
        }

        $post->update([
            'product_id' => $request->product_id,
            'status' => $request->status ? 1 : 0,
        ]);

        return redirect()->route('staff.posts.index')->with('success', 'Post actualizado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        abort_if($post->staff_id !== Auth::id(), 403);
        $post->delete();

        return back()->with('success', 'Post eliminado');
    }
}
