<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImageGallery;
use App\Models\ImageAlbum;
use App\Models\ImageFavorite;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ImageGalleryController extends Controller
{
    /**
     * Listing Of images gallery
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


    	$items = ImageGallery::latest();
        if (null !== $request->query('album')) {
            $images = $items->where('album', $request->query('album'));
            $album_id = $request->query('album');
        } else {
            $images = $items;
            $album_id = 0;
        }

        $search_title = $request->query('search_title');
        $images = $images->when(!empty($search_title),function($query) use ($search_title) { 
                $query->where('title', 'like', "%{$search_title}%");
        })->paginate(8);

        $albums = ImageAlbum::get();

    	return view('image-gallery',compact('images', 'albums', 'album_id', 'search_title'));
    }

    public function favorite()
    {
        $collection = ImageFavorite::latest()->get();

        $favorites = $collection->map(function($favorite){
            return [
                'title' => $favorite->title,
                'value' => $favorite->url
            ];
        });

        return response()->json($favorites);
    }

    /**
     * Upload image function
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
    	$this->validate($request, [
    		'title' => 'max:256',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // $input['image'] = time() . '.' . $request->image->getClientOriginalExtension();

        $oriName = $request->image->getClientOriginalName();
        $input['ori_filename'] = $oriName;
        $filePath = config('constants.s3.path') . date('Y') . '/' . date('m') . '/';
        $input['path'] = $filePath;
        $fileName = date('YmdHis') . '-' . Str::random(8) . '.' . $request->image->getClientOriginalExtension();
        $input['image'] = $fileName;
        $input['album'] = $request->album;
        $s3Path = $filePath . $fileName;
        $path = Storage::disk('s3')->put($s3Path, file_get_contents($request->image));
        $path = Storage::disk('s3')->url($path);

        // $request->image->move(public_path('images'), $input['image']);

        $input['title'] = isset($request->title) ? $request->title : pathinfo($oriName, PATHINFO_FILENAME);
        ImageGallery::create($input);


    	return back()
    		->with('success','圖片上傳成功.');
    }

    /**
     * Upload image by Tinymce
     *
     * @return \Illuminate\Http\Response
     */
    public function tinymceupload(Request $request)
    {
        if ($request->isMethod('options')) {
            header("Access-Control-Allow-Methods: POST, OPTIONS");
            return;

        }

        reset($_FILES);
        $temp = current($_FILES);
        if (is_uploaded_file($temp['tmp_name'])) {
            // if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
            //     header("HTTP/1.1 400 Invalid file name.");
            //     return;
            // }
            if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "jpeg", "png", "webp"))) {
                header("HTTP/1.1 400 Invalid extension.");
                return;
            }

            $imageFolder = "images/";
            $tempName = $temp['name'];
            $filetowrite = $imageFolder . $temp['name'];
            move_uploaded_file($temp['tmp_name'], $filetowrite);

            $input['ori_filename'] = $tempName;
            $filePath = config('constants.s3.path') . date('Y') . '/' . date('m') . '/';
            $input['path'] = $filePath;
            $fileName = date('YmdHis') . '-' . Str::random(8) . '.' . strtolower(pathinfo($tempName, PATHINFO_EXTENSION));
            $input['image'] = $fileName;
            $s3Path = $filePath . $fileName;

            $path = Storage::disk('s3')->put($s3Path, fopen('images/' . $tempName, 'r+'));
            $path = Storage::disk('s3')->url($path);
            File::delete($imageFolder . $tempName);
            $input['title'] = pathinfo($tempName, PATHINFO_FILENAME);
            ImageGallery::create($input);

            return response()->json(['location' => config('constants.cdn.url') . $filePath . $fileName], 201);

        } else {
            return response()->json(['error' => 'server error'], 500);
        }
    }

    /**
     * Remove Image function
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    	$data = ImageGallery::find($id);
        if($data):
            // 取得路徑
            $path = $data['path'].$data['image'];

            // 刪除S3上面之檔案
            if(Storage::disk('s3')->exists($path)):
                Storage::disk('s3')->delete($path);
            endif;

            // 刪除資料
            $data->delete();
            return back()
                ->with('success','檔案已刪除');
        else:
            return back()
                ->withErrors('msg','查無檔案');
        endif;
    }
}
