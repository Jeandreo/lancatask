<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\GoogleCalendar\Event;

class DeveloperController extends Controller
{
    public function test()
    {


        $event = new Event;
        $event->name = 'Reunião com Cliente X';
        $event->description = 'Alinhar escopo do projeto';
        $event->startDateTime = Carbon::parse('2025-07-25 10:00:00', 'America/Sao_Paulo');
        $event->endDateTime   = Carbon::parse('2025-07-25 11:00:00', 'America/Sao_Paulo');
        $event->addAttendee(['email' => 'jeandreofur@gmail.com']);
        $event->save();


        return 'Testando';
    }
}
