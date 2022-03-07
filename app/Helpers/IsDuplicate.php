<?php

namespace App\Helpers;

use Illuminate\Database\QueryException;

class IsDuplicate
{
    /**
     * Determina se l'eccezione riportata da Model::save() è
     * dovuta a una chiave con constraint unique di cui stiamo
     * creando un duplicato
     *
     * @param QueryException $e
     * @return bool
     */
    public static function isDuplicate(QueryException $e) {
        return $e->errorInfo[1] === 1062;
    }
}