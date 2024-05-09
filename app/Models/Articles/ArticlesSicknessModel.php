<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticlesSicknessModel extends Model
{
    use HasFactory;
    protected $table = 'health_articles_sickness';
    protected $fillable = [
        'article_id',
        'health_sickness_id',
        'created_user',
        'updated_user'
    ];
}
