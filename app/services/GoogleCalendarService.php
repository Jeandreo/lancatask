<?php
namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Illuminate\Support\Facades\Storage;

class GoogleCalendarService
{

    // Instancia arquivos
    protected Google_Service_Calendar $service;
    protected string $tokenPath;
    protected string $credentialsPath;

    public function __construct()
    {
        // Define onde está o JSON de credenciais baixado do Google Cloud
        $this->credentialsPath = storage_path('app/google/credentials.json');

        // Define o caminho real do arquivo de token dentro do disco "local" do Laravel
        $this->tokenPath = Storage::disk('local')->path('google/calendar_token.json');

        // Cria o cliente oficial do Google
        $client = new Google_Client();

        // Informa o arquivo de credenciais para autenticar a aplicação
        $client->setAuthConfig($this->credentialsPath);

        // Define o escopo de acesso (calendário completo)
        $client->setScopes([Google_Service_Calendar::CALENDAR]);

        // Pede acesso offline para receber refresh_token e poder renovar sem intervenção do usuário
        $client->setAccessType('offline');

        // Se já existe um token salvo, carrega no client para reutilizar
        if (file_exists($this->tokenPath)) {
            $client->setAccessToken(json_decode(file_get_contents($this->tokenPath), true));
        }

        // Se o access_token expirou, tenta renovar com o refresh_token e salva o novo token
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($this->tokenPath, json_encode($client->getAccessToken()));
        }

        // Cria o serviço do Calendar usando o client autenticado
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