<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string content
 */
class Comment extends Model
{
    protected $table = 'comments';
    protected $fillable = [
        'content',
    ];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function post() {
        return $this->belongsTo('App\Models\Post');
    }

    public static function boot() {
        parent::boot();
        static::addGlobalScope('OwnerScope');
    }

}