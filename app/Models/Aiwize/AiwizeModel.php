<?php

namespace App\Models\Aiwize;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiwizeModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'health_ai_wize';

    protected $fillable = [
        'ai_wize_id',
        'ai_wize_publish',
        'health_article_id',
        'long_title',
        'short_title',
        'content',
        'keyword',
        'choose_user',
        'status',
    ];

    public function getStatusCssAttribute(): string
    {
        if ($this->status == 0) {
            return '<div class="text-danger">已使用</div>';
        }
        if ($this->status == 1) {
            return '<div class="text-primary">未使用</div>';
        }
    }
}
