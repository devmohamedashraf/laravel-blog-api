<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string',
        ]);
        // validate if the post has comment from this user or not
        $comment = Comment::where('user_id', auth()->user()->id)
            ->where('post_id', $request->post_id)
            ->first();
        if ($comment) {
            return response()->json([
                'message' => 'You have already commented on this post',
            ], 403);
        }
        $comment = new Comment();
        $comment->user_id = auth()->user()->id;
        $comment->post_id = $request->post_id;
        $comment->content = $request->content;
        $comment->save();
        return response()->json([
            'message' => 'Comment created successfully',
            'comment' => $comment,
        ]);
    }

    public function update(Request $request, Comment $comment)
    {
        $user = auth()->user();
        if ($user->id !== $comment->user_id) {
            return response()->json([
                'message' => 'You are not authorized to update this comment',
            ], 403);
        }
        $request->validate([
            'content' => 'required|string',
        ]);
        $comment->comment = $request->content;
        $comment->save();
        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment,
        ]);
    }

    public function destroy(Comment $comment)
    {
        $user = auth()->user();
        if ($user->id !== $comment->user_id) {
            return response()->json([
                'message' => 'You are not authorized to delete this comment',
            ], 403);
        }
        $comment->delete();
        return response()->json([
            'message' => 'Comment deleted successfully',
        ]);
    }
}
