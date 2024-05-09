<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterExpertiseModel
 * @package App\Models\Masters
 */
class MasterExpertiseModel extends Model
{
    use HasFactory;

    /** @var string  */
    protected $table = 'health_master_expertise';

    /** @var string[]  */
    protected $fillable = [
        'master_id',
        'name',
        'created_user',
        'updated_user',
    ];
}
