<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSplashModel extends Model
{
    use HasFactory;
    protected $table = 'health_app_splash';
    protected $fillable = [
        'android_image',
        'iOS_image',
        'start',
        'end',
        'status',
        'created_user',
        'updated_user'
    ];

    public function getStatusCssAttribute(): string
    {
        if ($this->status == 0) {
            return '下架';
        }
        if ($this->status == 1) {
            return '上架';
        }
    }
}
