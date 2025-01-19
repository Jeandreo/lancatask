<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
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
        $contents = $this->repository->where('status', true)->orderBy('name', 'ASC')->get();
        $agenda = $this->meetingsToEvents($contents);

        // RETURN VIEW WITH DATA
        return view('pages.agenda.index')->with([
            'contents' => $contents,
            'agenda' => $agenda,
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
        $this->repository->create($data);

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

        // GENERATES DISPLAY WITH DATA
        return view('pages.agenda.show')->with([
            'content' => $content,
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

        // REDIRECT AND MESSAGES
        return redirect()
                ->back()
                ->with('message', 'Reunião <b>'. $data['name'] . '</b> foi atualizada com sucesso.');

    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeCalendar(Request $request, $id)
    {

        // VERIFY IF EXISTS
        if(!$content = $this->repository->find($id))
        return redirect()->back();

        // GET ALL DATA
        $data = $request->all();

        // UPDATE BY
        $data['updated_by'] = Auth::id();

        $data['start']  = formateDate($data['date_start']) . ' ' . $data['hour_start'] . ' ';
        $data['end'] = formateDate($data['date_end']) . ' ' . $data['hour_end'] . ' ';

        // STORING NEW DATA
        $content->update($data);

        // REDIRECT AND MESSAGES
        return response('Changed With Success');

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
        $status = $content->status == true ? false : true;

        // STORING NEW DATA
        $this->repository->where('id', $id)->update(['status' => $status, 'updated_by' => Auth::id()]);

        // REDIRECT AND MESSAGES
        return redirect()->back()->with('message', 'Evento ' . ($status == false ? 'desativado' : 'habiliitado') . ' com sucesso.');

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
