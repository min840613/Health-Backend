<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterExperiencesModel
 * @package App\Models\Masters
 */
class MasterExperiencesModel extends Model
{
    use HasFactory;

    /** @var string  */
    protected $table = 'health_master_experiences';

    /** @var string[]  */
    protected $fillable = [
        'master_id',
        'name',
        'is_current_job',
        'created_user',
        'updated_user',
    ];
}
