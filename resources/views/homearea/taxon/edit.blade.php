@extends('adminlte::page')

@section('title', '首頁分類單元 - 編輯')

@section('content_header')
    <h1>首頁分類單元 - 編輯</h1>
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
            {!! Form::open(['url' => route('home_taxon.update', ['home_taxon' => $result->id]),'method'=>'PUT']) !!}
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required" name="name_label">名稱</label>
                    <div class="col-sm-10">
                        <x-adminlte-input name="name" value="{{$result->name}}" required/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">分類</label>
                    <div class="col-sm-10">
                        <x-adminlte-select name="categories_id" required>
                            <option value=0>----請選取----</option>
                            @foreach($main_categories as $val)
                                <option value={{$val->categories_id}} {{($val->categories_id == $result->categories_id ? 'selected' : '')}}>{{$val->name}}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label" name="article_id_label">置頂文章ID</label>
                    <div class="col-sm-10">
                        <x-adminlte-input name="article_id" value="{{$result->article_id}}"/>
                        <a href="#" class="btn btn-info" id="searchArticlesBtn" style="pointer-events: none;">正在加載搜尋文章...</a>
                    </div>
                </div>
                <div class="form-group row publish_area" style='display: {{empty($result->article_id) ? 'none' : 'flex'}};'>
                    <label class="col-sm-2 col-form-label" name="article_id_label">置頂文章上架時間</label>
                    @php
                        $StartDateConfig = ['format' => 'YYYY-MM-DD HH:mm'];
                    @endphp
                    <x-adminlte-input-date name="published_at" value="{{empty($result->published_at) ? date('Y-m-d H:i') : date('Y-m-d H:i',strtotime($result->published_at))}}" :config="$StartDateConfig">
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-gradient-info">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input-date>
                </div>
                <div class="form-group row publish_area" style='display: {{empty($result->article_id) ? 'none' : 'flex'}};'>
                    <label class="col-sm-2 col-form-label">置頂文章下架時間</label>
                    @php
                        $EndDateConfig = ['format' => 'YYYY-MM-DD HH:mm'];
                    @endphp
                    <x-adminlte-input-date name="published_end" value="{{empty($result->published_end) ? date('Y-m-d 23:59',strtotime('+1 week')) : date('Y-m-d H:i',strtotime($result->published_end))}}" :config="$EndDateConfig">
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
                            <option value="1" {{$result->status == 1 ? 'selected' : ''}}>發佈</option>
                            <option value="0" {{$result->status == 0 ? 'selected' : ''}}>下架</option>
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
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function () {
            $('.modal-dialog').width('70%');
            $('.modal-dialog').css('max-width', '80%');

            $('input[name=article_id]').on('input', function(){
                if($(this).val()){
                    $('.publish_area').css('display', 'flex');
                }else{
                    $('.publish_area').css('display', 'none');
                }
            });

            $('#searchArticlesModalTitle').html('文章：'+$('select[name=categories_id]').find('option:selected').text());
            $('#searchArticlesIframe').attr('src', '{{ route("articles.search") }}'+'?main_category='+$('select[name=categories_id]').val());

            $('#searchArticlesBtn').on('click', function(){
                var parentId = $(this).prev('div').children('div').children('input').attr('id');
                console.log(parentId);
                $('#searchArticlesModal iframe').attr('data-parent', parentId );

                if($('select[name=categories_id]').val() == '0'){
                    Swal.fire({
                        title: '請先選取分類！',
                        icon: 'warning',
                        confirmButtonText: `確定`,
                        confirmButtonColor: '#f87e6c',
                    })
                }else{
                    $('#searchArticlesModal').modal('show');
                }
            });

            window.addEventListener("message", (e) => {
                $("#" + e.data.parent).val(e.data.articleId);
                $('.publish_area').css('display', 'flex');
                $('#searchArticlesModal').modal('hide');
            });

            $('select[name=categories_id]').on('change', function () {
                $('input[name=article_id]').val('');
                $('#searchArticlesBtn').html('正在加載搜尋文章...');
                $('#searchArticlesBtn').css('pointer-events', 'none');
                $('#searchArticlesModalTitle').html('文章：'+$(this).find('option:selected').text());
                $('#searchArticlesIframe').attr('src', '{{ route("articles.search") }}'+'?main_category='+$(this).val());
            })

            $("#searchArticlesIframe").on("load", function(){
                $('#searchArticlesBtn').html('搜尋文章');
                $('#searchArticlesBtn').css('pointer-events', 'auto');
            });
        })

        function historylist() {
            if (history.length > 1) {
                history.back();
            } else {
                window.close();
            }
        }
    </script>
@stop
