<?php


namespace App\Services;


class InvertString
{
    public function getInvertString(string $str) : string
    {
        return strrev($str);
    }
}