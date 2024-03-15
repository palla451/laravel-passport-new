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

            $response = $this->getTokenRefreshToken($user->email, $request->password);
            $response['user'] = $user;

            return $response;
        } else {
            return response()->json(['error' => 'invalid credentials'], 401);
        }
    }


    /**
     * @return JsonResponse
     */
    public function logged(): JsonResponse
    {
        return response()->json(Auth::user());
    }

    /**
     * @return JsonResponse
     */
    public function users():JsonResponse
    {
        $users = User::all();

        return response()->json(['users'=>$users]);
    }

    public function logout(Request $request)
    {
        $accessToken = Auth::user()->token();

        $accessToken->revoke();

        return response()->json(['message' => 'Logout eseguito con successo']);
    }


    /**
     * @param Request $request
     * @return array|mixed
     */
    public function refresh_token(Request $request)
    {
        $refresh_token = $request->refresh_token;
        return  $this->getRefreshToken($refresh_token);
    }




}
