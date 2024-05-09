<?php

namespace App\Models\Articles;

use App\Models\Authors\AuthorsModel;
use App\Models\Categories\MainCategoriesModel;
use App\Models\Categories\SubCategoriesModel;
use App\Models\DailyViewCountModel;
use App\Models\Masters\MastersModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * Class ArticleModel
 * @package App\Models\Articles
 */
class ArticleModel extends Model
{
    use HasFactory;

    protected $table = 'health_articles';

    protected $primaryKey = 'articles_id';

    protected $fillable = [
        'articles_id',
        'articles_status',
        'publish',
        'title',
        'og_title',
        'seo_title',
        'author',
        'author_type',
        'medicine_article_category_id',
        'talent_category_id',
        'adult_flag',
        'image',
        'image_alt',
        'ogimage',
        'video_type',
        'video_id',
        'fb_ia_video',
        'tag',
        'match_searchs',
        'extended_article',
        'article_content',
        'is_line_article',
        'is_line_rss',
        'video_file_name',
        'is_mixerbox_article',
        'is_zimedia',
        'is_yahoo_rss',
        'collaborator',
        'created_user',
        'updated_user',
        'match_url',
    ];

    /**
     * @return HasManyThrough
     */
    public function mainCategories(): HasManyThrough
    {
        return $this->hasManyThrough(MainCategoriesModel::class, ArticleCategoriesMappingsModel::class, 'article_id', 'categories_id', 'articles_id', 'category_id')->whereNull('parent')->orderBy('sort', 'asc');
    }

    /**
     * 取得主主類別
     * @return HasOneThrough
     */
    public function mainCategory(): HasOneThrough
    {
        return $this->hasOneThrough(MainCategoriesModel::class, ArticleCategoriesMappingsModel::class, 'article_id', 'categories_id', 'articles_id', 'category_id')
            ->whereNull('parent')
            ->where('sort', 0);
    }

    /**
     * @return HasManyThrough
     */
    public function subCategories(): HasManyThrough
    {
        return $this->hasManyThrough(SubCategoriesModel::class, ArticleCategoriesMappingsModel::class, 'article_id', 'sub_categories_id', 'articles_id', 'category_id')->whereNotNull('parent');
    }

    /**
     * @return HasOne
     */
    public function authorModel(): HasOne
    {
        return $this->hasOne(AuthorsModel::class, 'id', 'author');
    }

    /**
     * @return HasOne
     */
    public function masterModel(): HasOne
    {
        return $this->hasOne(MastersModel::class, 'id', 'talent_category_id');
    }

    /**
     * @return BelongsToMany
     */
    public function masters(): BelongsToMany
    {
        return $this->belongsToMany(MastersModel::class, ArticleMasterMappingModel::class, 'article_id', 'master_id', 'articles_id', 'id');
    }

    /**
     * @return HasManyThrough
     */
    public function recommendations(): HasManyThrough
    {
        return $this->hasManyThrough(ArticleModel::class, ArticlesFurtherReadingModel::class, 'article_id', 'articles_id', 'articles_id', 'recommendation_article_id')->where('type', 'article');
    }

    /**
     * @return HasManyThrough
     */
    public function ArticlesSicknessMapping(): HasMany
    {
        return $this->hasMany(ArticlesSicknessModel::class, 'article_id', 'articles_id');
    }

    /**
     * @return HasManyThrough
     */
    public function yahooRecommendations(): HasManyThrough
    {
        return $this->hasManyThrough(ArticleModel::class, ArticlesFurtherReadingModel::class, 'article_id', 'articles_id', 'articles_id', 'recommendation_article_id')->where('type', 'yahoo');
    }

    /**
     * @return HasMany
     */
    public function viewCount(): HasMany
    {
        return $this->hasMany(DailyViewCountModel::class, 'source_id', 'articles_id');
    }

    /**
     * @return HasOne
     */
    public function keyVisual(): HasOne
    {
        return $this->hasOne(KeyvisualModel::class, 'source_id', 'articles_id');
    }

    /**
     * @return HasMany
     */
    public function tags(): HasMany
    {
        return $this->hasMany(ArticleTagMappingModel::class, 'article_id', 'articles_id');
    }

    /**
     * @param $query
     */
    public function scopeActive($query)
    {
        $query->where('articles_status', 1)->where('publish', '<', now())->where('adult_flag', 0);
    }

    // /**
    //  * @return BelongsToMany
    //  */
    // public function articlesByCategory(): BelongsToMany
    // {
    //     // belongsToMany(目標表單名稱，中介表單名稱，中介表單上參照自己的外鍵，中介表單上參照目標的外鍵，自己的關聯鍵，目標的關聯鍵)
    //     return $this->belongsToMany(MainCategoriesModel::class, ArticleCategoriesMappingsModel::class, 'article_id', 'category_id', 'articles_id', 'categories_id');
    // }
}
