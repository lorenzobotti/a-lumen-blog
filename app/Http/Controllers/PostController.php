<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function showAllUsers()
    {
        return User::all();
    }

    public function createPost(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $title = $request->input('title');
        $content = $request->input('content');

        /** @var Post $post */
        $post = new Post();

        $post->user_id = $user->id;
        $post->title = $title;
        $post->content = $content;

        $saved = $post->save();
        if (!$saved) {
            return new Response('', 500);
        }

        return $post;
    }

    public function deletePost(int $id)
    {
        /** @var User $user */
        $user = Auth::user();

        /** @var Post $post */
        $post = Post::find($id);
        if (!$post) {
            return new Response('', 404);
        }

        $canDelete = $post->user_id == $user->id || $user->isMod();
        if (!$canDelete) {
            return new Response('', 401);
        }

        $deleted = $post->delete();
        if (!$deleted) {
            return new Response('', 500);
        }
    }

    public function getPostById(int $id)
    {
        /** @var Post $post */
        $post = Post::with(['user', 'comments', 'comments.user'])->find($id);
        if (!$post) {
            return new Response('', 404);
        }

        return $post;
    }

    public function allPosts()
    {
        /** @var Post $post */
        return Post::with(['user', 'comments', 'comments.user'])->get();
    }
}
