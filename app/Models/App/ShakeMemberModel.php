<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ShakeMemberModel
 * @package App\Models\App
 */
class ShakeMemberModel extends Model
{
    use HasFactory;

    /** @var string 資料來自於 app 寫入，故無法接新表 */
    protected $connection = 'mysql_tvbs_v4';

    /** @var string  */
    protected $table = 'health_shake_member';

    /** @var string[]  */
    protected $fillable = [
        'xnm_id',
        'xnmail',
        'xnbirthday',
        'xnnickname',
        'xnphone',
        'xnsex',
        'xnaddress',
        'xn_time_activity',
        'xn_time_mk',
        'shake_id',
        'shake_status',
        'profile_data',
        'profile',
        'error_code',
        'update_source',
        'app_version',
        'device_code',
        'pass_status',
        'acr',
        'created_user',
        'updated_user',
    ];

    /**
     * @return string
     */
    public function getShakeStatusStringAttribute(): string
    {
        $statusMappings = [0 => '失敗', 1 => '成功', 2 => '直接進入'];

        return $statusMappings[$this->shake_status];
    }
}
