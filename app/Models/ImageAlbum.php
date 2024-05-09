<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageAlbum extends Model
{
    use HasFactory;

    protected $table = 'image_album';
    
    protected $guarded = [];
}
