<?php

// GET PROJECTS

use App\Models\Catalog;
use App\Models\ChallengeCompleted;
use App\Models\ChallengeMonthly;
use App\Models\ChallenngeMonthly;
use App\Models\Project;
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

    // Consulta os projetos em que o usuário está associado ou é o gerente
    $projects = Project::where('status', 1)
        ->where(function ($query) use ($userId) {
            $query->whereHas('users', function ($subquery) use ($userId) {
                $subquery->where('user_id', $userId);
            })
                ->orWhere('manager_id', $userId)
                ->orWhere('created_by', $userId);
        });

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

function catalogs()
{
    // Obtém o ID do usuário autenticado
    $userId = Auth::id();

    // Consulta os projetos em que o usuário está associado ou é o gerente
    $catalogs = Catalog::where('status', 1)->where('created_by', $userId);

    return $catalogs;
}

function resizeAndSaveImage($base64Image, $sizes, $name, $path)
{

    // Directory to save images
    $uploadDir = public_path('storage/' . $path);

    // Create the directory if it doesn't exist
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

    // IMAGEM ORIGINAL
    $manager = new ImageManager(new Driver());

    foreach ($sizes as $value) {

        // Obtém imagem
        $image = $manager->read($base64Image);

        // Nome do arquivo
        $nameFile = $name . '-' . $value . 'px.jpg';
        $image = $image->scale(width: $value);
        $image = $image->encode(new AutoEncoder(quality: 90));


        // SALVE A IMAGEM REDIMENSIONADA
        $image->save('storage/' . $path . $nameFile, 95);
    }
}

// MONEY BRL TO DECIMAL
function toDecimal($value)
{

    // Limpa caracteres especiais
    $value = str_replace("\xC2\xA0", ' ', $value);

    // REMOVE R$ AND REPLACE POINTS
    $value = str_replace(array('R$', '.'), '', $value);
    $value = str_replace(',', '.', $value);

    // CONVERT TO FLOAT
    $value = floatval($value);

    // RETURN
    return $value;
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

// VERIFY IF DAY CHECKED
function checkDayMonth($date, $type)
{

    // VERIFY IF COMPLETED IN THE DAY
    $exists = ChallengeCompleted::where('type', $type)->where('date', $date)->first();

    // RETURN
    return $exists;
}


function formateDate($dataBrasileiro)
{
    // Divide a string da data em dia, mês e ano
    $partes = explode('/', $dataBrasileiro);

    // Verifica se as partes estão presentes e são válidas
    if (count($partes) === 3 && checkdate($partes[1], $partes[0], $partes[2])) {
        // Reorganiza as partes no formato americano (ano-mês-dia)
        $dataAmericano = $partes[2] . '-' . $partes[1] . '-' . $partes[0];

        return $dataAmericano;
    } else {
        // Caso a data brasileira seja inválida
        return null;
    }
}
