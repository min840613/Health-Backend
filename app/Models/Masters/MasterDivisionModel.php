<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class MasterDivisionModel
 * @package App\Models\Masters
 */
class MasterDivisionModel extends Model
{
    use HasFactory;

    /** @var string  */
    protected $table = 'health_master_divisions';

    /** @var string[]  */
    protected $fillable = [
        'master_id',
        'division_id',
        'description',
        'created_user',
        'updated_user',
    ];

    /**
     * @return HasOne
     */
    public function division(): HasOne
    {
        return $this->hasOne(DivisionsModel::class, 'id', 'division_id');
    }
}
