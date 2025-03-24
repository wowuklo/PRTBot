<?php

namespace App\Bot;

use App\Parser\ProtrackerParser;
use App\Utils\HttpClient;
class Bot
{

    public function __construct(
        private readonly HttpClient $httpClient,
        private readonly TelegramApi $telegramApi,
    )
    {
    }

    public function handleCommand(int $chatId, string $command): void
    {
        if ($command === '/start') {
            $response = 'Привет, я твой бот! Я могу помочь тебе узнать текущих метовых героев в Dota 2. Используй команду /meta, чтобы получить список.';
            $this->telegramApi->sendMessage($chatId, $response);
            return;
        }

        if ($command === '/meta') {
            $url = "https://dota2protracker.com/";
            $screenshotPath = $this->httpClient->get($url);

            $this->telegramApi->sendPhoto($chatId, $screenshotPath);
        } else {
            $this->telegramApi->sendMessage($chatId, "Ты чушок ебаный,используй /meta для поиска метовых героев,а не пиши хуйню в чат");
        }
    }

}