<?php
require_once "vendor/autoload.php";

$token = '1178456278:AAEw93uhOVW7qL7n6vbE-gmGH2YwA0X03Rk';

try {
    $client = (new app\ClientController($token))->run();
} catch (\TelegramBot\Api\InvalidJsonException $e) {
    $e->getMessage();
}
