<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetSanctumTokenRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function createToken(GetSanctumTokenRequest $request)
    {
        $data = $request->validated();
        if (Auth::attempt($data)) {
            $user = User::whereEmail($data['email'])
                ->first();
            return response()->json([
                'token' => $user->createToken("sanctum_token")->plainTextToken
            ]);
        }
    }
}
