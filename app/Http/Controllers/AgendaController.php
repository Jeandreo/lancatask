<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AgendaMember;
use App\Models\User;
use App\Models\Client;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    protected $request;
    private $repository;

    public function __construct(Request $request, Agenda $content)
    {

        $this->request = $request;
        $this->repository = $content;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {

        // GET ALL DATA
        $contents = $this->repository->orderBy('id', 'ASC')->get();

        // RETURN VIEW WITH DATA
        return view('pages.agenda.list')->with([
            'contents' => $contents,
        ]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // GET ALL DATA
        $contents   = $this->repository->where('status', true)->orderBy('name', 'ASC')->get();
        $agenda     = $this->meetingsToEvents($contents);
        $users      = User::where('status', true)->orderBy('name', 'ASC')->get();
        $clients    = Client::where('status', true)->orderBy('name', 'ASC')->get();
        
        // RETURN VIEW WITH DATA
        return view('pages.agenda.index')->with([
            'contents'  => $contents,
            'agenda'    => $agenda,
            'users'     => $users,
            'clients'   => $clients,
            'filterFor' => null,
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        // VERIFY IF EXISTS
        if(!$content = $this->repository->find($id)){
            return response()->json(['Meeting not found'], 200);
        };

        $users      = User::where('status', true)->orderBy('name', 'ASC')->get();
        $clients    = Client::where('status', true)->orderBy('name', 'ASC')->get();

        // GENERATES DISPLAY WITH DATA
        return view('pages.agenda.edit')->with([
            'content' => $content,
            'users'   => $users,
            'clients' => $clients,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // GET FORM DATA
        $data = $request->all();

        // CREATED BY
        $data['created_by'] = Auth::id();
        $data['date_start'] = $data['date_end'] = $data['date'];

        // Verifica se a hora de encerramento é maior que a hora de inicio
        if($data['hour_end'] < $data['hour_start']){
            return redirect()->back()->with('message', 'A hora de encerramento deve ser maior que a hora de inicio.');
        }

        // Extrai os emails extra
        $data['extra_emails'] = $data['emails_additional'];

        // SEND DATA
        $created = $this->repository->create($data);

        // Adiciona usuários
        if(isset($data['users']) && count($data['users'])){

            foreach($data['users'] as $user){
                AgendaMember::create([
                    'type'      => 'user',
                    'member_id' => $user,
                    'agenda_id' => $created->id,
                ]);
            }

        }

        // Adiciona clientes
        if(isset($data['clients']) && count($data['clients'])){

            foreach($data['clients'] as $client){
                AgendaMember::create([
                    'type'      => 'client',
                    'member_id' => $client,
                    'agenda_id' => $created->id,
                ]);
            }

        }

        // Obtém membros
        $members = AgendaMember::where('agenda_id', $created->id)->get();

        // Gera lista de emails
        $emails = [];
        foreach($members as $member){

            // Obtém email do usuário ou do cliente
            if($member->type == 'user'){
                $emails[] = User::find($member->member_id)->email;
            }else{
                $emails[] = Client::find($member->member_id)->email;
            }

        }

        // Se tiver emails adicionais   
        if(isset($data['emails_additional'])){

            // Decodifica os emails
            $data['emails_additional'] = json_decode($data['emails_additional'], true);

            // Extrai os valores
            $addicionalEmails = [];

            foreach($data['emails_additional'] as $email){
                $addicionalEmails[] = $email['value'];
            }

            // Adiciona os emails
            $emails = array_merge($emails, $addicionalEmails);

        }

        // Se foi criado com sucesso e quero enviar para o google calendar
        if($created && isset($data['send_google']) && $data['send_google']){

            $googleCalendarService = new GoogleCalendarService();

            $data = [
                'summary'   => $data['name'],
                'start'     => convertDateToISO($data['date_start'] . ' ' . $data['hour_start']),
                'end'       => convertDateToISO($data['date_end'] . ' ' . $data['hour_end']),
                'email'     => 'contato.growity@gmail.com',
            ];

            $payload = [
                'summary'     => $data['summary'],
                'start'       => ['dateTime' => $data['start'], 'timeZone' => 'America/Sao_Paulo'],
                'end'         => ['dateTime' => $data['end'],   'timeZone' => 'America/Sao_Paulo'],
                'attendees' => array_map(fn($email) => ['email' => $email], $emails),
                'reminders'   => [
                    'useDefault' => false,
                    'overrides'  => [
                        ['method' => 'email', 'minutes' => 60 * 24],
                        ['method' => 'email', 'minutes' => 30],
                    ],
                ],
            ];

            // Insere o evento no Google Calendar principal e dispara para todos
            $event = $googleCalendarService->insertEvent($payload, 'primary', 'all');

            // Se houve erro
            if($event['error']){
                return redirect()->back()->with('message', 'Não foi possível inserir o evento no Google Calendar por conta do Token expirado.');
            }

            // Salva o id do google calendar
            $created->id_google = $event->id;
            $created->save();

        }

        // REDIRECT AND MESSAGES
        return redirect()
                ->back()
                ->with('message', 'Evento adicionado com sucesso.');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // VERIFY IF EXISTS
        if(!$content = $this->repository->find($id)){
            return response()->json(['Evento não '], 200);
        };

        // Inicializa array de respostas
        $googleAttendees = [];

        // Se for um evento que esta no Google
        if($content->id_google){

            $googleCalendarService = new GoogleCalendarService();
            $attendees = $googleCalendarService->attendees($content->id_google);

            foreach($attendees as $attendee){
                $googleAttendees[$attendee->email] = $attendee->responseStatus;
            }

        }

        // GENERATES DISPLAY WITH DATA
        return view('pages.agenda.show')->with([
            'content' => $content,
            'googleAttendees' => $googleAttendees,
        ]);
    }

    public function update(Request $request, $id)
    {
        // VERIFY IF EXISTS
        if(!$content = $this->repository->find($id)){
            return response()->json(['Meeting not found'], 200);
        }

        // GET FORM DATA
        $data = $request->all();

        // FORMATAÇÃO
        $data['created_by']    = Auth::id();
        $data['date_start'] = $data['date_end'] = $data['date'];
        $data['extra_emails']  = $data['emails_additional'] ?? null;

        // Atualiza informações no banco
        $content->update($data);

        // --- GOOGLE CALENDAR ---
        if ($content->id_google) {

            $googleCalendarService = new GoogleCalendarService();

            // 1️⃣ Exclui o evento antigo
            try {
                $googleCalendarService->deleteEvent($content->id_google);
            } catch (\Exception $e) {
                \Log::error("Erro ao excluir evento antigo do Google Calendar: " . $e->getMessage());
            }

            // 2️⃣ Recria o evento atualizado

            // 🔹 Coleta todos os membros
            $members = AgendaMember::where('agenda_id', $content->id)->get();

            $emails = [];
            foreach ($members as $member) {
                if ($member->type == 'user') {
                    $user = User::find($member->member_id);
                    if ($user && $user->email) $emails[] = $user->email;
                } else {
                    $client = Client::find($member->member_id);
                    if ($client && $client->email) $emails[] = $client->email;
                }
            }

            // 🔹 Emails adicionais
            if (!empty($data['emails_additional'])) {
                $decoded = json_decode($data['emails_additional'], true);
                foreach ($decoded as $item) {
                    $emails[] = $item['value'] ?? $item;
                }
            }

            // 🔹 Remove duplicados
            $emails = array_unique($emails);

            // 🔹 Monta o payload para o novo evento
            $eventData = [
                'summary'   => $data['name'],
                'start'     => convertDateToISO($data['date_start'] . ' ' . $data['hour_start']),
                'end'       => convertDateToISO($data['date_end']   . ' ' . $data['hour_end']),
            ];

            $payload = [
                'summary'   => $eventData['summary'],
                'start'     => ['dateTime' => $eventData['start'], 'timeZone' => 'America/Sao_Paulo'],
                'end'       => ['dateTime' => $eventData['end'],   'timeZone' => 'America/Sao_Paulo'],
                'attendees' => array_map(fn($email) => ['email' => $email], $emails),
                'reminders' => [
                    'useDefault' => false,
                    'overrides'  => [
                        ['method' => 'email', 'minutes' => 60 * 24],
                        ['method' => 'email', 'minutes' => 30],
                    ],
                ],
            ];

            // 3️⃣ Insere o novo evento
            $event = $googleCalendarService->insertEvent($payload, 'primary', 'all');

            if (isset($event['error'])) {
                return redirect()->back()->with('message', 'Erro ao atualizar evento no Google Calendar (token expirado).');
            }

            // 4️⃣ Atualiza o ID do novo evento
            $content->id_google = $event->id;
            $content->save();
        }

        return redirect()
            ->back()
            ->with('message', 'Reunião <b>' . $request->name . '</b> foi atualizada com sucesso.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        // GET DATA
        $content = $this->repository->find($id);
        $content->status = false;
        $content->save();

         // Se foi criado com sucesso e quero enviar para o google calendar
         if($content->id_google){

            $googleCalendarService = new GoogleCalendarService();

            // Insere o evento no Google Calendar principal e dispara para todos
            $googleCalendarService->deleteEvent($content->id_google);

        }

        // REDIRECT AND MESSAGES
        return redirect()->back()->with('message', 'Evento cancelado com sucesso.');

    }

        /**
     * Gera um array com eventos para calendário.
     *
     * @param  array  $tasks
     * @return \Illuminate\Http\Response
     */
    function meetingsToEvents($meetings){

        $calendar = [];

        // FAZ LOOPING DE TAREFAS COM DATA E SALVA NO ARRAY
        foreach($meetings as $meeting){

            // Limpa o evento
            $event = [];

            $event['title'] = $meeting->name;
            $event['for']   = $meeting->for;
            $event['color'] = $meeting->color;
            $event['classNames'] = ['cursor-pointer'];

            $event['editable'] = false;
            $event['extendedProps'] = [
                'id'   => $meeting->id,
                'type' => 'meetings',
                'htmlTitle' => '<span>' . $meeting->name . '</span>',
            ];

            // Se não for um evento recorrente
            if($meeting->recurrent == false){

                // Caso não possua data, define ela como hoje
                $dateStart = $meeting->date_start == null ? date('Y-m-d') : $meeting->date_start . ($meeting->hour_start ? ' ' . $meeting->hour_start : '');

                // Se não tiver data Final, coloca para o mesmo dia
                $dateEnd = $meeting->date_end == null ? $meeting->date_start : $meeting->date_end . ' ' . $meeting->hour_end;

                $event['start'] = $dateStart;
                $event['end']   = $dateEnd;

            } else {

                // Define as regras da recorrencia
                $event['rrule'] = [
                    'dtstart' => $meeting->date_start . 'T' . $meeting->hour_start,
                    'freq' => 'weekly',
                    // 'interval' => 2,
                    'byweekday' => explode(',', $meeting->week_days),
                    // 'until' => '2012-06-01',
                ];
                $event['duration'] = $meeting->duration;

            }

            $calendar[] = $event;

        }

        // Retorna calendário
        return $calendar;

    }
}
