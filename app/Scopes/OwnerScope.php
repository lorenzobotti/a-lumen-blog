<?php


namespace App\Scopes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OwnerScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::check()) {
            $builder->where('user_id', Auth::user()->id);
            // $builder->where('user_id', 'hamburger');
        } else {
            // TODO: nega accesso a utenti non loggati
        }
    }
}