<?php

namespace App\Models\ThirdPartyFeed;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Articles\ArticleModel;

class MixerboxArticlesModel extends Model
{
    use HasFactory;

    protected $table = 'health_mixerbox_articles';

    protected $fillable = [
        'article_id',
        'status',
        'release_date',
        'created_user',
        'updated_user',
    ];

    protected $casts = [
        'release_date' => 'datetime:Y-m-d',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(ArticleModel::class, 'article_id', 'articles_id');
    }

    /**
     * @param $query
     * @return void
     */
    public function scopeActive($query): void
    {
        $query->where('status', 1);
    }

    public function getStatusCssAttribute(): string
    {
        if ($this->status == 0) {
            return '<i style="color: #b80000;" class="fa fa-times"></i>';
        }
        if ($this->status == 1) {
            return '<i style="color: green;" class="fa fa-check"></i>';
        }
    }
}
