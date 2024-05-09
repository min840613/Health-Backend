@extends('adminlte::page')

@section('title', '文章總覽')

@section('content_header')
    <h2>文章總覽 - 全文瀏覽</h2>
@stop

@section('content')
    <table class="table table-bordered">
        <tbody>
        <tr>
            <td class="col-2">主類別</td>
            <td>
                @foreach($article->mainCategories as $mainCategory)
                    <div>{{$mainCategory->name}}</div>
                @endforeach
            </td>
        </tr>
        <tr>
            <td class="col-2">子類別</td>
            <td>
                @foreach($article->subCategories as $subCategory)
                    <div>{{$subCategory->name}}</div>
                @endforeach
            </td>
        </tr>
        <tr>
            <td>發佈時間</td>
            <td>{{$article->publish}}</td>
        </tr>
        <tr>
            <td>文章標題</td>
            <td>{{$article->title}}</td>
        </tr>
        <tr>
            <td>上稿者名稱</td>
            <td>{{$article->authorModel->name ?? null}} {{ $article->author_type == '1' ? '報導' : '整理' }}</td>
        </tr>
        <tr>
            <td>主圖 URL</td>
            <td><img src="{{$article->image}}" width="300px"/></td>
        </tr>
        <tr>
            <td>主影音</td>
            <td>
                @if(!empty($article->video_id))
                    <div>https://www.youtube.com/watch?v={{$article->video_id}}</div>
                    <iframe src="https://www.youtube.com/embed/{{$article->video_id}}?enablejsapi=1" frameborder="0"
                            data-tag="{{$article->tag}}" data-video-id="{{$article->articles_id}}"
                            data-title="{{$article->title}}" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                @endif
            </td>
        </tr>
        <tr>
            <td>TAG</td>
            <td>
                @foreach(optional($article->tags)->pluck('tag') as $tag)
                    <div class="badge badge-pill badge-success">{{$tag}}</div>
                @endforeach
            </td>
        </tr>
        <tr>
            <td>內容</td>
            <td>{!!$article->article_content!!}</td>
        </tr>
        <tr>
            <td>延伸閱讀</td>
            <td>
                @foreach($article->recommendations as $recommendation)
                    <div>
                        <a href="{{route('articles.show', ['article'=> $recommendation['articles_id']])}}">{{$recommendation['articles_id']}} {{$recommendation['title']}}</a>
                    </div>
                @endforeach
            </td>
        </tr>
        <tr>
            <td>狀態</td>
            <td>{{$article->articles_status === 1 ? '發布' : '下架'}}</td>
        </tr>
        <tr>
            <td>LINE 文章供稿</td>
            <td>{{$article->is_line_article ? '是' : '否'}}</td>
        </tr>
        <tr>
            <td>LINE 影音供稿</td>
            <td>{{$article->is_line_rss ? '是' : '否'}}</td>
        </tr>
        @if($article->is_line_rss)
            <tr>
                <td>LINE 供稿影片選取</td>
                <td>
                    <div>
                        @if(strpos($article->video_file_name,'static.tvbs.com.tw') == false)
                        {{\Storage::disk('s3_old')->url('video/health/'.$article->video_file_name)}}
                        @else
                        {{$article->video_file_name}}
                        @endif
                    </div>
                    <video controls width="300px" height="150px">
                        @if(strpos($article->video_file_name,'static.tvbs.com.tw') == false)
                        <source src="{{\Storage::disk('s3_old')->url('video/health/'.$article->video_file_name)}}"/>
                        @else
                        <source src="{{$article->video_file_name}}"/>
                        @endif
                    </video>
                </td>
            </tr>
        @endif
        <tr>
            <td>MixerBox 供稿</td>
            <td>{{$article->is_mixerbox_article ? '是' : '否'}}</td>
        </tr>
        <tr>
            <td>YAHOO 供稿</td>
            <td>{{$article->is_yahoo_rss ? '是' : '否'}}</td>
        </tr>
        @if($article->is_yahoo_rss)
            <tr>
                <td>YAHOO供稿 延伸閱讀</td>
                <td>
                    @foreach($article->yahooRecommendations as $recommendation)
                        <div>
                            <a href="{{route('articles.show', ['article'=> $recommendation['articles_id']])}}">{{$recommendation['articles_id']}} {{$recommendation['title']}}</a>
                        </div>
                    @endforeach
                </td>
            </tr>
        @endif
        </tbody>
    </table>
@stop

@section('css')
    <style>
        td {
            vertical-align: middle !important;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function () {
        })
    </script>
@stop

