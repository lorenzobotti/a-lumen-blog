<?php

namespace App\Http\Controllers;

use App\Scopes\OwnerScope;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function createComment(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|string',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $comment = new Comment();
        $comment->fill($request->all());

        $comment->post_id = $request->input('post_id');
        $comment->user_id = $user->id;

        $saved = $comment->save();
        if (!$saved) {
            return new Response('', 500);
        }

        return $comment;
    }

    public function getCommentById(int $id)
    {
        /** @var Comment|null $comment */
        $comment = Comment::find($id);
        if (!$comment) {
            return new Response('', 404);
        }

        return $comment;
    }

    public function editComment(int $id, Request $request)
    {
        $this->validate($request, [
            'content' => 'required|string',
        ]);

        /** @var User $user */
        $user = Auth::user();

        /** @var Comment|null $comment */
        $comment = Comment::find($id);
        if (!$comment) {
            return new Response('', 404);
        }

        if (!$comment->canEditOrDelete($user)) {
            return new Response('', 401);
        }


        $comment->update($request->all());
        return $comment;
    }


    public function deleteComment(int $id)
    {
        /** @var User $user */
        $user = Auth::user();

        /** @var Comment|null $comment */
        $comment = Comment::find($id);
        if (!$comment) {
            return new Response('', 404);
        }

        if (!$comment->canEditOrDelete($user)) {
            return new Response('', 401);
        }

        $deleted = $comment->delete();
        if (!$deleted) {
            return new Response('', 500);
        }
    }
}
