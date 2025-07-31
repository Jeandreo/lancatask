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

        // GENERATES DISPLAY WITH DATA
        return view('pages.agenda.edit')->with([
            'content' => $content,
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
        $data['date_start'] = $data['date_start'];
        $data['date_end'] = $data['date_end'];
        $data['hour_start'] = $data['date_start'];
        $data['hour_end'] = $data['date_end'];

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

        // Se foi criado com sucesso e quero enviar para o google calendar
        if($created && isset($data['send_google']) && $data['send_google']){

            $googleCalendarService = new GoogleCalendarService();

            $data = [
                'summary'   => $data['name'],
                'start'     => convertDateToISO($data['date_start']),
                'end'       => convertDateToISO($data['date_end']),
                'email'     => 'jeandreofur@gmail.com',
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        // VERIFY IF EXISTS
        if(!$content = $this->repository->find($id)){
            return response()->json(['Meeting not found'], 200);
        };

        // GET FORM DATA
        $data = $request->all();

        // STORING AND FORMATING NEW DATA
        $data['created_by'] = Auth::id();
        $data['date_start'] = $data['date_start'];
        $data['date_end']   = $data['date_end'];
        $data['hour_start'] = $data['date_start'];
        $data['hour_end']   = $data['date_end'];

        // Adiciona a reunião ao banco de dados
        $content->update($data);

        // Se foi criado com sucesso e quero enviar para o google calendar
        if($content->id_google){

            $googleCalendarService = new GoogleCalendarService();

            $data = [
                'summary'   => $data['name'],
                'start'     => convertDateToISO($data['date_start']),
                'end'       => convertDateToISO($data['date_end']),
            ];

            $payload = [
                'summary'     => $data['summary'],
                'start'       => ['dateTime' => $data['start'], 'timeZone' => 'America/Sao_Paulo'],
                'end'         => ['dateTime' => $data['end'],   'timeZone' => 'America/Sao_Paulo'],
            ];

            // Insere o evento no Google Calendar principal e dispara para todos
            $googleCalendarService->updateEvent($content->id_google, $payload);

        }

        // REDIRECT AND MESSAGES
        return redirect()
                ->back()
                ->with('message', 'Reunião <b>'. $request->name . '</b> foi atualizada com sucesso.');

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
