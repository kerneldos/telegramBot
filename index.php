<?php
require_once "vendor/autoload.php";

$token = 'token';

try {
    $client = (new app\ClientController($token))->run();
} catch (TelegramBot\Api\InvalidJsonException $e) {
    $e->getMessage();
}
