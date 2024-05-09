<?php

namespace App\Models\Categories;

use App\Models\Articles\ArticleModel;
use App\Models\Articles\ArticleCategoriesMappingsModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class SubCategoriesModel extends Model
{
    use HasFactory;

    protected $table = 'health_sub_categories';

    protected $primaryKey = 'sub_categories_id';

    protected $fillable = [
        'categories_id',
        'name',
        'en_name',
        'meta_title',
        'description',
        'status',
        'sort',
        'created_user',
        'updated_user',
    ];

    public function articles(): HasManyThrough
    {
        return $this->hasManyThrough(ArticleModel::class, ArticleCategoriesMappingsModel::class, 'category_id', 'articles_id', 'sub_categories_id', 'article_id')->whereNotNull('parent');
    }

    /**
     * @param $query
     */
    public function scopeActive($query)
    {
        $query->where('status', 1);
    }
}
