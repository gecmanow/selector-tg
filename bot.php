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
$direct_sales = 'Прямые продажи';
$direct_project_sales = 'Прямые продажи/Проектные продажи';
$db = new PDO('mysql:dbname=' . $db_name . ';host=' . $host . ';charset=UTF8', $user, $password);
$query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'Прямые продажи' OR `departament` = 'Прямые продажи/Проектные продажи'");
//$query->bindValue(':direct_sales', "$direct_sales");
//$query->bindValue(':direct_project_sales', "$direct_project_sales");
$query->execute();
$result = $query->fetchAll(PDO::FETCH_ASSOC);

echo '<pre>' . print_r($result, true) . '</pre>';

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
                    'text' => 'Корневой',
                    'callback_data' => '/root',
                )
            ),
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
                    'text' => 'IT + маркетинг',
                    'callback_data' => '/it_and_marketing',
                )
            ),
            array(
                array(
                    'text' => 'Бухгалтерия',
                    'callback_data' => '/comptabilitat',
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
echo '<pre>' . print_r($keyboardStaffDirectSales, true) . '</pre>';
//if($data['message']['from']['id'] == 261803700) {

switch ($message) {
    case '/start':
        $query = $db->prepare('INSERT INTO actions (name, chat_id) VALUES (' . $first_name . ', ' . $chat_id . ')');
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);
        $response = $keyboardAction;
        $response['chat_id'] = $chat_id;
        $response['text'] = 'Здравствуйте ' . $first_name . ', что Вы хотите сделать?' . $db_response;

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
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'Прямые продажи' OR `departament` = 'Прямые продажи/Проектные продажи'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case '/project_sales':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'Проектные продажи' OR `departament` = 'Прямые продажи/Проектные продажи'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case '/supply':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'Снабжение'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case '/ved':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'ВЭД'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case '/hr':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'HR'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case '/it_and_marketing':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'IT + маркетинг'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case '/service':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'Сервис'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case '/root':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'Корневой'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case '/comptabilitat':
        $query = $db->prepare("SELECT `name`, `telegram_id` FROM `users` WHERE `departament` = 'Бухгалтерия'");
        $query->execute();
        $db_response = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = getWorkers($db_response, $chat_id_in);

        sendMessage($token, $response);

        break;

    case '/back':
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
