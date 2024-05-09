<?php

namespace App\Observers;

use App\Models\ImageAlbum;
use App\Models\ImageGallery;

class ImageAlbumObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the ImageAlbum "created" event.
     *
     * @param  \App\Models\ImageAlbum  $imageAlbum
     * @return void
     */
    public function created(ImageAlbum $imageAlbum)
    {
        //
    }

    /**
     * Handle the ImageAlbum "updated" event.
     *
     * @param  \App\Models\ImageAlbum  $imageAlbum
     * @return void
     */
    public function updated(ImageAlbum $imageAlbum)
    {
        //
    }

    /**
     * Handle the ImageAlbum "deleted" event.
     *
     * @param  \App\Models\ImageAlbum  $imageAlbum
     * @return void
     */
    public function deleted(ImageAlbum $imageAlbum)
    {

        \DB::table('image_gallery')->where('album', $imageAlbum->id)->update(['album' => 0]);
    }

    /**
     * Handle the ImageAlbum "restored" event.
     *
     * @param  \App\Models\ImageAlbum  $imageAlbum
     * @return void
     */
    public function restored(ImageAlbum $imageAlbum)
    {
        //
    }

    /**
     * Handle the ImageAlbum "force deleted" event.
     *
     * @param  \App\Models\ImageAlbum  $imageAlbum
     * @return void
     */
    public function forceDeleted(ImageAlbum $imageAlbum)
    {
        //
    }
}
