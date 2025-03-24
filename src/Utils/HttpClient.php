<?php

namespace App\Utils;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class HttpClient
{
    public function __construct(private readonly RemoteWebDriver $webDriver)
    {
    }

    public function get(string $url): string
    {
        $this->webDriver->get($url);
        sleep(3);

        try {
            $element = $this->webDriver->findElement(WebDriverBy::cssSelector('.tbody.max-h-96.overflow-y-auto'));
        } catch (\Exception $e) {
            throw new \RuntimeException('СSS совпадений не найдено');
        }


        $this->webDriver->executeScript("arguments[0].scrollIntoView(true);", [$element]);
        sleep(1);


        $scrollY = $this->webDriver->executeScript("return window.scrollY;");
        $scrollX = $this->webDriver->executeScript("return window.scrollX;");

        $location = $element->getLocation();
        $size = $element->getSize();

        $x = $location->getX() - $scrollX;
        $y = $location->getY() - $scrollY;


        $screenshotPath = 'screenshot.png';
        $this->webDriver->takeScreenshot($screenshotPath);


        $image = imagecreatefrompng($screenshotPath);
        if ($image === false) {
            throw new \RuntimeException('Не удалось обработать изображение');
        }


        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        $x = max(0, min($x, $imageWidth - 1));
        $y = max(0, min($y, $imageHeight - 1));
        $width = min($size->getWidth(), $imageWidth - $x);
        $height = min($size->getHeight(), $imageHeight - $y);


        $croppedImage = imagecrop($image, [
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height
        ]);

        if ($croppedImage === false) {
            imagedestroy($image);
            throw new \RuntimeException('Не удалось обрезать изображение');
        }


        $screenshotPath = 'meta_screenshot.png';
        imagepng($croppedImage, $screenshotPath);


        imagedestroy($image);
        imagedestroy($croppedImage);

        return $screenshotPath;
    }
}