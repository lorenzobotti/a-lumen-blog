<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property int user_id
 * @property string title
 * @property string content
 */
class Post extends Model
{
    use SoftDeletes;

    protected $table = 'posts';
    protected $fillable = ['title', 'content'];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function comments() {
        return $this->hasMany('App\Models\Comment');
    }

//    public static function boot() {
//        parent::boot();
//        static::addGlobalScope(new OwnerScope);
//    }
}