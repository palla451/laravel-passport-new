<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait TokenManagement
{
    /**
     * @param $email
     * @param $password
     * @return array
     */
    public function getTokenRefreshToken($email, $password): array
    {
        $baseUrl = url('/');
        $response = Http::post("{$baseUrl}/oauth/token", [
            'username' => $email,
            'password' => $password,
            'client_id' => config('passport.password_grant_client.id'),
            'client_secret' => config('passport.password_grant_client.secret'),
            'grant_type' => 'password',
            'scope' => '',
        ]);

        return  json_decode($response->getBody(), true);
    }

    /**
     * @param $refreshToken
     * @return mixed
     */
    public function getRefreshToken($refreshToken)
    {
        $baseUrl = url('/');
      $response =  Http::post("{$baseUrl}/oauth/token", [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => config('passport.password_grant_client.id'),
            'client_secret' => config('passport.password_grant_client.secret'),
            'scope' => '',
        ]);


        // Verifica la risposta dalla richiesta di aggiornamento del token
        if ($response->successful()) {
            return $response->json();
        } else {
            // Gestisci eventuali errori dalla richiesta di aggiornamento del token
            return response()->json(['error' => 'Impossibile aggiornare il token'], $response->status());
        }
    }
}
