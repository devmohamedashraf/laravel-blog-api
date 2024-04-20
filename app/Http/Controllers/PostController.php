<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePost;
use App\Http\Requests\UpdatePost;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::paginate(15);
        return response()->json($posts);
    }

    public function userPosts()
    {
        $user = auth()->user();
        $posts = Post::where('user_id', $user->id)->get();
        return response()->json($posts);
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->with('user')->first();
        return response()->json($post);
    }

    public function store(StorePost $request)
    {
        $user = auth()->user();
        $post = new Post();
        $post->user_id = $user->id;
        $post->slug = $request->slug;
        $post->title = $request->title;
        $post->content = $request->content;
        $post->thumbnail = $this->storeThumbnail($post, $request);
        $post->is_published = $request->is_published ? $request->is_published : false;
        $post->published_at = $request->published_at ? $request->published_at : null;
        $post->save();
        return response()->json(['message' => 'Post created successfully', 'data' => $post]);
    }

    public function update(UpdatePost $request, Post $post)
    {
        $user = auth()->user();
        if ($user->id !== $post->user_id) {
            return response()->json(['message' => 'You are not authorized to update this post'], 403);
        }
        $post->slug = $request->slug;
        $post->title = $request->title;
        $post->content = $request->content;
        $post->is_published = $request->is_published;
        $post->published_at = $request->published_at;

        if ($request->hasFile('thumbnail')) {
            $post->thumbnail = $this->storeThumbnail($post, $request);
        }
        $post->save();
        return response()->json(['message' => 'Post updated successfully', 'data' => $post]);
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['message' => 'Post deleted successfully']);
    }

    private function storeThumbnail(Post $post, $request)
    {
        // delete old thumbnail
        if ($post->thumbnail) {
            Storage::disk('public')->delete($post->thumbnail);
        }
        $thumbnail = $request->file('thumbnail');
        $name = str()->uuid() . '.' . $thumbnail->getClientOriginalExtension();
        $thumbnail = Storage::disk('public')->putFileAs('posts', $thumbnail, $name);
        return $thumbnail;
    }

    public function filter(Request $request)
    {
        $posts = Post::where('is_published', true);

        if ($request->has('title')) {
            $posts->where('title', 'like', '%' . $request->title . '%');
        }
        $posts = $posts->paginate(15);
        return response()->json($posts);
    }
}
