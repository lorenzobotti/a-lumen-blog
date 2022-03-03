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
        $this->validate([
            'content' => 'required|string',
        ]);

        $user = Auth::user();

        $comment = new Comment();
        $comment->fill($request->all());

        $comment['post_id'] = $request->input('post_id');
        $comment['user_id'] = $user['id'];

        $saved = $comment->save();
        if (!$saved) {
            return new Response('', 500);
        }

        return $comment;
    }

    public function getCommentById(int $id) {
        $comment = Comment::find($id)->first();
        if (!$comment) {
            return new Response('', 404);
        }

        return $comment;
    }

    public function editComment(int $id, Request $request) {
        $this->validate([
            'content' => 'required|string',
        ]);

        // non dobbiamo controllare che l'utente sia premium perché ci pensa
        // già PremiumMiddleware
        $user = Auth::user();

        $comment = Comment::find($id);
        if (!$comment) {
            return new Response('', 404);
        }

        $author = User::find($user['id']);
        $isAuthor = $author['id'] === $comment['user_id'];
        if (!$isAuthor) {
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
