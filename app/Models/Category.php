<?php

namespace App\Models;


use App\Helpers\IsDuplicate;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

/**
 * @property int id
 * @property string name
 */
class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = ['name'];

    public function posts() {
        return $this->belongsToMany(Post::class, 'posts_categories', 'category_id', 'post_id');
    }

    public function users() {
        return $this->belongsToMany(User::class, 'favorite_categories', 'category_id', 'user_id');
    }


    /**
     * Cerca di creare la categoria passata, se esiste già
     * usa quella esistente. Restituisce l'id
     *
     * @param string $name
     * @return self
     */
    public static function createIfNotExist(string $name): self {
        /** @var Category|null $alreadyExisting */
        $alreadyExisting = self::where('name', $name)->first();
        if ($alreadyExisting) {
            return $alreadyExisting;
        }

        $newCategory = new self();
        $newCategory->name = $name;
        $newCategory->save();

        return $newCategory;
    }
}