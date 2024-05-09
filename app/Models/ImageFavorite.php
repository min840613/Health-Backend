<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageFavorite extends Model
{
    use HasFactory;

    protected $table = 'image_favorite';
    
    protected $guarded = [];
}
