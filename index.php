<?php
require_once "vendor/autoload.php";

$token = 'YOUR TELEGRAM API BOT TOKEN';

try {
    $client = (new app\ClientController($token))->run();
} catch (\TelegramBot\Api\InvalidJsonException $e) {
    $e->getMessage();
}
