<?php

namespace App\Models\HomeArea;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Categories\MainCategoriesModel;
use App\Models\Categories\SubCategoriesModel;
use App\Models\HomeArea\RightBarDetailModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RightBarModel extends Model
{
    use HasFactory;
    protected $table = 'health_right_bar';
    protected $fillable = [
        'name',
        'main_category',
        'sub_category',
        'article_require_master',
        'status',
        'sort',
        'created_user',
        'updated_user',
    ];

    public function mainCategory(): HasOne
    {
        return $this->hasOne(MainCategoriesModel::class, 'categories_id', 'main_category');
    }

    public function subCategory(): HasOne
    {
        return $this->hasOne(SubCategoriesModel::class, 'sub_categories_id', 'sub_category');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(RightBarDetailModel::class, 'right_bar_id', 'id')->orderBy('sort', 'asc');
    }

    public function scopeActive($query)
    {
        $query->where('status', 1);
    }

    public function getCategoriesStatusCssAttribute(): string
    {
        if($this->status == 0){
            return '<i style="color: #b80000;" class="fa fa-times"></i>';
        }
        if($this->status == 1){
            return '<i style="color: green;" class="fa fa-check"></i>';
        }
    }
}
