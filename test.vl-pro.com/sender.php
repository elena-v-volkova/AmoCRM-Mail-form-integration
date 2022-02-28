<?php
require_once 'access.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $_POST["phone"];
    $email = (empty($_POST['email'])) ? 'не указан' : $_POST['email'];

    // поля для контакта 
    $arrContactParams = [
        "name" => "Заявка Волкова",
        "phone" => $phone,
        "email" => $email,
    ];

    amoAddContact($access_token, $subdomain, $arrContactParams);

    header('Content-type:application/json;charset=utf-8');
    if (sendEmail($phone, $email)) {
        echo json_encode(["message" => "Заяка отправлена, скоро мы пришлем набор файлов"]);
    } else {
        echo json_encode(["message" => "Письмо не ушло"]);
    }
}

function sendEmail($phone, $email)
{
    $to = 'lena-063@mail.ru';
    $subject = 'Заявка Волкова';
    $subject .= "=?utf-8?b?" . base64_encode($subject) . "?=";
    $headers = "Content-type: text/html; charset=\"utf-8\"\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $message = '<strong>Телефон:</strong> ' . $phone . '<br />';
    $message .= '<strong>Email:</strong> ' . $email . '<br />';
    return mail($to, $subject, $message, $headers);
}

function amoAddContact($access_token, $subdomain, $arrContactParams)
{
    $contacts = array(
        [
            "name" => $arrContactParams["name"],
            "custom_fields_values" => [
                [
                    "field_id" => 912627,
                    "values" => [
                        [
                            "value" => $arrContactParams["phone"],
                        ]
                    ]
                ],
                [
                    "field_id" => 912625,
                    "values" => [
                        [
                            "value" => $arrContactParams["email"],
                        ]
                    ]
                ],
            ]
        ]
    );

    $method = "/api/v4/contacts";

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token,
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
    curl_setopt($curl, CURLOPT_URL, "https://" . $subdomain . ".amocrm.ru" . $method);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($contacts));
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookie.txt');
    curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookie.txt');
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    $out = curl_exec($curl);
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $code = (int)$code;
    $errors = [
        301 => 'Moved permanently.',
        400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
        401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
        403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
        404 => 'Not found.',
        500 => 'Internal server error.',
        502 => 'Bad gateway.',
        503 => 'Service unavailable.'
    ];

    if ($code < 200 || $code > 204) die("Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error'));
    // return json_decode($out, true);
}