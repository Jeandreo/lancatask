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
        $client->setAuthConfig(storage_path(env('GOOGLE_CREDENTIAL_APP')));
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
        $credentials = env('GOOGLE_CREDENTIAL_APP');
        $credentialsPath = $credentials ? storage_path($credentials) : null;

        if (!$credentialsPath || !file_exists($credentialsPath)) {
            return redirect()->back()->with([
                'google_credentials_missing' => true,
                'type' => 'warning',
                'title' => 'Google Agenda',
                'message' => 'Arquivo de credenciais não encontrado. Configure o GOOGLE_CREDENTIAL_APP no .env e adicione o JSON em storage.',
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

            // Salva o token
            Storage::disk('local')->put('google/calendar_token.json', json_encode($token));

            return redirect()->route('dashboard.index')->with([
                'message' => 'Calendário conectado com sucesso!',
                'type' => 'success'
            ]);
        }

        return 'Nenhum code recebido.';
    }

}
