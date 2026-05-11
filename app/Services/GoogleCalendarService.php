<?php
namespace App\Services;

use Google_Client;
use Google_Service_Calendar;

class GoogleCalendarService
{

    // Instancia arquivos
    protected Google_Service_Calendar $service;
    protected string $tokenPath;
    protected string $credentialsPath;

    public function __construct()
    {
        $this->credentialsPath = config('google-calendar.auth_profiles.oauth.credentials_json');
        $this->tokenPath = config('google-calendar.auth_profiles.oauth.token_json');

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

    /**
     * Insere um evento no calendário.
     *
     * @param array  $payload      Dados do evento no formato aceito pela API
     * @param string $calendarId   ID do calendário (default: 'primary')
     * @param string $sendUpdates  all|externalOnly|none -> controla envio de e-mails
     */
    public function insertEvent(array $payload, string $calendarId = 'primary', string $sendUpdates = 'all')
    {

        // Converte o payload em um objeto de evento do Google
        $event = new \Google_Service_Calendar_Event($payload);

        // Verifica se o token ainda é válido
        if ($this->service->getClient()->isAccessTokenExpired()) {
            return ['error' => 'Token expirado'];
        }

        // Insere o evento e, se definido, envia convites/atualizações por e-mail
        return $this->service->events->insert($calendarId, $event, ['sendUpdates' => $sendUpdates]);

    }

    public function updateEvent(string $eventId, array $payload, string $calendarId = 'primary', string $sendUpdates = 'all')
    {
        $event = $this->service->events->get($calendarId, $eventId);
        
        foreach ($payload as $key => $value) {
            $event->$key = $value;
        }

        return $this->service->events->update($calendarId, $eventId, $event, ['sendUpdates' => $sendUpdates]);
    }

    public function deleteEvent(string $eventId, string $calendarId = 'primary')
    {
        return $this->service->events->delete($calendarId, $eventId);
    }

    public function attendees(string $eventId, string $calendarId = 'primary')
    {
        $event = $this->service->events->get($calendarId, $eventId);
        return $event->getAttendees();
    }
    
    
    /**
     * Lista os próximos eventos a partir de agora.
     *
     * @param int    $max         Quantidade máxima de eventos a retornar
     * @param string $calendarId  ID do calendário (default: 'primary')
     */
    public function listUpcoming(int $max = 10, string $calendarId = 'primary')
    {
        
        // Chama a API para listar eventos futuros, ordenados por horário de início
        return $this->service->events->listEvents($calendarId, [
            'maxResults'   => $max,                         // limite de resultados
            'orderBy'      => 'startTime',                 // ordena por data de início
            'singleEvents' => true,                        // expande recorrentes em instâncias únicas
            'timeMin'      => now()->toRfc3339String(),    // filtra a partir do momento atual
        ]);
        
    }
}
