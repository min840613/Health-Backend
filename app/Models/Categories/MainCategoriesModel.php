<?php

namespace App\Models\Categories;

use App\Enums\MainCategoriesType;
use App\Models\Articles\ArticleCategoriesMappingsModel;
use App\Models\Articles\ArticleModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainCategoriesModel extends Model
{
    use HasFactory;

    protected $table = 'health_categories';
    protected $primaryKey = 'categories_id';
    protected $fillable = [
        'categories_type',
        'publish',
        'name',
        'en_name',
        'meta_title',
        'description',
        'image',
        'categories_status',
        'show_category_menu',
        'sort_index',
        'target',
        'is_nav',
        'index_position',
        'created_user',
        'updated_user'
    ];

    /**
     * @param $query
     */
    public function scopeActive($query)
    {
        $query->where('categories_status', 1);
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategoriesModel::class, 'categories_id', 'categories_id');
    }

    public function articles()
    {
        return $this->hasManyThrough(ArticleModel::class, ArticleCategoriesMappingsModel::class, 'category_id', 'articles_id', 'categories_id', 'article_id')->whereNull('parent')->orderBy('sort','asc');
    }

    /**
     * @param $query
     */
    public function scopeFilterAdvertorial($query)
    {
        $query->whereNot('categories_type', MainCategoriesType::ADVERTORIAL);
    }

    public function getCategoriesStatusCssAttribute(): string
    {
        if($this->categories_status == 0){
            return '<i style="color: #b80000;" class="fa fa-times"></i>';
        }
        if($this->categories_status == 1){
            return '<i style="color: green;" class="fa fa-check"></i>';
        }
    }
}
