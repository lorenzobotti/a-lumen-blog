<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Helpers\TokenGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function showAllUsers()
    {
        return User::all();
    }

    public function getById($id)
    {
        $user = User::with('categories')->find($id);
        if (!$user) {
            return new Response('', 404);
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
            return new Response($status = 404);
        }

        $saved_password = $user->password;
        if (!password_verify($password, $saved_password)) {
            return new Response('', 401);
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
            return new Response('', 500);
        }

        return $newToken;
    }

    public function banUser($id)
    {
        /** @var User $user */
        $banner = Auth::user();

        if (!$banner || !$banner->isMod()) {
            return new Response('', 401);
        }

        /** @var User|null $banned */
        $banned = User::find($id);
        if (!$banned) {
            return new Response('', 404);
        }

        $banned->banned_at = new \DateTime();
        $saved = $banned->save();
        if (!$saved) {
            return new Response('', 500);
        }
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $fields = $request->all();

        $user = new User();
        $user->fill($fields);
        $user->api_token = TokenGenerator::generateRandomString(64);

        $saved = $user->save();
        if (!$saved) {
            return new Response('', 500);
        }
        return $user;
    }

    public function postsFromFavorites() {
        /** @var User $user */
        $user = Auth::user();

        // return $user->favorites()->get();

        // TODO: non ho idea di come risolvere questo problema senza usare il query builder
        // TODO: attendere approvazione di Stefano
        // return Category::with(['posts', 'users'])->has('user')->where('user.id', $user->id)->get();
        // return Category::with(['posts', 'users'])->has('users.id', '=', $user->id)->get();
//        return Category::with(['posts', 'users', 'posts.categories'])->whereHas('users', function ($query) use ($user) {
//            $query->where('users.id', $user->id);
//        })->get();

        // TODO: fare coi modelli
        return DB::table('posts')
            ->join('posts_categories', 'posts_categories.post_id', '=', 'posts.id')
            ->join('favorite_categories', 'posts_categories.category_id', '=', 'favorite_categories.category_id')
            ->where('favorite_categories.user_id', $user->id)
            ->get();
    }
}
