<?php

namespace App\Models\Encyclopedia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Encyclopedia\OrgansModel;
use App\Models\Encyclopedia\SicknessToOrganModel;
use App\Models\Articles\ArticlesSicknessModel;
use App\Models\Articles\ArticleModel;

class SicknessModel extends Model
{
    use HasFactory;
    protected $table = 'health_sickness';
    protected $fillable = [
        'status',
        'name',
        'sort',
        'created_user',
        'updated_user'
    ];

    public function organs(): BelongsToMany
    {
        return $this->belongsToMany(OrgansModel::class, SicknessToOrganModel::class, 'sickness_id', 'organ_id');
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(ArticleModel::class, ArticlesSicknessModel::class, 'health_sickness_id', 'article_id');
    }

    public function scopeActive($query)
    {
        $query->where('status', 1);
    }
}
