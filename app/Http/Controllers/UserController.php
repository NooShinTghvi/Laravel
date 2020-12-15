<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function test(): string
    {
        return 'hi';
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|max:75',
            'last_name' => 'required|max:75',
            'mobile' => 'required|max:11',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $validatedData['password'] = bcrypt($request->password);

        $user = User::create($validatedData);

        $accessToken = $user->createToken('authToken')->accessToken;

        return response(['user' => $user, 'access_token' => $accessToken]);
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'Invalid Credentials']);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken]);

    }

//    public function accountCharging(Request $request): int
//    {
//        $user = Auth::user();
//        if ($request->filled('value')) {
//            $user->credit += $request->get('value');
//            $user->save();
//            return 202;
//        }
//        return 203;
//    }
}
