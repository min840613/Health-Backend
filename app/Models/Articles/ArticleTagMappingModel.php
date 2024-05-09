<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleTagMappingModel extends Model
{
    use HasFactory;

    protected $table = 'health_articles_tag_mapping';
    protected $primaryKey = 'article_id';
    protected $fillable = [
        'article_id',
        'tag'
    ];
}
