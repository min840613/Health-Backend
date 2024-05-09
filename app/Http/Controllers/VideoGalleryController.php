<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VideoGallery;
use App\Models\VideoAlbum;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class VideoGalleryController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $items = VideoGallery::latest();
        if (null !== $request->query('album')) {
            $videos = $items->where('album', $request->query('album'));
            $album_id = $request->query('album');
        } else {
            $videos = $items;
            $album_id = 0;
        }

        $search_title = $request->query('search_title');
        $videos = $videos->when(!empty($search_title),function($query) use ($search_title) { 
                $query->where('title', 'like', "%{$search_title}%");
        })->paginate(8);

        $albums = VideoAlbum::get();

        return view('video-gallery',compact('videos', 'albums', 'album_id', 'search_title'));
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
            'video' => 'required|file|mimes:mp4,mov,ogg|max:1024000',
        ],[], ['title' => '影片標題', 'video' => '影片檔案']);

        ini_set('memory_limit', '1024M');

        $filePath = config('constants.s3.video_path') . date('Y') . '/' . date('m') . '/';
        $input['path'] = $filePath;
        $fileName = date('YmdHis') . '-' . Str::random(8) . '.' . $request->video->getClientOriginalExtension();
        $input['video'] = $fileName;
        $input['album'] = $request->album;
        $s3Path = $filePath . $fileName;
        $path = Storage::disk('s3')->put($s3Path, file_get_contents($request->video));
        $path = Storage::disk('s3')->url($path);

        // $request->image->move(public_path('images'), $input['image']);
        $oriName = $request->video->getClientOriginalName();

        $input['title'] = isset($request->title) ? $request->title : pathinfo($oriName, PATHINFO_FILENAME);
        VideoGallery::create($input);


        // 取得route name
        $url = url()->previous();
        $route = app('router')->getRoutes($url)->match(app('request')->create($url))->getName();

        // 從 "影片管理-新增" 過來的，就回到 "影片管理"
        if ($route == 'video.create') {
            return to_route('video.list');
        }
        
    	return back()
    		->with('success','影片上傳成功.');
    }

    /**
     * 影片管理
     * 
     * */
    public function list(Request $request)
    {
        $field = ['分類名稱', '影片標題', '影片內容', '編輯'];

        $input = request()->all();

        $role_name = 'video_list';

        $videoGalleryData = VideoGallery::with(['albumRelation'])
            ->when(!empty($input['album_id']) , function ($query) use ($input) {
                $query->where('album', $input['album_id']);
            })
            ->when(!empty($input['search_title']) , function ($query) use ($input) {
                $query->where('title', 'like', "%{$input['search_title']}%");
            })
            ->orderByDesc('id')->paginate(5)->through(function ($data) {
                $data->album_title = '';
                if (!is_null($data->albumRelation)) {
                    $data->album_title = $data->albumRelation->title;
                }
                return $data;
            });

        $videoAlbumData = VideoAlbum::get();


        $editField = [
            [
                'title' => '標題名稱',
                'type' => 'text',
                'name' => 'title',
                'placeholder' => '',
                'required' => false,
                'id' => 'title'
            ],
            [
                'title' => '分類',
                'type' => 'custom',
                'name' => 'album_id',
                'placeholder' => '',
                'required' => false,
                'id' => 'album_id',
                'custom' => 'video.custom_select',
                'option' => $videoAlbumData,
            ],
        ];


        return view('video.list', compact('field', 'videoGalleryData', 'role_name', 'videoAlbumData', 'editField'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $field = [
            'album' =>  [
                'type'          =>  'album_select',
                'title'         =>  '影片分類',
                'placeholder'   => '分類',
                'required'      =>  false,
                'comment'       =>  null,
                'options'       =>  VideoAlbum::get()
            ],
            'title'  =>  [
                'type'          =>  'text',
                'title'         =>  '影片標題',
                'placeholder'   =>  '影片標題',
                'required'      =>  false,
                'comment'       =>  null,
            ],
            'video' =>  [
                'type'          =>  'video',
                'title'         =>  '影片檔案',
                'placeholder'         =>  '影片檔案',
                'required'      =>  true,
                'comment'       =>  null,
            ],

        ];
        $role_name = 'video_list';
        return view('video.create', compact('field', 'role_name'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result =  VideoGallery::find($id);
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'max:256',
        ],[], ['title' => '影片標題']);

        $videoGalleryData = VideoGallery::find($id);
        $input = [];

        $input['title'] = $request->input('title');
        $input['album'] = $request->input('album_id');

        $videoGalleryData->update($input);

        return response()->json($request->input(), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = VideoGallery::find($id);
        if ($data) {
            // 取得路徑
            $path = $data['path'] . $data['video'];

            // 刪除S3上面之檔案
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }

            // 刪除資料
            $data->delete();
            return back()
                ->with('success','檔案已刪除');
        } else {
            return back()
                ->withErrors('msg','查無檔案');
        }
    }
}
