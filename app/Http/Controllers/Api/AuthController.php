<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\TokenManagement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use TokenManagement;


    /**
     * @param Request $request
     * @return array
     */
    public function login(Request $request): array
    {
        $login = $request->input('email');
        $user = User::where('email', $login)->orWhere('username', $login)->first();

        if (!$user) {
            return response()->json(['error' => 'username o email invalid'],401);
        }

        if (Auth::attempt(['email' => $user->email, 'password' => $request->password]) ||
            Auth::attempt(['username' => $user->username, 'password' => $request->password])) {

            return $this->getTokenRefreshToken($user->email, $request->password);
        } else {
            return response()->json(['error' => 'invalid credentials'], 401);
        }



    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function refresh_token(Request $request): array
    {
        $refresh_token = $request->refresh_token;

        return $this->getRefreshToken($refresh_token);
    }
}
