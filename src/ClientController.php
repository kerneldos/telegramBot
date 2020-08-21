<?php


namespace app;

use TelegramBot\Api\Client;
use TelegramBot\Api\Types\CallbackQuery;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\InputMedia\ArrayOfInputMedia;
use TelegramBot\Api\Types\InputMedia\InputMediaPhoto;
use TelegramBot\Api\Types\InputMedia\InputMediaVideo;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\Update;

class ClientController extends Client {

    public function run() {
        //команда для start
        $this->command('start', function ($message) {
            $keyboard = new ReplyKeyboardMarkup([
                ['/help', '/showInlineKeyboard']
            ], null, true);

            $answer = 'Добро пожаловать!';
            $this->api->sendMessage($message->getChat()->getId(), $answer, null, false, null, $keyboard);
        });

        $helpAction = function ($message) {
            $answer = '
                Команды:
                    /help - вывод справки
                    /showInlineKeyboard - отображение inline клавиатуры
            ';

            /**
             * @var Message $message
             */
            $this->api->sendMessage($message->getChat()->getId(), $answer);
        };

        //команда для помощи
        $this->command('help', $helpAction);

        $this->command('showInlineKeyboard', function ($message) {
            $keyboard = new InlineKeyboardMarkup([
                [
                    ['text' => 'Показать новости', 'callback_data' => '/posts'],
                    ['text' => 'Показать картинку', 'callback_data' => '/picture'],
                    ['text' => 'Показать видео', 'callback_data' => '/video'],
                ]
            ]);

            $this->api->sendMessage($message->getChat()->getId(), 'Выберете действие:', null, false, null, $keyboard);
        });

        $this->callbackQuery(function ($update) {
            /**
             * @var CallbackQuery $update
             */

            $message = $update->getMessage();
            $action = $update->getData();

            switch ($action) {
                case '/posts':
                    $html = simplexml_load_file('https://netology.ru/blog/feed');

                    $answer = '';
                    foreach ($html->channel->item as $item) {
                        $answer .= "\xE2\x9E\xA1 " . $item->title . " (<a href='" . $item->link . "'>читать</a>)\n\n";
                    }

                    $this->api->sendMessage($message->getChat()->getId(), $answer, 'HTML');

                    break;
                case '/picture':
                    $media = new ArrayOfInputMedia();
                    $media->addItem(new InputMediaPhoto('http://lorempixel.com/640/480/nature'));

                    $this->api->sendMediaGroup($message->getChat()->getId(), $media);
                    break;
                case '/video':
                    $media = new ArrayOfInputMedia();
                    $media->addItem(new InputMediaVideo('http://clips.vorwaerts-gmbh.de/VfE_html5.mp4'));

                    $this->api->sendMediaGroup($message->getChat()->getId(), $media);
                    break;
                default:
                    $this->api->sendMessage($message->getChat()->getId(), $update->getData());
            }
        });

        $update = Update::fromResponse(json_decode($this->getRawBody(), true));

        if (!empty($update->getMessage()) && empty($update->getMessage()->getEntities())) {
            $update->getMessage()->setText('/help');

            $this->events->handle($update);
        }

        parent::run();
    }
}
