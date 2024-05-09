<?php

namespace App\Models\ThirdPartyFeed;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Articles\ArticleModel;
use App\Models\Articles\ArticlesFurtherReadingModel;

class YahooRssModel extends Model
{
    use HasFactory;

    /** @var string  */
    protected $table = 'health_yahoo_rss';

    protected $guarded = [
        'id',
    ];

    /**
    * @return HasOne
    */
    public function article(): HasOne
    {
        return $this->hasOne(ArticleModel::class, 'articles_id', 'article_id');
    }

    /**
    * @return hasMany
    */
    public function furtherReading(): hasMany
    {
        return $this->hasMany(ArticlesFurtherReadingModel::class, 'article_id', 'article_id')->where('type', 'yahoo');;
    }

    public function getStatusCssAttribute(): string
    {
        if($this->status == 0){
            return '<i style="color: #b80000;" class="fa fa-times"></i>';
        }
        if($this->status == 1){
            return '<i style="color: green;" class="fa fa-check"></i>';
        }
    }
}
