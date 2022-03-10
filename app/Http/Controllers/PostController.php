<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Exceptions\NotPremiumException;
use App\Exceptions\NotYoursException;
use App\Exceptions\ServerErrorException;
use App\Helpers\ExceptionHelper;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\PostLike;
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
            'categories' => 'array',
            'categories.*' => 'string',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $title = $request->input('title');
        $content = $request->input('content');
        /** @var string[]|null $categories */
        $categories = $request->input('categories');

        /** @var Post $post */
        $post = new Post();

        $post->user_id = $user->id;
        $post->title = $title;
        $post->content = $content;

        $saved = $post->save();
        if (!$saved) {
            return new Response('', 500);
        }

        if ($categories) {
            foreach ($categories as $categoryName) {
                $categoryModel = Category::createIfNotExist($categoryName);
                $post->categories()->save($categoryModel);
            }
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
            //return new Response('', 404);
            throw new NotFoundException();
        }

        $canDelete = $post->user_id == $user->id || $user->isMod();
        if (!$canDelete) {
            // return new Response('', 401);
            throw new NotYoursException();
        }

        $deleted = $post->delete();
        if (!$deleted) {
            throw new ServerErrorException();
        }
    }

    public function getPostById(int $id)
    {
        /** @var Post|null $post */
        $post = Post::with(['user', 'comments', 'comments.user', 'categories'])->find($id);
        if (!$post) {
            throw new NotFoundException();
        }


        $user = Auth::user();
        if ($user) {
            $userLikedPost = PostLike::where('user_id', $user->id)->where('post_id', $id)->get() != null;
            $post->fillJsonAttribute('liked_by_you->', $userLikedPost);
        }
        // $post->attributes['likes'] = $post->like_count;

        return $post;
    }

    public function allPosts()
    {
        /** @var Post[] $post */
        return Post::with(['user', 'comments', 'comments.user', 'categories'])->get();
    }

    public function getLikesByPostId(int $id)
    {
        /** @var Post|null $post */
        $post = Post::find($id);
        if (!$post) {
            throw new NotFoundException();
        }

        $likes = $post->likes()->get();
        return $likes;
    }

    public function likePost(int $id)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user) {
            return new Response('', 401);
        }

        /** @var Post|null $post */
        $post = Post::find($id);
        if (!$post) {
            throw new NotFoundException();
        }

        $like = new PostLike();
        $like->user_id = $user->id;
        $like->post_id = $post->id;

        try {
            $saved = $like->save();
            if (!$saved) {
                throw new ServerErrorException();
            }
        } catch (QueryException $e) {
            if (ExceptionHelper::isDuplicate($e)) {
                // TODO: creare eccezione apposta
                return new Response('', 409);
            } else {
                throw new ServerErrorException();
            }
        }
    }

    public function unlikePost(int $id)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user) {
            return new Response('', 401);
        }

        /** @var PostLike|null $like */
        $like = PostLike::where('user_id', $user->id)->where('post_id', $id)->first();
        if (!$like) {
            throw new NotFoundException();
        }

        // TODO: se non metto forceDelete poi quando vado a rimettere il like
        // pensa che esista già.
        $deleted = $like->forceDelete();
        if (!$deleted) {
            throw new ServerErrorException();
        }
    }
}