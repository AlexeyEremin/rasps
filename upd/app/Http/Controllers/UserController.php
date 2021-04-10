<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request) {

        $valid = Validator::make($request->all(), [
           'login' => 'required|exists:users',
           'password' => 'required'
        ]);

        if($valid->fails())
            return response($valid->errors(), 400);

        $user = User::where('login', $request->login)->first();
        if(!Hash::check($request->password, $user->password))
            return response($valid->errors()->add('field', 'Не верный логин или пароль'), 400);

        $token = Token::addToken($request, $user, $request->stay);

        return response(['token' => $token], 201);
    }
}
