<?php

namespace App\Models\Encyclopedia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Encyclopedia\BodyModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrgansModel extends Model
{
    use HasFactory;
    protected $table = 'health_organs';
    protected $fillable = [
        'body_id',
        'status',
        'name',
        'icon',
        'icon_android',
        'icon_ios',
        'sort',
        'created_user',
        'updated_user'
    ];

    public function body(): BelongsTo
    {
        return $this->belongsTo(BodyModel::class, 'body_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function sickness(): BelongsToMany
    {
        return $this->belongsToMany(SicknessModel::class, 'health_sickness_to_organ', 'organ_id', 'sickness_id');
    }
}
