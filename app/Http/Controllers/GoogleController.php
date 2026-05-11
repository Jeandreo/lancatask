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
        $client = new Google_Client();
        $client->setAuthConfig(config('google-calendar.auth_profiles.oauth.credentials_json'));
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
        $credentialsPath = config('google-calendar.auth_profiles.oauth.credentials_json');

        if (!file_exists($credentialsPath)) {
            return redirect()->back()->with([
                'google_credentials_missing' => true,
                'type' => 'warning',
                'title' => 'Google Agenda',
                'message' => 'Arquivo de credenciais não encontrado. Adicione o JSON em storage/app/google-calendar/oauth-credentials.json.',
            ]);
        }

        $client = $this->getClient();
        return redirect()->away($client->createAuthUrl());
    }

    public function callback()
    {

        $client = $this->getClient();

        if (request()->has('code')) {

            $token = $client->fetchAccessTokenWithAuthCode(request('code'));

            Storage::disk('local')->put('google-calendar/oauth-token.json', json_encode($token));

            return redirect()->route('dashboard.index')->with([
                'message' => 'Calendário conectado com sucesso!',
                'type' => 'success'
            ]);
        }

        return 'Nenhum code recebido.';
    }

}
