<?php

namespace App\Models\App;

use App\Models\Articles\ArticleModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class NotificationsModel
 * @package App\Models\App
 */
class NotificationsModel extends Model
{
    use HasFactory;

    /** @var string  */
    protected $table = 'health_push_notifications';

    /** @var string[]  */
    protected $fillable = [
        'source_id',
        'type',
        'categories_id',
        'category_en',
        'category',
        'push_notifications_status',
        'platform_type',
        'member_group',
        'message',
        'message_body',
        'content_type',
        'image',
        'url',
        'prepush',
        'pushed',
        'created_user',
        'updated_user',
    ];

    /**
     * @return HasOne
     */
    public function article(): HasOne
    {
        return $this->hasOne(ArticleModel::class, 'articles_id', 'source_id');
    }

    /**
     * @return HasOne
     */
    public function activity(): HasOne
    {
        return $this->hasOne(ActivitiesAnnouncementModel::class, 'id', 'source_id');
    }

    /**
     * @return HasOne
     */
    public function system(): HasOne
    {
        return $this->hasOne(SystemAnnouncementModel::class, 'id', 'source_id');
    }

    /**
     * @return HasOne
     */
    public function shake(): HasOne
    {
        return $this->hasOne(ShakeModel::class, 'shake_id', 'source_id');
    }
}
