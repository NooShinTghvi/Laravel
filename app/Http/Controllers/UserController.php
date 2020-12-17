<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function test(): array
    {
        return $this->myResponse('success', ['hi'], []);
    }

    public function register(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:75',
            'last_name' => 'required|max:75',
            'mobile' => 'required|max:11',
            'email' => 'email|required|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);
        if ($validator->fails()) {
            return $this->myResponse('error', $validator->errors()->all(), []);
        }

        $user = User::create([
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'mobile' => $request->get('mobile'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password'))
        ]);

        $accessToken = $user->createToken('authToken')->accessToken;

        return $this->myResponse('success', [], ['user' => $user, 'access_token' => $accessToken]);
    }

    public function login(Request $request): array
    {
        $loginData = $request->validate([
            'email' => 'email|min:8|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return $this->myResponse('error', ['Invalid Credentials'], []);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return $this->myResponse('success', [], ['user' => auth()->user(), 'access_token' => $accessToken]);
    }

    public function me(): array
    {
        return $this->myResponse('success', [], ['user' => auth()->user()]);
    }

    public function accountCharging(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required|integer|gt:1000',
        ]);
        if ($validator->fails()) {
            return $this->myResponse('error', $validator->errors()->all(), []);
        }
        $user = Auth::user();
        $user->credit += $request->get('value');
        $user->save();
        return $this->myResponse('success', [], []);
    }
}
