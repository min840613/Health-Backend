<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleBlocksModel extends Model
{
    use HasFactory;

    protected $table = 'article_blocks';
    protected $primaryKey = 'id';
    protected $fillable = [
        'talent_id',
        'type',
    ];
}
