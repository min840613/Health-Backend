<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class InstitutionsModel
 * @package App\Models\Masters
 */
class InstitutionsModel extends Model
{
    use HasFactory;

    /** @var string  */
    protected $table = 'health_institutions';

    /** @var string[]  */
    protected $fillable = [
        'name',
        'nick_name',
        'en_name',
        'is_centre',
        'sort',
        'status',
        'created_user',
        'updated_user',
    ];

    /**
     * @return HasMany
     */
    public function masters(): HasMany
    {
        return $this->hasMany(MastersModel::class, 'institution_id', 'id');
    }

    /**
     * @param $query
     */
    public function scopeIsCentre($query): void
    {
        $query->where('is_centre', 1);
    }

    /**
     * @param $query
     */
    public function scopeActive($query): void
    {
        $query->where('status', 1);
    }
}
