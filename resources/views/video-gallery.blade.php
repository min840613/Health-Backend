<!DOCTYPE html>
<html>
<head>
    <title>Health2.0影片庫</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- References: https://github.com/fancyapps/fancyBox -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>


    <style type="text/css">
    .gallery
    {
        display: inline-block;
        margin-top: 20px;
    }
    .close-icon{
        border-radius: 50%;
        position: absolute;
        right: 5px;
        top: -10px;
        padding: 5px 8px;
    }
    .form-image-upload{
        background: #e8e8e8 none repeat scroll 0 0;
        padding: 15px;
    }
    .reset{
        color: #28a745;
        border-color: #28a745;
    }
    .reset:hover{
        color: #fff;
        background-color: #28a745;
        border-color: #28a745;
    }
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        text-align: center;
        display: none;
    }
    .overlay-text{
        font-size: 2vw;
        color: white;
        top: 25vw;
        position: relative;
        background-color: #54beab;
    }

    .lds-hourglass:after {
      content: " ";
      display: inline-flex;
      border-radius: 50%;
      width: 0;
      height: 0;
      margin: 8px;
      box-sizing: border-box;
      border: 32px solid #fff;
      border-color: #fff transparent #fff transparent;
      animation: lds-hourglass 1.2s infinite;
      background-color:#54beab;
    }
    @keyframes lds-hourglass {
      0% {
        transform: rotate(0);
        animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
      }
      50% {
        transform: rotate(900deg);
        animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
      }
      100% {
        transform: rotate(1800deg);
      }
    }

    </style>
</head>
<body>


<div class="container">


    <h3>Health2.0影片庫</h3>
    <div class="overlay">
        <span class="overlay-text">影片上傳中<div class="lds-hourglass"></div></span>
        

    </div>
    <form action="{{ url('video-gallery') }}" class="form-image-upload" method="POST" enctype="multipart/form-data">


        {!! csrf_field() !!}


        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        @if ($message = Session::get('success'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
        </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <strong>選擇分類:</strong>
                <select name="album" id="select_album" class="form-control form-select form-select-lg mb-3" aria-label="albums">
                    <option value="0">所有影片</option>
                    @if($albums->count())
                        @foreach($albums as $album)
                    <option value="{{ $album->id }}" {{ ($album_id > 0 && $album_id == $album->id) ? "selected" : "" }}>{{ $album->title }}</option>
                        @endforeach

                    @endif
                </select>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-5">
                <strong>影片標題:</strong>
                <input type="text" name="title" class="form-control" placeholder="影片標題">
            </div>
            <div class="col-md-5">
                <strong>上傳:</strong>
                <input type="file" name="video" class="form-control">
            </div>
            <div class="col-md-2">
                <br/>
                <button type="submit" id="uploadBtn" class="btn btn-success">上傳</button>
            </div>
        </div>


    </form>
<hr>
    <form action="{{ url('video-gallery') }}" class="form-image-upload" method="GET" enctype="multipart/form-data">


        {!! csrf_field() !!}



        <div class="row">
            <div class="col-md-5">
                <strong>搜尋標題:</strong>
                <input type="text" name="search_title" id="search_title" value="{{ (!empty($search_title)) ? $search_title : '' }}" class="form-control" placeholder="標題">
            </div>
            <div class="col-md-2">
                <br/>
                <button type="submit" class="btn btn-success">查詢</button>
                <button type="submit" class="btn btn-outline-success reset">取消查詢</button>
            </div>

        </div>


    </form>

    <div class="row" style="text-align: center;">
        <div class='list-group gallery'>


            @if($videos->count())
                @foreach($videos as $video)
                <div class='col-sm-4 col-xs-6 col-md-6 col-lg-6' style="margin-bottom:20px;">
                    <div style="width: 100%; text-align: center;">

                        <!-- <img class="img-responsive" alt="" src="{{ config('constants.cdn.url') . $video->path . $video->video }}"/> -->

                        <div class='text-center'>
                            <div>
                                <p class='text-muted' style="background-color: #88add4;border-radius: 5px 5px 5px 5px;color:#000;" >{{ $video->title }}</p>
                            </div>
                        </div>
                        <video width="320" height="240" controls>
                            <source src="{{ config('constants.cdn.url') . $video->path . $video->video }}" type="video/mp4">
                        </video>
 <!-- text-center / end -->

                    </div>
                    <div class="text-center" style="margin-top: 10px;">
                        <a class="btn btn-danger copyVideoUrl" data-href="{{ config('constants.cdn.url') . $video->path . $video->video }}" href="javascript:;">複製連結</a>
                        <a class="btn btn-success chooseFile" data-href="{{ config('constants.cdn.url') . $video->path . $video->video }}" href="javascript:;">選擇</a>
                    </div>

                </div> <!-- col-6 / end -->
                @endforeach
            @endif


        </div> <!-- list-group / end -->
        {!! $videos->links() !!}
    </div> <!-- row / end -->
</div> <!-- container / end -->
</body>
<script type="text/javascript">
    $(document).ready(function(){
        $(".delete_btn").click(function(){
            if(confirm('是否確認要刪除?')){
                $(this).parent('form').submit();
            } else {
                return false;
            }
        })
        $(".fancybox").fancybox({
            openEffect: "none",
            closeEffect: "none"
        });

        $(".copyVideoUrl").click(function(){
            var text = $(this).attr('data-href');
            var flag = copyText(text);
            alert(flag ? "複製成功" : "複製失敗");
        })

        $(".chooseFile").click(function(){
            var videoUrl = $(this).attr('data-href');
            var insertCode = '<p><video controls="controls"><source src="' + videoUrl + '"></video></p>';

            if (window.location.search.indexOf('top=tinymce') > -1) {
                window.parent.tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, insertCode );
                // tinyMCEPopup.close();
                top.tinymce.activeEditor.windowManager.close();
            } else {
                var postData = {};
                postData.imgUrl = videoUrl;
                var qparent = window.parent.$('.VideoGalleryIframe').attr('data-parent');
                if (null !== qparent) {
                    postData.parent = qparent;
                }
                parent.postMessage(postData, "{{ url('"+window.parent.location.href+"') }}");
            }
        })

        function copyText(text) {
            var textarea = document.createElement("textarea");
            var currentFocus = document.activeElement;
            document.body.appendChild(textarea);
            textarea.value = text;
            textarea.focus();
            if (textarea.setSelectionRange)
                textarea.setSelectionRange(0, textarea.value.length);
            else
                textarea.select();
            try {
                var flag = document.execCommand("copy");
            } catch(eo){
                var flag = false;
            }
            document.body.removeChild(textarea);
            currentFocus.focus();
            return flag;
        }

        // $('#select_album').on('change', function() {
        //     var url = '/image-gallery';
        //     if (this.value > 0) {
        //         url = url + '?album=' + this.value;
        //     }
        //     location.href = url;
        // });

        $('#select_album').on('change', function() {
            var url = '/video-gallery';
            var querystr = window.location.search;

            // from tinymce
            if (querystr.indexOf('top=tinymce') > -1) {
                url = url + '?top=tinymce';
                if (this.value > 0) {
                    url = url + '&album=' + this.value;
                }
            } else {
                if (this.value > 0) {
                    url = url + '?album=' + this.value;
                }
            }
            location.href = url;
        });


    });

    $('.reset').click(function () {
        $("#search_title").val('');
    })

    $(".form-image-upload").submit(function(e){
        $(".overlay").show();
    })
</script>
</html>
