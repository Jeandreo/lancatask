<?php

// GET PROJECTS
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

function randomEmoji()
{
    $emojis = ["😊", "😄", "😃", "😁", "😆", "😍", "😋", "😎", "😸", "🌟", "🎉", "🥳", "🎈", "🌈", "💖"];
    $random_index = array_rand($emojis);
    return $emojis[$random_index];
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
