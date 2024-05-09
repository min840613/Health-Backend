<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoGallery extends Model
{
    use HasFactory;

    protected $table = 'video_gallery';

    protected $guarded = [];

    public function albumRelation()
    {
        return $this->hasOne(VideoAlbum::class, 'id', 'album');
    }
}
