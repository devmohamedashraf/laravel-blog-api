<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function action(Request $request, Post $post)
    {
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        $user = auth()->user();
        $action = $request->action;
        $current = $post->getLikes()->where('user_id', $user->id)->first();

        switch ($action) {
            case 'like':
                $this->likeCommand($current, $post);
                break;
            case 'dislike':
                $this->dislikeCommand($current, $post);
                break;
            case 'remove':
                $this->removeCommand($current, $post);
                break;
            default:
                return response()->json(['message' => 'Invalid action'], 400);
        }
        return response()->json(['message' => 'Success']);
    }

    private function likeCommand($current, $post)
    {
        if ($current) {
            if ($current->liked) {
                return response()->json(['message' => 'You already liked this post'], 400);
            } else {
                $post->decreaseDislikes();
            }
            $current->liked = true;
            $current->save();
        } else {
            Like::create([
                'user_id' => auth()->id(),
                'post_id' => $post->id,
                'liked' => true,
            ]);
        }
        $post->increaseLikes();
    }

    private function dislikeCommand($current, $post)
    {
        if ($current) {
            if (!$current->liked) {
                return response()->json(['message' => 'You already disliked this post'], 400);
            } else {
                $post->decreaseLikes();
            }
            $current->liked = false;
            $current->save();
        } else {
            Like::create([
                'user_id' => auth()->id(),
                'post_id' => $post->id,
                'liked' => false,
            ]);
        }
        $post->increaseDislikes();
    }

    private function removeCommand($current, $post)
    {
        if ($current->liked) $post->decreaseLikes();
        else $post->decreaseDislikes();
        $current->delete();
    }
}
