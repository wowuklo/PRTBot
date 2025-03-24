<?php

namespace App\Bot;

class TelegramApi
{

    public function __construct(readonly string $token) {

    }

    public function sendMessage(int $chatId, string $message): void
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";
        $url .= "?chat_id={$chatId}&text=" . urlencode($message);

        $response = file_get_contents($url);
        if ($response === false) {
            throw new \RuntimeException("Couldn't send message");
        }
    }

    public function sendPhoto(int $chatId, string $photoPath): void
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendPhoto";

        $postFields = [
            "chat_id" => $chatId,
            "photo" =>new \CURLFile($photoPath)
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \RuntimeException("Couldn't send photo" . curl_error($ch));
        }

        curl_close($ch);
    }

}