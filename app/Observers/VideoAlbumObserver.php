<?php

namespace App\Observers;

use App\Models\VideoAlbum;

class VideoAlbumObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;


    /**
     * Handle the ImageAlbum "deleted" event.
     *
     * @param  \App\Models\ImageAlbum  $imageAlbum
     * @return void
     */
    public function deleted(VideoAlbum $videoAlbum)
    {

        \DB::table('video_gallery')->where('album', $videoAlbum->id)->update(['album' => 0]);
    }

}
