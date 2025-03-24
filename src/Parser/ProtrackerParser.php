<?php

namespace App\Parser;

use DOMDocument;
use DOMXPath;

class ProtrackerParser
{
    public function parseMetaHeroes(string $html): array
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $heroes = [];
        $nodes = $xpath->query('//div[contains(@class, "tbody") and contains(@class, "max-h-96") and contains(@class, "overflow-y-auto")]');

        if ($nodes === false) {
            throw new \RuntimeException('Failed to parse meta heroes.');
        }

        if ($nodes->length === 0) {
            throw new \RuntimeException('No meta heroes found.');
        }

        foreach ($nodes as $node) {
            $heroes[] = $node->nodeValue;
        }

        return $heroes;
    }
}
