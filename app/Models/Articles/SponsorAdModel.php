<?php

namespace App\Models\Articles;

use App\Enums\SponsorAdCategoriesType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Categories\MainCategoriesModel;
use App\Models\Categories\SubCategoriesModel;

/**
 * Class SponsorAdModel
 * @package App\Models\Articles
 */
class SponsorAdModel extends Model
{
    use HasFactory;

    /** @var string  */
    protected $table = 'health_sponsor_ad';

    /** @var string[]  */
    protected $fillable = [
        'article_id',
        'categories_list_id',
        'categories_type',
        'position',
        'start',
        'end',
        'created_user',
        'updated_user',
    ];

    protected $casts = [
        'categories_type' => 'integer',
    ];

    /**
     * @return HasOne
     */
    public function article(): HasOne
    {
        return $this->hasOne(ArticleModel::class, 'articles_id', 'article_id');
    }

    /**
     * @return HasOne
     */
    public function MainCategories(): HasOne
    {
        return $this->hasOne(MainCategoriesModel::class, 'categories_id', 'categories_list_id');
    }

    /**
     * @return HasOne
     */
    public function mainCategory(): HasOne
    {
        return $this->hasOne(MainCategoriesModel::class, 'categories_id', 'categories_list_id');
    }

    /**
     * @return HasOne
     */
    public function SubCategories(): HasOne
    {
        return $this->hasOne(SubCategoriesModel::class, 'categories_id', 'categories_list_id');
    }

    /**
     * @param $query
     */
    public function scopeActive($query)
    {
        $query->where('start', '<=', now())->where('end', '>=', now());
    }

    /**
     * @param $query
     */
    public function scopeMainCategoryType($query)
    {
        $query->where('categories_type', SponsorAdCategoriesType::MAIN);
    }

    /**
     * @param $query
     */
    public function scopeSubCategoryType($query)
    {
        $query->where('categories_type', SponsorAdCategoriesType::SUB);
    }
}
