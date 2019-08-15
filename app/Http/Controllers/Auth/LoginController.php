<?php

namespace App\Http\Controllers\Auth;

use Socialite;
use App\Models\User;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function index()
    {
        return Socialite::with('bitbucket')->redirect();
    }

    public function store()
    {
        $user = Socialite::with('bitbucket')->user();

        $user = User::updateOrCreate(
            ['bitbucket_id' => $user->id],
            [
                'bitbucket_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'access_token' => $user->token,
                'refresh_token' => $user->refreshToken,
            ]
        );

        auth()->login($user);

        return redirect('/');
    }

    public function destroy()
    {
        auth()->logout();

        return redirect('/');
    }
}
