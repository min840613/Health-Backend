<?php

namespace App\Models\Encyclopedia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticlesCategoriesModel extends Model
{
    use HasFactory;
    protected $table = 'health_articles_categories';
    protected $fillable = [
        'name',
        'status',
        'sort',
        'publish_time',
        'created_user',
        'updated_user'
    ];

    public function getStatusCssAttribute(): string
    {
        if($this->status == 1){
            return '<i style="color: green;" class="fa fa-check"></i>';
        }else{
            return '<i style="color: #b80000;" class="fa fa-times"></i>';
        }
    }
}
