<?php
namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Illuminate\Support\Facades\Storage;

class CalendarService
{
    protected Google_Service_Calendar $service;
    protected string $tokenPath;
    protected string $credentialsPath;

    public function __construct()
    {
        $this->credentialsPath = storage_path('app/google/credentials.json');
        $this->tokenPath       = Storage::disk('local')->path('google/calendar_token.json');

        $client = new Google_Client();
        $client->setAuthConfig($this->credentialsPath);
        $client->setScopes([Google_Service_Calendar::CALENDAR]);
        $client->setAccessType('offline');

        if (file_exists($this->tokenPath)) {
            $client->setAccessToken(json_decode(file_get_contents($this->tokenPath), true));
        }

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($this->tokenPath, json_encode($client->getAccessToken()));
        }

        $this->service = new Google_Service_Calendar($client);
    }

    public function insertEvent(array $payload, string $calendarId = 'primary', string $sendUpdates = 'all')
    {
        $event = new \Google_Service_Calendar_Event($payload);
        return $this->service->events->insert($calendarId, $event, ['sendUpdates' => $sendUpdates]);
    }

    public function listUpcoming(int $max = 10, string $calendarId = 'primary')
    {
        return $this->service->events->listEvents($calendarId, [
            'maxResults'   => $max,
            'orderBy'      => 'startTime',
            'singleEvents' => true,
            'timeMin'      => now()->toRfc3339String(),
        ]);
    }
}
