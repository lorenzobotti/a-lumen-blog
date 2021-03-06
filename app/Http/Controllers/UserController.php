<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Exceptions\NotPremiumException;
use App\Exceptions\ServerErrorException;
use App\Helpers\ExceptionHelper;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Helpers\TokenGenerator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function showAllUsers()
    {
        return User::all();
    }

    public function getById($id)
    {
        /** @var User|null $user */
        $user = User::with(['categories', 'posts'])->find($id);
        if (!$user) {
            throw new NotFoundException();
        }

        return $user;
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        /** @var User $user */
        $user = User::where('email', $email)->first();
        if (!$user) {
            throw new NotFoundException();
        }

        $saved_password = $user->password;
        if (!password_verify($password, $saved_password)) {
            throw new WrongCredentialsException();
        };

        return $user->api_token;
    }

    public function newToken(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $newToken = TokenGenerator::generateRandomString(64);

        $user->api_token = $newToken;
        $saved = $user->save();
        if (!$saved) {
            throw new ServerErrorException();
        }

        return $newToken;
    }

    public function banUser($id)
    {
        /** @var User|null $user */
        $banner = Auth::user();

        if (!$banner || !$banner->isMod()) {
            throw new NotPremiumException();
        }

        /** @var User|null $banned */
        $banned = User::find($id);
        if (!$banned) {
            throw new NotFoundException();
        }

        $banned->banned_at = new \DateTime();
        $saved = $banned->save();
        if (!$saved) {
            throw new ServerErrorException();
        }
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = new User();
        $user->email = $request->input('email');
        $user->password = $request->input('password');
        $user->api_token = TokenGenerator::generateRandomString(64);

        $saved = $user->save();
        if (!$saved) {
            throw new ServerErrorException();
        }
        return $user;
    }

    public function postsFromFavorites() {
        /** @var User $user */
        $user = Auth::user();

        $favorites = $user->categories()->pluck('category_id');
        return Post::with(['user', 'comments', 'comments.user', 'categories'])
            ->whereHas('categories', function($query) use ($favorites) {
                $query->whereIn('category_id', $favorites);
            })->get();

//        return DB::table('posts')
//            ->join('posts_categories', 'posts_categories.post_id', '=', 'posts.id')
//            ->join('favorite_categories', 'posts_categories.category_id', '=', 'favorite_categories.category_id')
//            ->where('favorite_categories.user_id', $user->id)
//            ->get();
    }

    public function addFavorite(Request $request) {
        $this->validate($request, [
            'categories' => 'required|array',
            'categories.*' => 'string',
        ]);

        /** @var User $user */
        $user = Auth::user();

        /** @var string[] $names */
        $names = $request->input('categories');

        foreach ($names as $name) {
            try {
                $category = Category::createIfNotExist($name);
                echo $category->id;
                $user->categories()->save($category);
            } catch (QueryException $e) {
                if (!ExceptionHelper::isDuplicate($e)) {
                    throw new ServerErrorException();
                }
            }
        }
    }

    public function removeFavorite(Request $request) {
        $this->validate($request, [
            'categories' => 'required|array',
            'categories.*' => 'string',
        ]);

        /** @var User $user */
        $user = Auth::user();

        /** @var string[] $names */
        $names = $request->input('categories');
        foreach($names as $name) {
            /** @var Category|null $category */
            $category = Category::where('name', $name);

            if (!$category) {
                continue;
            }

            $user->categories()->delete($category);
        }
    }

    public function getFavorites(Request $request) {
        /** @var User $user */
        $user = Auth::user();

        return $user->categories()->get();
    }
}
