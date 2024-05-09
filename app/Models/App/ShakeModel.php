<?php

namespace App\Models\App;

use App\Models\Articles\ArticleModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class ShakeModel
 * @package App\Models\App
 */
class ShakeModel extends Model
{
    use HasFactory;

    /** @var string */
    protected $table = 'health_shake';

    /** @var string */
    protected $primaryKey = 'shake_id';

    /** @var string[] */
    protected $fillable = [
        'shake_id',
        'shake_status',
        'shake_title',
        'content',
        'shake_url',
        'shake_type',
        'shake_content_type',
        'is_ec_connect',
        'shake_time_start',
        'shake_time_end',
        'created_user',
        'updated_user',
    ];

    /**
     * @return HasMany
     */
    public function members(): HasMany
    {
        return $this->hasMany(ShakeMemberModel::class, 'shake_id', 'shake_id');
    }

    /**
     * shake_url 用作兩種用途，當 content_type = 1 則為文章 id
     * @return HasOne
     */
    public function article(): HasOne
    {
        return $this->hasOne(ArticleModel::class, 'articles_id', 'shake_url');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePublish($query)
    {
        return $query->where('shake_status', 1);
    }
}
