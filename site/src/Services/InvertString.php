<?php


namespace App\Services;


class InvertString
{
    public function getInvertString(string $str) : string
    {
        return strrev($str);
    }
}

/* ============================================================================
 * ============= Fichier créé par Vincent Commin et Louis Leenart =============
 * ============================================================================
 * */