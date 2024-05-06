<?php

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

$db_conn = mysqli_connect($host, $user, $password, $db_name);

$api = file_get_contents('php://input');

$output = json_decode($api, true);
$chat_id = $output['message']['chat']['id'];
$message = $output['message']['text'];
$callback_query = $output['callback_query'];
$data = $callback_query['data'];
$message_id = $callback_query['message']['message_id'];
$chat_id_in = $callback_query['message']['chat']['id'];
$first_name = $output['message']['from']['first_name'];

$reader = IOFactory::createReader('Xlsx');
$spreadsheet = $reader->load('table.xlsx');
// Только чтение данных
$reader->setReadDataOnly(true);

$sheetsCount = $spreadsheet->getSheetCount();
$table = $spreadsheet->getActiveSheet()->toArray();

file_put_contents(__DIR__ . '/message.txt', print_r($output, true));

$keyboardAction = array(
    'reply_markup' => json_encode(array(
        'inline_keyboard' => array(
            array(
                array(
                    'text' => 'Зайти',
                    'callback_data' => '/department',
                )
            ),
            array(
                array(
                    'text' => 'Перезвонить',
                    'callback_data' => '/department',
                )
            ),
            array(
                array(
                    'text' => 'Назначить Zoom',
                    'callback_data' => '/department',
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
                    'text' => 'Прямые продажи',
                    'callback_data' => '/direct_sales',
                )
            ),
            array(
                array(
                    'text' => 'Проектные продажи',
                    'callback_data' => '/project_sales',
                )
            ),
            array(
                array(
                    'text' => 'Снабжение',
                    'callback_data' => '/supply',
                )
            ),
            array(
                array(
                    'text' => 'ВЭД',
                    'callback_data' => '/ved',
                )
            ),
            array(
                array(
                    'text' => 'HR',
                    'callback_data' => '/hr',
                )
            ),
            array(
                array(
                    'text' => 'ИТ и маркетинг',
                    'callback_data' => '/it_and_marketing',
                )
            ),
            array(
                array(
                    'text' => 'Сервис',
                    'callback_data' => '/service',
                )
            ),
            array(
                array(
                    'text' => 'Назад',
                    'callback_data' => '/back',
                )
            )
        ),
        'one_time_keyboard' => TRUE,
        'resize_keyboard' => TRUE,
    ))
);

$keyboardStaffDirectSales = array(
    'reply_markup' => json_encode(array(
        'inline_keyboard' => array(
            array(
                array(
                    'text' => 'Сотрудник 1',
                    'callback_data' => '/keyboardStaffDirectSales',
                )
            ),
            array(
                array(
                    'text' => 'Сотрудник 2',
                    'callback_data' => '/keyboardStaffDirectSales',
                )
            ),
            array(
                array(
                    'text' => 'Сотрудник 3',
                    'callback_data' => '/keyboardStaffDirectSales',
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
        $response = $keyboardAction;
        $response['chat_id'] = $chat_id;
        $response['text'] = 'Здравствуйте ' . $first_name . ', что Вы хотите сделать?';

        sendMessage($token, $response);

        break;

    case 'Зайти':
        $response = $keyboardDepartment;
        $response['chat_id'] = $chat_id;
        $response['text'] = 'Выберите отдел:';

        sendMessage($token, $response);

        break;

    case 'Перезвонить':
        $response = $keyboardDepartment;
        $response['chat_id'] = $chat_id;
        $response['text'] = 'Выберите отдел:';

        sendMessage($token, $response);

        break;

    case 'Назначить Zoom':
        $response = $keyboardDepartment;
        $response['chat_id'] = $chat_id;
        $response['text'] = 'Выберите отдел:';

        sendMessage($token, $response);

        break;

    case 'Прямые продажи':
        $response = $keyboardStaffDirectSales;
        $response['chat_id'] = $chat_id;
        $response['text'] = 'Выберите сотрудника:';

        sendMessage($token, $response);

        break;

    case 'Проектные продажи':
        $response = $keyboardStaffDirectSales;
        $response['chat_id'] = $chat_id;
        $response['text'] = 'Выберите сотрудника:';

        sendMessage($token, $response);

        break;

    case 'Снабжение':
        $response = $keyboardStaffDirectSales;
        $response['chat_id'] = $chat_id;
        $response['text'] = 'Выберите сотрудника:';

        sendMessage($token, $response);

        break;

    case 'ВЭД':
        $response = $keyboardStaffDirectSales;
        $response['chat_id'] = $chat_id;
        $response['text'] = 'Выберите сотрудника:';

        sendMessage($token, $response);

        break;

    case 'HR':
        $response = $keyboardStaffDirectSales;
        $response['chat_id'] = $chat_id;
        $response['text'] = 'Выберите сотрудника:';

        sendMessage($token, $response);

        break;

    case 'ИТ и маркетинг':
        $response = $keyboardStaffDirectSales;
        $response['chat_id'] = $chat_id;
        $response['text'] = 'Выберите сотрудника:';

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

switch ($data){
    case '/department':

        $response = $keyboardDepartment;
        $response['chat_id'] = $chat_id_in;
        $response['text'] = 'Выберите отдел:';

        sendMessage($token, $response);

        break;

    case '/direct_sales':
        $db_conn = mysqli_connect($host, $user, $password, $db_name);
        $query = 'SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = `Прямые продажи` OR `Прямые продажи;Проектные продажи`';

        $result = mysqli_query($db_conn, $query);

        while($row = $result->fetch_assoc()) {
            $people[] = $row;
        }

        $keyboard = array(
            'reply_markup' => array(
                'inline_keyboard' => array(),
                'one_time_keyboard' => TRUE,
                'resize_keyboard' => TRUE,
            )
        );

        foreach($people as $i => $p) {
            $keyboard['reply_markup']['inline_keyboard'][$i][0]['text'] = $p['name'];
            $keyboard['reply_markup']['inline_keyboard'][$i][0]['callback_data'] = $p['telegram_id'];
        }

        $response = json_encode($keyboard);
        $response['chat_id'] = $chat_id_in;
        $response['text'] = 'Выберите сотрудника:';

        sendMessage($token, $response);

        break;

    case '/back':
        $response = $keyboardAction;
        $response['chat_id'] = $chat_id_in;
        $response['text'] = 'Здравствуйте ' . $first_name . ', что Вы хотите сделать?';

        sendMessage($token, $response);

        break;
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
