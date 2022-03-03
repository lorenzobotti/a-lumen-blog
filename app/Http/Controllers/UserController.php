<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Helpers\TokenGenerator;

class UserController extends Controller
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

    /**
     * Show all users
     *
     */
    public function showAllUsers() {
        return response()->json(User::all());
    }

    public function getById($id) {
        return response()->json(User::find($id));
    }

    public function login(Request $request) {
        $this->validate($request, [
            'email' => 'requires|email',
            'password' => 'required',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();
        if (!$user) {
            return new Response($status = 404);
        }

        $saved_password = $user['password'];
        if (!password_verify($password, $saved_password)) {
            return new Response('', 401);
        };

        return response()->json($user['api_token']);
    }

    public function newToken(Request $request) {
        $userId = Auth::user()['id'];

        $newToken = TokenGenerator::generateRandomString(64);

        $user = User::find($userId);
        $user['api_token'] = $newToken;
        $saved = $user->save();
        if (!$saved) {
            return new Response('', 500);
        }

        return $newToken;
    }

//    public function getByToken(string $token) {
//        return response()->json(User::where('token', $token)->first());
//    }



    public function create(Request $request) {
        $this->validate($request, [
            'email' => 'requires|email',
            'password' => 'required',
        ]);

        $fields = $request->all();
        $fields['api_token'] = TokenGenerator::generateRandomString(64);
        $user = User::create($fields);
        return $user;
    }
}
