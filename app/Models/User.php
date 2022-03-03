<?php

namespace App\Models;

use App\Helpers\TokenGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class User extends Model {
    protected $table = 'users';
    /**
     * @var string[]
     */
    protected $fillable = [
        'first_name', 'last_name', 'email',
        'profile_pic', 'password',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'password', 'api_token',
    ];

    public function setPasswordAttribute(string $password) {
        $this->attributes['password'] = password_hash($password, PASSWORD_BCRYPT);
    }

    public function posts() {
        return $this->hasMany('App\Models\Post');
    }

    public function comments() {
        return $this->hasMany('App\Models\Comment');
    }

//    public function scopeIsPremium(Builder $query) {
//        $query->where('subscription', 'premium');
//    }

    // TODO: molto imperativo, probabilmente non la soluzione migliore
    public function isPremium(int $id): bool
    {
        return $this->subscription === 'premium';
    }

}






//namespace App\Models;
//
//use Illuminate\Auth\Authenticatable;
//use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
//use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
//use Laravel\Lumen\Auth\Authorizable;
//
//class User extends Model implements AuthenticatableContract, AuthorizableContract
//{
//    use Authenticatable, Authorizable, HasFactory;
//
//    /**
//     * The attributes that are mass assignable.
//     *
//     * @var string[]
//     */
//    protected $fillable = [
//        'first_name', 'last_name', 'email', 'password',
//    ];
//
//    /**
//     * The attributes excluded from the model's JSON form.
//     *
//     * @var string[]
//     */
//    protected $hidden = [
//        'password', 'api-token',
//    ];
//}
