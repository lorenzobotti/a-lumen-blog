<?php

namespace App\Helpers;

use Illuminate\Database\QueryException;

class ExceptionHelper
{
    /**
     * Determina se l'eccezione riportata da Model::save() è
     * dovuta a una chiave con constraint unique di cui stiamo
     * creando un duplicato
     *
     * @param QueryException $e
     * @return bool
     */
    public static function isDuplicate(QueryException $e): bool {
        return $e->errorInfo[1] === 1062;
    }
}