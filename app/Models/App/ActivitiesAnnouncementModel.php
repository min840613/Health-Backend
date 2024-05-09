<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ActivitiesAnnouncementModel
 *
 * @package App\Models\App
 * @property int $id
 * @property string $title 標題
 * @property string $content 系統公告內容
 * @property string $image_url 圖片URL
 * @property string $created_user 建立者
 * @property string|null $updated_user 修改者
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitiesAnnouncementModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitiesAnnouncementModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitiesAnnouncementModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitiesAnnouncementModel whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitiesAnnouncementModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitiesAnnouncementModel whereCreatedUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitiesAnnouncementModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitiesAnnouncementModel whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitiesAnnouncementModel whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitiesAnnouncementModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitiesAnnouncementModel whereUpdatedUser($value)
 * @mixin \Eloquent
 */
class ActivitiesAnnouncementModel extends Model
{
    use HasFactory;

    /** @var string  */
    protected $table = 'health_activities_announcement';

    /** @var string[]  */
    protected $fillable = [
        'title',
        'content',
        'image_url',
        'created_user',
        'updated_user',
    ];
}
