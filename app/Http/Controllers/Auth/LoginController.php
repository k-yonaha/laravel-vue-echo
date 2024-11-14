<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            return response()->json(
                [
                    'message' => 'ログイン成功',
                    'user' => Auth::user(),
                ], 200
            );
        }

        return response()->json(['message' => '認証できませんでした。'], 401);
    }
}
