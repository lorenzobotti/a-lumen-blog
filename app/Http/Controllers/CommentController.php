<?php

namespace App\Http\Controllers;

use App\Helpers\IsDuplicate;
use App\Models\CommentLike;
use App\Scopes\OwnerScope;
use Illuminate\Database\QueryException;
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
        $comment = Comment::with(['post', 'post.user', 'user', 'likes'])->find($id);
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

    public function likeComment(int $id) {
        /** @var User $user */
        $user = Auth::user();

        /** @var Comment|null $comment */
        $comment = Comment::find($id);
        if (!$comment) {
            return new Response('', 404);
        }

        $like = new CommentLike();
        $like->user_id = $user->id;
        $like->comment_id = $comment->id;

        try {
            $saved = $like->save();
            if (!$saved) {
                return new Response('', 500);
            }
        } catch(QueryException $e) {
            if (IsDuplicate::isDuplicate($e)) {
                return new Response('', 409);
            } else {
                return new Response('', 500);
            }
        }
    }


    public function unlikeComment(int $id) {
        /** @var User $user */
        $user = Auth::user();

        /** @var CommmentLike|null $like */
        $like = CommentLike::where('user_id', $user->id)->where('comment_id', $id)->first();
        if (!$like) {
            return new Response('', 404);
        }

        $deleted = $like->forceDelete();
        if (!$deleted) {
            return new Response('', 500);
        }
    }

}
