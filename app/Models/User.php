<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string subscription
 * @property string first_name
 * @property string last_name
 * @property string api_token
 * @property string password
 * @property \DateTime banned_at
 */
class User extends Model {
    protected $table = 'users';
    protected $fillable = [
        'first_name', 'last_name', 'email',
        'profile_pic', 'password',
    ];
    protected $hidden = ['password', 'api_token'];

    public function setPasswordAttribute(string $password) {
        $this->attributes['password'] = password_hash($password, PASSWORD_BCRYPT);
    }

    public function posts() {
        return $this->hasMany('App\Models\Post');
    }

    public function comments() {
        return $this->hasMany('App\Models\Comment');
    }

    public function isPremium(): bool
    {
        return $this->subscription === 'premium';
    }

    public function isMod(): bool
    {
        return $this->subscription === 'mod';
    }
}