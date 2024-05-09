<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNewsModel extends Model
{
    use HasFactory;
    protected $table = 'health_app_news';

    protected $fillable = [
        'message',
        'start',
        'end',
        'created_user',
        'updated_user'
    ];
}
