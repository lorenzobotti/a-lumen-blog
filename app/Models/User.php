<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string subscription
 * @property string first_name
 * @property string email
 * @property string last_name
 * @property string api_token
 * @property string password
 * @property \DateTime banned_at
 */
class User extends Model
{
    protected $table = 'users';
    protected $fillable = [
        'first_name', 'last_name', 'email',
        'profile_pic', 'password',
    ];
    protected $hidden = ['password', 'api_token'];

    public function setPasswordAttribute(string $password)
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_BCRYPT);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes() {
        return $this->hasMany(PostLike::class);
    }

    public function favorites() {
        // ?????
        return $this->hasManyThrough(Post::class, Category::class, );
    }

    public function categories() {
        return $this->belongsToMany(Category::class, 'favorite_categories', 'user_id', 'category_id');
    }

    public function isPremium(): bool
    {
        return $this->subscription === 'premium';
    }

    public function isMod(): bool
    {
        return $this->subscription === 'mod';
    }

    public function isBanned(): bool
    {
        return $this->banned_at != null;
    }

    function canEditOrDelete(Comment $comment)
    {
        return $this->isMod() || ($this->isPremium() && $this->id == $comment->user_id);
    }
}