<?php

namespace App\Models\Masters;

use App\Models\Articles\ArticleMasterMappingModel;
use App\Models\Articles\ArticleModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class MastersModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'health_masters';

    protected $guarded = [];

    /** @var string[]  */
    protected $fillable = [
        'status',
        'type',
        'name',
        'en_name',
        'image',
        'content_image',
        'description',
        'title',
        'institution_id',
        'is_contracted',
        'sort',
        'created_user',
        'updated_user',
    ];

    /**
     * @return HasOne
     */
    public function institution(): HasOne
    {
        return $this->hasOne(InstitutionsModel::class, 'id', 'institution_id');
    }

    /**
     * @return HasMany
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(MasterExperiencesModel::class, 'master_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function expertise(): HasMany
    {
        return $this->hasMany(MasterExpertiseModel::class, 'master_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function divisions(): HasMany
    {
        return $this->hasMany(MasterDivisionModel::class, 'master_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(ArticleModel::class, ArticleMasterMappingModel::class, 'master_id', 'article_id', 'id', 'articles_id');
    }

    /**
     * @param $query
     */
    public function scopeActive($query): void
    {
        $query->where('status', 1);
    }

    /**
     * @param $query
     */
    public function scopeIsDoctor($query): void
    {
        $query->where('type', 1);
    }

    /**
     * @return string
     */
    public function getStatusCssAttribute(): string
    {
        if ($this->status == 0) {
            return '<i style="color: #b80000;" class="fa fa-times"></i>';
        }
        if ($this->status == 1) {
            return '<i style="color: green;" class="fa fa-check"></i>';
        }
    }

    public function getMasterTypeNameAttribute(): string
    {
        if ($this->type == 1) {
            return '醫師';
        }
        if ($this->type == 2) {
            return '專家';
        }
        if ($this->type == 3) {
            return '營養師';
        }
    }
}
