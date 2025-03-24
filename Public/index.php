<?php

namespace App;

require __DIR__ . '/../vendor/autoload.php';

use App\Bot\Bot;
use App\Bot\TelegramApi;
use App\Parser\ProtrackerParser;
use App\Utils\HttpClient;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

$config = require __DIR__ . '/../config/config.php';

$host = 'http://localhost:4444';
$options = new ChromeOptions();
$options->addArguments(['--headless']);
$capabilities = DesiredCapabilities::chrome();
$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

$driver = RemoteWebDriver::create($host, $capabilities);

$httpClient = new HttpClient($driver);
$parser = new ProtrackerParser();
$telegramApi = new TelegramApi($config['telegram_token']);
$bot = new Bot($httpClient, $telegramApi,$parser );

$content = file_get_contents('php://input');
$updates = json_decode($content, true);

$offset = 0;

while (true) {
    $updates = file_get_contents("https://api.telegram.org/bot{$config['telegram_token']}/getUpdates?offset=$offset");
    $updates = json_decode($updates, true);

    if (isset($updates['result'])) {
        foreach ($updates['result'] as $update) {
            $offset = $update['update_id'] + 1;
            $chatId = $update['message']['chat']['id'];
            $text = $update['message']['text'];

            $bot->handleCommand($chatId, $text);
        }
    }
    sleep(1);
}
