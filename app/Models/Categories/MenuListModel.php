<?php

namespace App\Models\Categories;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Categories\SubCategoriesModel;

class MenuListModel extends Model
{
    use HasFactory;
    protected $table = 'health_menu_list';
    protected $primaryKey = 'menu_list_id';
    protected $fillable = [
        'parentid',
        'title',
        'url',
        'position',
        'categories_id',
        'blank',
        'menu_list_status',
        'is_app',
        'layout',
        'sort',
        'created_user',
        'updated_user'
    ];

    protected $casts = [
        'categories_id' => 'integer',
    ];

    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategoriesModel::class, 'categories_id', 'categories_id');
    }
}
