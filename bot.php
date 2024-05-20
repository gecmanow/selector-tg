<?php

setlocale(LC_ALL, 'ru_RU.utf8');
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
mb_http_output('UTF-8');
mb_language('uni');
header('Content-type: text/html; charset=utf-8');

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$host = $_ENV['DB_HOST'];
$db_name = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];
$token = $_ENV['TOKEN'];

$db = new PDO('mysql:dbname=' . $db_name . ';host=' . $host . ';charset=UTF8', $user, $password);

$api = file_get_contents('php://input');

$output = json_decode($api, true);
$chat_id = $output['message']['chat']['id'];
$message = $output['message']['text'];
$callback_query = $output['callback_query'];
$data = $callback_query['data'];
$message_id = $callback_query['message']['message_id'];
$chat_id_in = $callback_query['message']['chat']['id'];
$first_name = $output['message']['from']['first_name'];
$first_name_in = $callback_query['message']['chat']['first_name'];
//$reader = IOFactory::createReader('Xlsx');
//$spreadsheet = $reader->load('table.xlsx');
// Только чтение данных
//$reader->setReadDataOnly(true);

//$sheetsCount = $spreadsheet->getSheetCount();
//$table = $spreadsheet->getActiveSheet()->toArray();

file_put_contents(__DIR__ . '/message.txt', print_r($output, true));

$keyboardAction = array(
    'reply_markup' => json_encode(array(
        'inline_keyboard' => array(
            array(
                array(
                    'text' => 'Зайти',
                    'callback_data' => 'enter',
                )
            ),
            array(
                array(
                    'text' => 'Перезвонить',
                    'callback_data' => 'call',
                )
            ),
            array(
                array(
                    'text' => 'Назначить Zoom',
                    'callback_data' => 'zoom',
                )
            )
        ),
        'one_time_keyboard' => TRUE,
        'resize_keyboard' => TRUE,
    ))
);

$keyboardDepartment = array(
    'reply_markup' => json_encode(array(
        'inline_keyboard' => array(
            array(
                array(
                    'text' => 'Корневой',
                    'callback_data' => 'root',
                )
            ),
            array(
                array(
                    'text' => 'Прямые продажи',
                    'callback_data' => 'direct_sales',
                )
            ),
            array(
                array(
                    'text' => 'Проектные продажи',
                    'callback_data' => 'project_sales',
                )
            ),
            array(
                array(
                    'text' => 'Снабжение',
                    'callback_data' => 'supply',
                )
            ),
            array(
                array(
                    'text' => 'ВЭД',
                    'callback_data' => 'ved',
                )
            ),
            array(
                array(
                    'text' => 'HR',
                    'callback_data' => 'hr',
                )
            ),
            array(
                array(
                    'text' => 'IT + маркетинг',
                    'callback_data' => 'it',
                )
            ),
            array(
                array(
                    'text' => 'Бухгалтерия',
                    'callback_data' => 'comptabilitat',
                )
            ),
            array(
                array(
                    'text' => 'Сервис',
                    'callback_data' => 'service',
                )
            ),
            array(
                array(
                    'text' => 'Назад',
                    'callback_data' => 'back',
                )
            )
        ),
        'one_time_keyboard' => TRUE,
        'resize_keyboard' => TRUE,
    ))
);

//if($data['message']['from']['id'] == 261803700) {

switch ($message) {
    case '/start':
        $date = date('Y-m-d H:i:s');
        $query = $db->prepare("INSERT INTO actions (name, chat_id, action, created_at) VALUES ('$first_name', '$chat_id', 'start', '$date')");
        $query->execute();

        $response = $keyboardAction;
        $response['chat_id'] = $chat_id;
        $response['text'] = 'Здравствуйте ' . $first_name . ', что Вы хотите сделать?';

        sendMessage($token, $response);

        break;

    case 'Назад':
        $response = $keyboardAction;
        $response['chat_id'] = $chat_id;
        $response['text'] = 'Здравствуйте ' . $first_name . ', что Вы хотите сделать?';

        sendMessage($token, $response);

        break;

    default:
        $response = array(
            'chat_id' => $chat_id,
            'text' => 'Не понимаю о чём вы...'
        );

        sendMessage($token, $response);
}
//$action = 'action';
/*$search_worker = strpos($data, '@');
if($search_worker !== false) {
    $worker = explode('@', $data)[1];
    $data = explode('@', $data)[0];
}*/

switch ($data){
    case 'enter':
        $date = date('Y-m-d H:i:s');
        $query = $db->prepare("INSERT INTO actions (name, chat_id, action, created_at) VALUES ('$first_name_in', '$chat_id_in', 'enter', '$date')");
        $query->execute();

        $response = $keyboardDepartment;
        $response['chat_id'] = $chat_id_in;
        $response['text'] = 'Выберите отдел:';

        sendMessage($token, $response);

        break;

    case 'call':
        $date = date('Y-m-d H:i:s');
        $query = $db->prepare("INSERT INTO actions (name, chat_id, action, created_at) VALUES ('$first_name_in', '$chat_id_in', 'call', '$date'");
        $query->execute();

        $response = $keyboardDepartment;
        $response['chat_id'] = $chat_id_in;
        $response['text'] = 'Выберите отдел:';

        sendMessage($token, $response);

        break;

    case 'zoom':
        $date = date('Y-m-d H:i:s');
        $query = $db->prepare("INSERT INTO actions (name, chat_id, action, created_at) VALUES ('$first_name_in', '$chat_id_in', 'zoom', '$date'");
        $query->execute();

        $response = $keyboardDepartment;
        $response['chat_id'] = $chat_id_in;
        $response['text'] = 'Выберите отдел:';

        sendMessage($token, $response);

        break;

    case 'direct_sales':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'Прямые продажи' OR `departament` = 'Прямые продажи/Проектные продажи'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case 'project_sales':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'Проектные продажи' OR `departament` = 'Прямые продажи/Проектные продажи'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case 'supply':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'Снабжение'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case 'ved':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'ВЭД'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case 'hr':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'HR'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case 'it':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'IT + маркетинг'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case 'service':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'Сервис'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case 'root':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'Корневой'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case 'comptabilitat':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'Бухгалтерия'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case 'worker':

        $query = $db->prepare("SELECT `name`, `action` FROM `actions` WHERE `chat_id` = '$chat_id_in' ORDER BY `created_at` DESC LIMIT 1");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        if(count($db_response) > 0) {
            foreach($db_response as $i => $dbr) {
                $worker['name'] = $dbr['name'];
                $worker['action'] = $dbr['action'];
            }

            if($worker['action'] === 'enter') {
                $action_response = 'Зайдите ко мне.';
            } elseif ($worker['action'] === 'call') {
                $action_response = 'Позвоните мне.';
            } elseif ($worker['action'] === 'zoom') {
                $action_response = 'Назначьте встречу в Zoom.';
            } else {
                $action_response = 'не могу найти требуемое действие...';
            }

            $response['chat_id'] = $worker;
            $response['text'] = 'Здравствуйте ' . $worker['name'] . '! ' . $action_response;
            sendMessage($token, $response);
        }

        break;

    case 'back':
        $response = $keyboardAction;
        $response['chat_id'] = $chat_id_in;
        $response['text'] = 'Здравствуйте ' . $first_name . ', что Вы хотите сделать?';

        sendMessage($token, $response);

        break;

    default:
        $response = array(
            'chat_id' => $chat_id_in,
            'text' => 'Не понимаю о чём вы...'
        );

        sendMessage($token, $response);
}

/*} else {
    $response = array(
        'chat_id' => $data['message']['chat']['id'],
        'text' => 'Ты не мой хозяин!'
    );

    sendMessage($token, $response);
}*/

function deleteMessage() {
    //
}

function sendMessage($token, $response) {
    $ch = curl_init('https://api.telegram.org/bot' . $token . '/sendMessage');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_exec($ch);
    curl_close($ch);
}

function getWorkers($db_response, $chat_id_in) {
    if(count($db_response) > 0) {
        $keyboard = array(
            'reply_markup' => array(
                'inline_keyboard' => array(),
                'one_time_keyboard' => TRUE,
                'resize_keyboard' => TRUE,
            )
        );

        foreach($db_response as $i => $dbr) {
            $keyboard['reply_markup']['inline_keyboard'][$i][0]['text'] = $dbr['name'];
            $keyboard['reply_markup']['inline_keyboard'][$i][0]['callback_data'] = $dbr['telegram_id'];
        }

        $keyboard2 = json_encode($keyboard['reply_markup']);
        $keyboard3 = [];
        $keyboard3['reply_markup'] = $keyboard2;

        $response = $keyboard3;
        $response['chat_id'] = $chat_id_in;
        $response['text'] = 'Выберите сотрудника:';
        return $response;
    } else {
        return array(
            'chat_id' => $chat_id_in,
            'text' => 'Сотрудников нет...'
        );
    }
}
