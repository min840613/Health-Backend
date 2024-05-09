<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Categories\MainCategoriesModel;

class ArticleCategoriesMappingsModel extends Model
{
    use HasFactory;

    protected $table = 'health_article_categories_mappings';
    protected $primaryKey = 'id';
    protected $fillable = [
        'article_id',
        'category_id',
        'sort',
        'parent',
    ];

    /**
    * @return HasOne
    */
    public function mainCategory(): HasOne
    {
        return $this->hasOne(MainCategoriesModel::class, 'categories_id', 'category_id');
    }
}
