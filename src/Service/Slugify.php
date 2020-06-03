<?php

namespace App\Service;

class Slugify
{
    public function generate(string $input) : string
    {
        $input = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $input);
        $input = strtolower($input);
        $input = str_replace(' ', '-', $input);
        $input = preg_replace("#[^a-z0-9-]*#","", $input);
        $input = str_replace('--', '', $input);
        return $input;
    }
}