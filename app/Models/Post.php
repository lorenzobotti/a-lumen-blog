<?php

namespace App\Models;

use App\Models\PostLike;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
    protected $hidden = ['likes'];
    protected $appends = ['like_count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes() {
        return $this->hasMany(PostLike::class, 'post_id', 'id');
    }

    public function categories() {
        return $this->belongsToMany(Category::class,'posts_categories', 'post_id', 'category_id');
    }

    public function likeCount(): Attribute {
//        return Attribute::make(
//            get: fn($val) => count($val),
//        );
          return Attribute::make(
            get: fn($val) => $this->likes()->count(),
        );

        // return $this->likes()->count();
    }
}