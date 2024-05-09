<!DOCTYPE html>
<html>
<head>
    <title>文章</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- References: https://github.com/fancyapps/fancyBox -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen"> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script> --}}


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
    #searchArticle_title{
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        overflow:hidden;
        -webkit-box-orient: vertical;
    }
    </style>
</head>
<body>


<div class="container">
    {{-- <h3>Health2.0圖庫</h3> --}}
    <div class="row" style="text-align: center;">
        <div class='list-group gallery'>
            @if($results->count())
                @foreach($results as $result)
                <div class='col-sm-4 col-xs-6 col-md-3 col-lg-3' style="margin-bottom:20px;">
                    <div style="width: 100%; text-align: center; height: 200px; overflow: hidden;">
                        <img class="img-responsive" alt="" src="{{ $result->image }}"/>
                        <div id='searchArticle_title' class='text-center'>
                            <small class='text-muted'>{{ $result->title }}</small>
                        </div>
                    </div>
                    <div class="text-center" style="margin-top: 10px;">
                        <a class="btn btn-danger copyArticleId" data-href="{{ $result->articles_id }}" href="javascript:;">複製文章ID</a>
                        <a class="btn btn-success chooseArticle" data-title ="{{$result->title}}" data-href="{{ $result->articles_id }}" href="javascript:;">選擇</a>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
        {!! $results->render() !!}
    </div> <!-- row / end -->
</div> <!-- container / end -->
</body>
<script type="text/javascript">
    $(document).ready(function(){

        $(".copyArticleId").click(function(){
            var text = $(this).attr('data-href');
            var flag = copyText(text);
            alert(flag ? "複製成功" : "複製失敗");
        })

        $(".chooseArticle").click(function(){
            var postData = {};
            postData.articleId = $(this).attr('data-href');
            postData.articleTitle = $(this).attr('data-title');

            var qparent = window.parent.$('.searchArticles_iframe').attr('data-parent');
            if (null !== qparent) {
                postData.parent = qparent;
            }
            var qtitle = window.parent.$('.searchArticles_iframe').attr('data-parentTitle');
            if (null !== qtitle) {
                postData.parentTitle = qtitle;
            }

            parent.postMessage(postData, "{{ url('"+window.parent.location.href+"') }}");

            // var articleId = $(this).attr('data-href');
            // var insertCode = '<p><img src="' + articleId + '"></p>'
            // parent.postMessage(articleId, "{{ url('"+window.parent.location.href+"') }}");
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


    });
</script>
</html>
