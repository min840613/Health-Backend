<?php

namespace App\Models\ThirdPartyFeed;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MixerboxArticleConditionModel extends Model
{
    use HasFactory;

    protected $table = 'health_mixerbox_article_condition';

    protected $fillable = [
        'category_id',
        'category_en_name',
        'category_name',
        'created_user',
        'updated_user',
    ];
}
