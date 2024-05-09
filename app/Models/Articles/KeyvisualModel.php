<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KeyvisualModel extends Model
{
    use HasFactory;

    protected $table = 'health_keyvisual';
    protected $primaryKey = 'keyvisual_id';
    protected $fillable = [
        'source_id',
        'type',
        'title',
        'link',
        'image',
        'app_image',
        'start',
        'end',
        'status',
        'sort',
        'created_user',
        'updated_user',
    ];

    /**
     * @return HasOne
     */
    public function article(): HasOne
    {
        return $this->hasOne(ArticleModel::class, 'articles_id', 'source_id');
    }

    /**
     * @param $query
     */
    public function scopeActive($query)
    {
        $query->where('status', 1)->where('start', '<=', now())->where('end', '>=', now());
    }
}
