<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class DivisionsModel
 * @package App\Models\Masters
 */
class DivisionsModel extends Model
{
    use HasFactory;

    /** @var string  */
    protected $table = 'health_divisions';

    /** @var string[]  */
    protected $fillable = [
        'status',
        'name',
        'en_name',
        'icon',
        'icon_hover',
        'icon_android',
        'icon_android_hover',
        'icon_ios',
        'icon_ios_hover',
        'sort',
        'created_user',
        'updated_user',
    ];

    /**
     * @return BelongsToMany
     */
    public function masters(): belongsToMany
    {
        return $this->belongsToMany(MastersModel::class, MasterDivisionModel::class, 'division_id', 'master_id');
    }

    /**
     * @param $query
     */
    public function scopeActive($query): void
    {
        $query->where('status', 1);
    }
}
