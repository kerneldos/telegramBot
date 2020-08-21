<?php


namespace app;


class ClientController extends \TelegramBot\Api\Client {

    public function run() {
        //команда для start
        $this->command('start', function ($message) {
            $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                ['/help', '/showInlineKeyboard']
            ], null, true);

            $answer = 'Добро пожаловать!';
            $this->api->sendMessage($message->getChat()->getId(), $answer, null, false, null, $keyboard);
        });

        //команда для помощи
        $this->command('help', function ($message) {
            $answer = '
                Команды:
                    /help - вывод справки
                    /showInlineKeyboard - отображение inline клавиатуры
            ';

            /**
             * @var \TelegramBot\Api\Types\Message $message
             */
            $this->api->sendMessage($message->getChat()->getId(), $answer);
        });

        $this->command('showInlineKeyboard', function ($message) {
            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup([
                [
                    ['text' => 'Показать новости', 'callback_data' => '/posts'],
                    ['text' => 'Показать картинку', 'callback_data' => '/picture'],
                ]
            ]);

            $this->api->sendMessage($message->getChat()->getId(), 'Выберете действие:', null, false, null, $keyboard);
        });

        $this->callbackQuery(function ($update) {
            /**
             * @var \TelegramBot\Api\Types\CallbackQuery $update
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
                    $media = new \TelegramBot\Api\Types\InputMedia\ArrayOfInputMedia();
                    $media->addItem(new \TelegramBot\Api\Types\InputMedia\InputMediaPhoto('http://lorempixel.com/640/480/nature'));

                    $this->api->sendMediaGroup($message->getChat()->getId(), $media);
                    break;
                default:
                    $this->api->sendMessage($message->getChat()->getId(), $update->getData());
            }
        });

        //        $updates = $this->api->getUpdates();

        //        $update = BotApi::jsonValidate($this->getRawBody(), true);
        //        $update = json_decode($this->getRawBody(), true);
        //
        //        if (!empty($update['callback_query'])) {
        //
        //            $this->callbackQuery(function ($query) {
        //                /**
        //                 * @var TelegramBot\Api\Types\CallbackQuery $query
        //                 */
        //                $this->api->sendMessage($query->getMessage()->getChat()->getId(), 'answer');
        //            });
        //
        //            $chatId = $update['callback_query']["message"]["chat"]["id"];
        //            $message = $update["message"]["text"];
        ////            echo print_r($updates, true);
        //            try {
        //                $this->api->sendMessage($chatId, 'answer');
        //            } catch (\TelegramBot\Api\InvalidArgumentException $e) {
        //                $e->getMessage();
        //            } catch (\TelegramBot\Api\Exception $e) {
        //                $e->getMessage();
        //            }
        //        }

        parent::run();
    }
}
