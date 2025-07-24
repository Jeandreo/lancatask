<?php

// GET PROJECTS
use App\Models\Project;
use Carbon\Carbon;
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

function convertDateToISO($date){
    return Carbon::createFromFormat('Y-m-d H:i', $date, 'America/Sao_Paulo')->format('Y-m-d\TH:i:sP');
}

function formatLinks($text)
{
    // Regex para detectar URLs que não estão dentro de tags HTML
    $pattern = '/(?<!<a href="|<img src=")(https?:\/\/[^\s<]+)/i';

    // Substituir URLs por links clicáveis
    $formattedText = preg_replace_callback($pattern, function ($matches) {
        $url = $matches[0];
        return '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">' . $url . '</a>';
    }, $text);

    return $formattedText;
}


// PUT THE BACKGROUND IN THE TEXT COLOR
function hex2rgb($colour, $opacity)
{

    // REMOVE # FROM STRING
    $colour = ltrim($colour, '#');

    // EXTRACT RGB FROM HEX
    $rgb = sscanf($colour, '%2x%2x%2x');
    $rgb[] = $opacity;

    // RETURN RGBA
    return sprintf('rgb(%d, %d, %d, %d%%)', ...$rgb);
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

// CONVERT DATA PT TO US
if (! function_exists('convertDateFormat')) {
    function convertDateFormat($date){
        $dateObj = DateTime::createFromFormat('d/m/Y', $date);
        return $dateObj->format('Y-m-d');
    }
}
