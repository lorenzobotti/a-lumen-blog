<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Exceptions\NotYoursException;
use App\Exceptions\ServerErrorException;
use App\Helpers\ExceptionHelper;
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
            throw new ServerErrorException();
        }

        return $comment;
    }

    public function getCommentById(int $id)
    {
        /** @var Comment|null $comment */
        $comment = Comment::with(['post', 'post.user', 'user', 'likes'])->find($id);
        if (!$comment) {
            throw new NotFoundException();
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
            throw new NotFoundException();
        }

        if (!$user->canEditOrDelete($comment)) {
            throw new NotYoursException();
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
            throw new NotFoundException();
        }

        if (!$user->canEditOrDelete($comment)) {
            throw new NotYoursException();
        }

        $deleted = $comment->delete();
        if (!$deleted) {
            throw new ServerErrorException();
        }
    }

    public function likeComment(int $id) {
        /** @var User $user */
        $user = Auth::user();

        /** @var Comment|null $comment */
        $comment = Comment::find($id);
        if (!$comment) {
            throw new NotFoundException();
        }

        $like = new CommentLike();
        $like->user_id = $user->id;
        $like->comment_id = $comment->id;

        try {
            $saved = $like->save();
            if (!$saved) {
                throw new ServerErrorException();
            }
        } catch(QueryException $e) {
            if (ExceptionHelper::isDuplicate($e)) {
                // TODO: eccezione apposta
                return new Response('', 409);
            } else {
                throw new ServerErrorException();
            }
        }
    }


    public function unlikeComment(int $id) {
        /** @var User $user */
        $user = Auth::user();

        /** @var CommmentLike|null $like */
        $like = CommentLike::where('user_id', $user->id)->where('comment_id', $id)->first();
        if (!$like) {
            throw new NotFoundException();
        }

        $deleted = $like->forceDelete();
        if (!$deleted) {
            throw new ServerErrorException();
        }
    }

}
