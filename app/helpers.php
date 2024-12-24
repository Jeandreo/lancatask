<?php

// GET PROJECTS

use App\Models\Catalog;
use App\Models\ChallengeCompleted;
use App\Models\ChallengeMonthly;
use App\Models\ChallenngeMonthly;
use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;

function randomEmoji()
{
    $emojis = ["😊", "😄", "😃", "😁", "😆", "😍", "😋", "😎", "😸", "🌟", "🎉", "🥳", "🎈", "🌈", "💖"];
    $random_index = array_rand($emojis);
    return $emojis[$random_index];
}

function projects()
{
    // Obtém o ID do usuário autenticado
    $userId = Auth::id();

    // // // Consulta os projetos em que o usuário está associado ou é o gerente
    // // $projects = Project::where('status', 1)
    // //     ->where(function ($query) use ($userId) {
    // //         $query->whereHas('users', function ($subquery) use ($userId) {
    // //             $subquery->where('user_id', $userId);
    // //         })
    // //             ->orWhere('manager_id', $userId)
    // //             ->orWhere('created_by', $userId);
    // //     });

    $projects = Project::all();

    return $projects;
}

function randomColor()
{
    $letters = '0123456789ABCDEF';
    $color = '#';
    for ($i = 0; $i < 6; $i++) {
        $color .= $letters[rand(0, 15)];
    }
    return $color;
}


function findImage($pathAndFile, $default = 'user')
{

    if (Storage::disk('public')->exists($pathAndFile)) {
        $url = asset('storage/' . $pathAndFile);
    } else {
        if ($default == 'landscape') {
            $url = asset('/assets/media/images/default.png');
        } elseif ($default == 'image') {
            $url = asset('/assets/media/images/blank_file.png');
        } elseif ($default == 'beautiful') {
            $url = asset('/assets/media/images/img-beautiful.jpg');
        } else {
            $url = asset('/assets/media/avatars/blank.png');
        }
    }

    return $url;
}
