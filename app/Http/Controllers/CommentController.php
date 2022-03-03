<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }



    public function createComment(Request $request) {
        $user = Auth::user();

        $comment = new Comment();


        $comment.fill($request->all());
        $comment['post_id'] = $request->input('post_id');

        return Comment::create([
            'content' => $content,
            'post_id' => $post_id,
            'user_id' => $user['id'],
        ]);
    }

    public function getCommentById(int $id) {
        $comment = Comment::with([])->find($id)->first();
        if (!$comment) {
            return new Response('', 404);
        }

        return $comment;
    }

    public function editComment(int $id, Request $request) {
        $user = Auth::user();
        $comment = Comment::find($id);
        if (!$comment) {
            return new Response('', 404);
        }

        if ($comment['user_id'] !== $user['id']) {
            return new Response('', 401);
        }

        $comment->update($request->all());
        return $comment;
    }



    public function deleteComment(int $id) {
        $user = Auth::user();
        $comment = Comment::find($id);
        if (!$comment) {
            return new Response('', 404);
        }

        if ($comment['user_id'] !== $user['id']) {
            return new Response('', 401);
        }

        $comment->delete();
    }
}
