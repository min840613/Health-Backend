<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleMasterMappingModel extends Model
{
    use HasFactory;

    /** @var string */
    protected $table = 'health_article_master_mapping';

    /** @var bool */
    public $timestamps = false;

    /** @var string[] */
    protected $fillable = [
        'article_id',
        'master_id',
    ];
}
