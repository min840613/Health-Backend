<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SystemAnnouncementModel
 * @package App\Models\App
 */
class SystemAnnouncementModel extends Model
{
    use HasFactory;

    /** @var string  */
    protected $table = 'health_system_announcement';

    /** @var string[]  */
    protected $fillable = [
        'title',
        'content',
        'image_url',
        'created_user',
        'updated_user',
    ];
}
