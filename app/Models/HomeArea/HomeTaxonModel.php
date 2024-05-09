<?php

namespace App\Models\HomeArea;

use App\Models\Articles\ArticleCategoriesMappingsModel;
use App\Models\Categories\MainCategoriesModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Articles\ArticleModel;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HomeTaxonModel extends Model
{
    use HasFactory;

    protected $table = 'health_home_taxon';
    protected $fillable = [
        'status',
        'name',
        'categories_id',
        'article_id',
        'sort',
        'published_at',
        'published_end',
        'created_user',
        'updated_user',
    ];

    public function getCategoriesStatusCssAttribute(): string
    {
        if ($this->status == 0) {
            return '<i style="color: #b80000;" class="fa fa-times"></i>';
        }
        if ($this->status == 1) {
            return '<i style="color: green;" class="fa fa-check"></i>';
        }
    }

    /**
     * @return HasOne
     */
    public function article(): HasOne
    {
        return $this->hasOne(ArticleModel::class, 'articles_id', 'article_id');
    }

    /**
     * @return HasManyThrough
     */
    public function categoryArticles(): HasManyThrough
    {
        return $this->hasManyThrough(ArticleModel::class, ArticleCategoriesMappingsModel::class, 'category_id', 'articles_id', 'categories_id', 'article_id')->whereNull('parent')->where('sort', 0);
    }

    /**
     * @return HasOne
     */
    public function mainCategory(): HasOne
    {
        return $this->hasOne(MainCategoriesModel::class, 'categories_id', 'categories_id');
    }

    /**
     * @param $query
     */
    public function scopeActive($query)
    {
        $query->where('status', 1);
    }
}
