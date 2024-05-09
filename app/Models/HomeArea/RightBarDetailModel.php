<?php

namespace App\Models\HomeArea;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Articles\ArticleModel;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RightBarDetailModel extends Model
{
    use HasFactory;
    protected $table = 'health_right_bar_detail';
    protected $fillable = [
        'name',
        'right_bar_id',
        'article_id',
        'status',
        'sort',
        'published_at',
        'published_end',
        'created_user',
        'updated_user',
    ];

    public function scopeActive($query)
    {
        $query->where('status', 1)
                ->where('published_at', '<=', Carbon::now())
                ->where('published_end', '>=', Carbon::now());
    }

    public function article(): HasOne
    {
        return $this->hasOne(ArticleModel::class, 'articles_id', 'article_id');
    }

    public function getStatusCssAttribute()
    {
        if($this->status == 1 && $this->published_at <= Carbon::now() && $this->published_end >= Carbon::now()){
            return '<i style="color: green;" class="fa fa-check"></i>';
        }else{
            return '<i style="color: #b80000;" class="fa fa-times"></i>';
        }
    }
}
