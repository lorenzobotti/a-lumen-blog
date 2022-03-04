<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int user_id
 * @property int post_id
 * @property string content
 */
class Comment extends Model
{
    protected $table = 'comments';
    protected $fillable = ['content'];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function post() {
        return $this->belongsTo('App\Models\Post');
    }

    function canEditOrDelete(User $user) {
        return $user->isMod() || ($user->isPremium() && $user->id == $this->user_id);
    }
}