<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GoogleController extends Controller
{
    public function getClient(): Google_Client
    {
        // dd(route('google.callback'));
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setScopes([
            Google_Service_Calendar::CALENDAR,
        ]);
        $client->setRedirectUri(route('google.callback'));
        return $client;
    }

    public function redirect()
    {
        $client = $this->getClient();
        return redirect()->away($client->createAuthUrl());
    }

    public function callback()
    {

        $client = $this->getClient();

        if (request()->has('code')) {

            $token = $client->fetchAccessTokenWithAuthCode(request('code'));

            // Salva o token
            Storage::disk('local')->put('google/calendar_token.json', json_encode($token));

            return 'Token salvo com sucesso!';
        }

        return 'Nenhum code recebido.';
    }

}
