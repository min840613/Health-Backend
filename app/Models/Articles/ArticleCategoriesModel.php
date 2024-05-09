<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleCategoriesModel extends Model
{
    use HasFactory;

    protected $table = 'health_articles_categories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'status',
        'sort',
        'publish_time'
    ];
}
