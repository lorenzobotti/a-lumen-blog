<?php

namespace App\Http\Controllers;

use App\Scopes\OwnerScope;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{

    // use OwnerScope;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Show all users
     *
     */
    public function showAllUsers() {
        return response()->json(User::all());
    }

    public function createPost(Request $request) {
        $user = Auth::user();

        $title = $request->input('title');
        $content = $request->input('content');

        $post = new Post();
        $post->fill([
            'title' => $title,
            'content' => $content,
        ]);
        $post['user_id'] = $user['id'];

        $saved = $post->save();
        if (!$saved) {
            return new Response('', 500);
        }


        return $post;
    }

    public function deletePost(int $id) {
//        $user = Auth::user();

        $post = Post::find($id);
        if (!$post) {
            return new Response('', 404);
        }

        // non serve perché c'è OwnerScope
//        if ($post->user_id != $user->id) {
//            return new Response('', 401);
//        }

        $post->delete();
    }

    public function getPostById(int $id)
    {

        $post = Post::with(['user'])->find($id);
        if (!$post) {
            return new Response('', 404);
        }

        return $post;
    }

    public function allPosts() {
        // eager loading
        return Post::withoutGlobalScope(OwnerScope::class)->with(['user', 'comments', 'comments.user'])->get();
    }
}
