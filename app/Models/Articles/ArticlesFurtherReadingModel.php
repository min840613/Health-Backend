<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Articles\ArticleCategoriesMappingsModel;
use App\Models\Articles\ArticleModel;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArticlesFurtherReadingModel extends Model
{
    use HasFactory;

    protected $table = 'health_articles_further_reading';
    protected $fillable = [
        'article_id',
        'recommendation_article_id',
        'type',
        'created_user',
        'updated_user',
    ];

    /**
    * @return HasOneThrough
    */
    public function furtherArticle(): HasOneThrough
    {
        return $this->hasOneThrough(ArticleCategoriesMappingsModel::class, ArticleModel::class, 'articles_id', 'article_id', 'article_id', 'articles_id')->whereNull('parent')->orderBy('sort','asc');
    }

    /**
    * @return HasOne
    */
    public function furtherArticleTitle(): HasOne
    {
        return $this->hasOne(ArticleModel::class, 'articles_id', 'recommendation_article_id');
    }

}
