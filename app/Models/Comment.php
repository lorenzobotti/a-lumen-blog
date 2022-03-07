<?php

namespace App\Models;

use App\Models\Post;
use App\Models\User;
use App\Models\CommentLike;
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function likes() {
        return $this->hasMany(CommentLike::class);
    }

    function canEditOrDelete(User $user)
    {
        return $user->isMod() || ($user->isPremium() && $user->id == $this->user_id);
    }
}