@extends('adminlte::page')

@section('title', '內容置頂 － 編輯')

@section('content_header')
    <h1>內容置頂{{empty($rightBar) ? '' : ('：'.$rightBar->name)}} － 編輯</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            @if ($message = Session::get('error'))
                <div class="alert alert-danger">
                    <p class="m-0">{{ $message }}</p>
                </div>
            @endif
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {!! Form::open(['url' => route('detail.update', ['right_bar' => $rightBar->id, 'detail' => $rightBar->detail[0]->id]),'method'=>'PUT']) !!}
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required" name="article_id_label">文章ID</label>
                    <div class="col-sm-10">
                        <input type="hidden" name='master_type' value="{{$rightBar->article_require_master}}">
                        <input type="hidden" name='main_category' value="{{$rightBar->main_category}}">
                        <input type="hidden" name='sub_category' value="{{$rightBar->sub_category}}">
                        <x-adminlte-input name="article_id" value="{{$rightBar->detail[0]->article_id}}" required/>
                        <a href="#" class="btn btn-info" id="searchArticlesBtn" style="pointer-events: none;">正在加載搜尋文章...</a>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required" name="name_label">頭條標題</label>
                    <div class="col-sm-10">
                        <x-adminlte-input name="name" value="{{$rightBar->detail[0]->name}}" required/>
                        <div id="charCount"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">發布時間</label>
                    @php
                        $StartDateConfig = ['format' => 'YYYY-MM-DD HH:mm'];
                    @endphp
                    <x-adminlte-input-date name="published_at" value="{{empty($rightBar->detail[0]->published_at) ? date('Y-m-d H:i') : date('Y-m-d H:i',strtotime($rightBar->detail[0]->published_at))}}" :config="$StartDateConfig" required>
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-gradient-info">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input-date>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">下架時間</label>
                    @php
                        $EndDateConfig = ['format' => 'YYYY-MM-DD HH:mm'];
                    @endphp
                    <x-adminlte-input-date name="published_end" value="{{empty($rightBar->detail[0]->published_end) ? date('Y-m-d 23:59',strtotime('+1 week')) : date('Y-m-d H:i',strtotime($rightBar->detail[0]->published_end))}}" :config="$EndDateConfig" required>
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-gradient-info">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input-date>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">狀態</label>
                    <div class="col-sm-10">
                        <x-adminlte-select name="status">
                            <option value="1" {{$rightBar->detail[0]->status == "1" ? 'selected' : ''}}>發佈</option>
                            <option value="0" {{$rightBar->detail[0]->status == "0" ? 'selected' : ''}}>下架</option>
                        </x-adminlte-select>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-4">儲存</button>
                <input type="button" name="back" class="btn btn-outline-info" value="返回" onClick="historylist()">
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    @include('components.searchArticlesModal')
@stop

@section('css')
    <style>
        .column-required::after {
            content: ' *';
            color: red;
        }

        #charCount {
            position: absolute;
            right: 15px;
            /* top: calc(50% - 0.5em); */
            top: calc(66%);
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function () {
            charCount($('#name').val().length);

            $('#name').on('input', function() {
                charCount($(this).val().length);
            });

            $('.modal-dialog').width('70%');
            $('.modal-dialog').css('max-width', '80%');

            let searchArticleTitle = '文章：'+'{{ $rightBar->mainCategory->name }}';

            if('{{$subCategoryName}}' != ''){
                searchArticleTitle += ' － {{ $subCategoryName }}';
            }

            $('#searchArticlesModalTitle').html(searchArticleTitle);

            let iframeSrc = '{{ route("articles.search") }}';
            iframeSrc += '?main_category={{$rightBar->main_category}}';
            iframeSrc += '&sub_category={{$rightBar->sub_category}}';
            iframeSrc += '&master={{$rightBar->article_require_master}}';

            $('#searchArticlesIframe').attr('src', iframeSrc);

            $("#searchArticlesIframe").on("load", function(){
                $('#searchArticlesBtn').html('搜尋文章');
                $('#searchArticlesBtn').css('pointer-events', 'auto');
            });


            $('#searchArticlesBtn').on('click', function(){
                var parentId = $(this).prev('div').children('div').children('input').attr('id');
                var parentTitle = $(this).parent().parent().next('div').children('div').children('div').children('div').children('input').attr('id');
                console.log(parentId);
                console.log(parentTitle);
                $('#searchArticlesModal iframe').attr('data-parent', parentId );
                $('#searchArticlesModal iframe').attr('data-parentTitle', parentTitle );

                $('#searchArticlesModal').modal('show');
            });

            window.addEventListener("message", (e) => {
                $("#" + e.data.parent).val(e.data.articleId);
                $("#" + e.data.parentTitle).val(e.data.articleTitle);
                charCount(e.data.articleTitle.length);
                $('#searchArticlesModal').modal('hide');
            });
        })

        function charCount(countNum){
            $('#charCount').text(countNum + '/26');

            if(countNum > 26){
                $('#charCount').addClass('text-danger');
            }else{
                $('#charCount').removeClass('text-danger');
            }
        }

        function historylist() {
            if (history.length > 1) {
                history.back();
            } else {
                window.close();
            }
        }
    </script>
@stop
