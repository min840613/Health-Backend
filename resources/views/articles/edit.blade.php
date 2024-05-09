@extends('adminlte::page')

@section('title', '文章總覽')

@section('content_header')
    <h1>文章總覽 - 編輯</h1>
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
        {!! Form::model($ArticleData, ['method' => 'PATCH','route' => ['articles.update', $ArticleData->articles_id], 'id'=>'formSubmit']) !!}
            <div class="card-body">
                @foreach($field as $k=>$v)
                    @switch($v['type'])
                        @case('category_selectize')
                            @foreach($ArticleData->mainCategories as $key=>$value)
                            @if($value['subCategoriesID'])
                            @foreach($value['subCategoriesID'] as $kk=>$vv)
                            <div class="form-group row CategoriesArea">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    @if($key == 0 && $kk == 0)
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                    @endif
                                </label>
                                <div class="col-sm-10 row">
                                    <div class="col-sm-3">
                                        <select class="form-control {{$k}}" name="{{$k.'[]'}}">
                                            <option value="">請選擇</option>
                                            @foreach($CategoriesDataList as $cate_key=>$cate_value)
                                            <option value="{{$cate_key}}" {{$value['categories_id']==$cate_key?'selected':''}}>{{$cate_value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <select class="form-control subcategories" name="subcategories[]">
                                            <option value="">請選擇</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        @if($key == 0 && $kk == 0)
                                        <a class="CategoryClickPlus" href="javascript:;">
                                            <span class="fa fa-solid fa-plus"></span>
                                        </a>
                                        @else
                                        <a class="CategoryClickMinus" href="javascript:;">
                                            <span class="fa fa-solid fa-minus"></span>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="form-group row CategoriesArea">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    @if($key == 0)
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                    @endif
                                </label>
                                <div class="col-sm-10 row">
                                    <div class="col-sm-3">
                                        <select class="form-control {{$k}}" name="{{$k.'[]'}}">
                                            <option value="">請選擇</option>
                                            @foreach($CategoriesDataList as $cate_key=>$cate_value)
                                            <option value="{{$cate_key}}" {{$value['categories_id']==$cate_key?'selected':''}}>{{$cate_value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <select class="form-control subcategories" name="subcategories[]">
                                            <option value="">請選擇</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        @if($key == 0)
                                        <a class="CategoryClickPlus" href="javascript:;">
                                            <span class="fa fa-solid fa-plus"></span>
                                        </a>
                                        @else
                                        <a class="CategoryClickMinus" href="javascript:;">
                                            <span class="fa fa-solid fa-minus"></span>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endforeach
                            @break
                        @case('medicine_selectize')
                            <div id="medicine_article_sickness" class="form-group row" style="display: none;">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label">部位</label>
                                        {!!
                                            Form::select(NULL, $HealthBodyList, null,array(
                                                'placeholder'   => "請選擇",
                                                'class'         => 'form-control '.$k,
                                                'id'            => 'medicine_article_sickness_One'
                                            ))
                                        !!}
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="col-form-label">器官與組織</label>
                                        ：
                                        {!!
                                            Form::select(NULL, [], null,array(
                                                'placeholder'   => "請選擇",
                                                'class'         => 'form-control',
                                                'id'            => 'medicine_article_sickness_Two'
                                            ))
                                        !!}
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="col-form-label">疾病：</label>
                                        {!!
                                            Form::select(NULL, [], null,array(
                                                'placeholder'   => "請選擇",
                                                'class'         => 'form-control',
                                                'id'            => 'medicine_article_sickness_Three'
                                            ))
                                        !!}
                                    </div>
                                    <div class="col-sm-3 align-middle d-flex align-items-end">
                                        <a id="medicine_article_sickness_join" class="btn btn-success btn-flat float-left px-4" href="javascript:;">加入</a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row" style="display: none;">
                                <label class="col-sm-2 col-form-label" for=""></label>
                                <div class="col-sm-10">
                                    {!!
                                        Form::text('medicine_article_sickness_id', implode(',',$SicknessSelectedList), array(
                                            'id'            => 'medicine_article_sickness_id',
                                            'style'         => 'display: none;'
                                        ))
                                    !!}
                                    <div id="medicine_article_sickness_list"></div>
                                </div>
                            </div>
                            @break;
                        @case('datetime')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{ $v['title'] }}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10" datas="{{ old('publish') ? old('publish') : ($v['value'] ? $v['value'] : date('Y-m-d H:i')) }} ">
                                    <x-adminlte-date-range name="{{$k}}" id="{{$k}}">
                                        <x-slot name="appendSlot">
                                            <div class="input-group-text bg-dark">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                        </x-slot>
                                    </x-adminlte-date-range>
                                </div>
                            </div>
                            @break
                        @case('text')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10">
                                    {!!
                                        Form::text($k, $v['value'], array(
                                            'placeholder' => $v['placeholder'],
                                            'class' => 'form-control',
                                            'id' => $k
                                        ))
                                    !!}
                                </div>
                            </div>
                            @break
                        @case('image')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-7">
                                    {!!
                                        Form::text($k, $v['value'][0], array(
                                            'placeholder'   => $v['placeholder'],
                                            'class'         => 'form-control',
                                            'id'            => $k,
                                            'readonly'      => 'readonly'
                                        ))
                                    !!}
                                    {!!
                                        Form::text($k.'_alt', $v['value'][1], array(
                                            'placeholder' => $k.'圖說',
                                            'class' => 'form-control',
                                            'style' => 'display: none',
                                            'id' => $k.'_alt'
                                        ))
                                    !!}
                                </div>
                                <div class="col-sm-3">
                                    <a class="btn btn-success btn-flat float-left px-4 gallery_image" href="javascript:;">圖庫</a>
                                    <a class="btn btn-success btn-flat float-left px-4 ml-4 review_image_click" href="javascript:;">預覽</a>
                                </div>
                                <div class="col-sm-12 mt-4 review_image_div" style="display: none;">
                                    <div class="row">
                                        <label class="col-sm-2"></label>
                                        <div class="col-sm-10">
                                            <img class="review_image" style="width: 100%" src="" />
                                        </div>
                                    </div>
                                </div>
                                @if($v['comment'])
                                <div class="col-sm-2">
                                </div>
                                <div class="col-sm-10">
                                    <span style="color: red">備註：{{$v['comment']}}</span>
                                </div>
                                @endif
                            </div>
                            @break
                        @case('textarea')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10">
                                    {!!
                                        Form::textarea($k, $v['value'], array(
                                            'placeholder' => $v['placeholder'],
                                            'class' => 'form-control' ,
                                            'id' => $k,
                                            'rows' => '10',
                                            'cols' => '30'
                                        ))
                                    !!}
                                </div>
                            </div>
                            @break
                        @case('select')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10">
                                    {!!
                                        Form::select($k, $v['options'], $v['value'],array(
                                            'class' => 'form-control' ,
                                            'id' => $k
                                        ))
                                    !!}
                                </div>
                            </div>
                            @break;
                        @case('select_collaborator')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-4">
                                    <select class="form-control" name="collaborator">
                                        <option value=""></option>
                                        <option value="e1" {{$v['value'] == 'e1' ? 'selected' : ''}}>e1:AIWIZE</option>
                                    </select>
                                </div>
                            </div>
                            @break;
                        @case('medicine_category_select')
                            <div id="medicine_article_categories" class="form-group row" style="display: none;">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10">
                                    {!!
                                        Form::select($k, $v['options'], $v['value'],array(
                                            'class' => 'form-control' ,
                                            'id' => $k
                                        ))
                                    !!}
                                </div>
                            </div>
                            @break;
                        @case('selectize_author')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10 row">
                                    <div class="col-sm-3">
                                        <select class="form-control" name="{{$k}}">
                                            <option value="">請選擇</option>
                                            @foreach($AuthorsDataList as $key=>$value)

                                                @if ( old('author') && old('author') == $key )
                                                    <option value="{{$key}}" selected >{{$value}}</option>
                                                @elseif ( !old('author') && $v['value'][0] == $key )
                                                    <option value="{{$key}}" selected >{{$value}}</option>
                                                @else
                                                    <option value="{{$key}}" >{{$value}}</option>
                                                @endif

                                            @endforeach;
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <select class="form-control" name="author_type">
                                            <option value="" >請選擇</option>

                                                @if ( old('author_type') && old('author_type') == 1 )
                                                    <option value="1" selected>報導</option>
                                                @elseif ( !old('author') && $v['value'][1] == 1 )
                                                    <option value="1" selected>報導</option>
                                                @else
                                                    <option value="1">報導</option>
                                                @endif

                                                @if ( old('author_type') && old('author_type') == 2 )
                                                    <option value="2" selected>整理</option>
                                                @elseif ( !old('author') && $v['value'][1] == 2 )
                                                    <option value="2" selected>整理</option>
                                                @else
                                                    <option value="2">整理</option>
                                                @endif

                                        </select>
                                    </div>
                                    <div class="col-sm-6"></div>
                                </div>
                            </div>
                            @break
                        @case('radio')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10">
                                    <div class="form-check form-check-inline"">
                                        {!!
                                            Form::radio($k, '1', ($v['value']==1?'checked':''),array(
                                                'class' => 'form-check-input',
                                                'id'   =>  $k.'_1'
                                            ))
                                        !!}
                                        {!!
                                            Form::label($k.'_1', '是',array(
                                                'class' =>  'form-check-label',
                                                'for'   =>  'label_1'
                                            ))
                                        !!}
                                    </div>
                                    <div class="form-check form-check-inline"">
                                        {!!
                                            Form::radio($k, '0', ($v['value']==0?'checked':''),array(
                                                'class' => 'form-check-input',
                                                'id'   =>  $k.'_0'
                                            ))
                                        !!}
                                        {!!
                                            Form::label($k.'_0', '否', array(
                                                'class' =>  'form-check-label'
                                            ))
                                        !!}
                                    </div>
                                </div>
                            </div>
                            @break
                        @case('extended_text')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10 row">
                                    <div class="col-sm-8">
                                        {!!
                                            Form::text(null, null, array(
                                                'placeholder' => $v['placeholder'],
                                                'class' => 'form-control',
                                                'id' => $k
                                            ))
                                        !!}
                                    </div>
                                    <div class="col-sm-4">
                                        <a class="btn btn-success btn-flat float-left px-4 searchEmbed" href="javascript:;">搜尋文章</a>
                                        <a class="btn btn-success btn-flat float-left px-4 ml-4 embed" href="javascript:;">嵌入</a>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-4 review_embed_div" style="display: none;">
                                    <div class="row">
                                        <label class="col-sm-2"></label>
                                        <div class="col-sm-10">

                                        </div>
                                    </div>
                                </div>
                                <div id="CreateEmbedData" class="d-none"></div>
                            </div>
                            @break
                        @case('further_text')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10 row">
                                    <div class="col-sm-8">
                                        <button id="further_loading" class="btn btn-success btn-flat float-left px-4" style="display: none;" type="button" disabled>
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            Loading...
                                        </button>
                                        <a class="btn btn-success btn-flat float-left px-4 searchFurther" href="javascript:;">產生延伸閱讀</a>
                                        {!!
                                            Form::text($k, $v['value'], array(
                                                'placeholder' => $v['placeholder'],
                                                'class' => 'form-control d-none',
                                                'id' => $k
                                            ))
                                        !!}
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-4 review_further_div" style="display: none;">
                                    <div class="row">
                                        <label class="col-sm-2"></label>
                                        <div class="col-sm-10">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            @break
                        @case('video')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-7">
                                    {!!
                                        Form::text($k, $v['value'], array(
                                            'placeholder'   => $v['placeholder'],
                                            'class'         => 'form-control',
                                            'id'            => $k,
                                            'readonly'      => 'readonly'
                                        ))
                                    !!}
                                </div>
                                <div class="col-sm-3">
                                    <a class="btn btn-success btn-flat float-left px-4 video_gallery" href="javascript:;">影片庫</a>
                                    <a class="btn btn-success btn-flat float-left px-4 ml-2 clearVideo" href="javascript:;">清除</a>
                                </div>
                            </div>
                            @break;
                        @case('line_article_select')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10">
                                    {!!
                                        Form::select($k, $v['options'], $v['value'][0],array(
                                            'class' => 'form-control' ,
                                            'id' => $k
                                        ))
                                    !!}
                                </div>
                            </div>
                            @break
                        @case('line_select')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10">
                                    {!!
                                        Form::select($k, $v['options'], $v['value'][0],array(
                                            'class' => 'form-control' ,
                                            'id' => $k
                                        ))
                                    !!}
                                </div>
                            </div>
                            <div class="form-group row line_video" style="display: none;">
                                <label class="col-sm-2 col-form-label" for="line_video_gallery"></label>
                                <div class="col-sm-7">
                                    {!!
                                        Form::text('video_file_name', $v['value'][1], array(
                                            'placeholder'   => '請選擇影片',
                                            'class'         => 'form-control',
                                            'id'            => 'line_video_gallery',
                                            'readonly'      => 'readonly'
                                        ))
                                    !!}
                                </div>
                                <div class="col-sm-3">
                                    <a class="btn btn-success btn-flat float-left px-4 video_gallery" href="javascript:;">影片庫</a>
                                    <a class="btn btn-success btn-flat float-left px-4 ml-2 clearVideo" href="javascript:;">清除</a>
                                </div>
                            </div>
                            @break
                        @case('mixerbox_article_select')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10">
                                    {!!
                                        Form::select($k, $v['options'], $v['value'][0],array(
                                            'class' => 'form-control' ,
                                            'id' => $k
                                        ))
                                    !!}
                                </div>
                            </div>
                            @break
                        @case('yahoo_select')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10">
                                    {!!
                                        Form::select($k, $v['options'], $v['value'], array(
                                            'class' => 'form-control' ,
                                            'id'    => $k
                                        ))
                                    !!}
                                </div>
                            </div>
                            @break
                        @case('yahoo_ext_select')
                            <div class="form-group row {{ $k }}" style="display: none">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-8">
                                    {!!
                                        Form::select(null, $v['options'], null, array(
                                            'class' => 'form-control' ,
                                            'id'    => $k
                                        ))
                                    !!}
                                    {!!
                                        Form::text($k.'_data', implode(',',$v['value']),array(
                                            'class' => 'form-control d-none' ,
                                            'id'    => $k.'_data'
                                        ))
                                    !!}
                                </div>
                                <div class="col-sm-2">
                                    <a class="btn btn-success btn-flat float-left px-4 insert_yahoo_ext" href="javascript:;">插入</a>
                                </div>
                            </div>
                            <div class="form-group row" style="display: none;">
                                <label class="col-sm-2 col-form-label" for=""></label>
                                <div id="yahoo_ext_list" class="col-sm-10">

                                </div>
                            </div>
                            @break;
                        @case('end_select')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-8">
                                    {!!
                                        Form::select(null, $v['options'], null,array(
                                            'class' => 'form-control' ,
                                            'id' => $k
                                        ))
                                    !!}
                                </div>
                                <div class="col-sm-2">
                                    <a class="btn btn-success btn-flat float-left px-4 insertEndOfText" href="javascript:;">插入</a>
                                </div>
                            </div>
                            @break
                        @case('selectize')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-8">
                                    {!!
                                        Form::text($k, $v['value'], array(
                                            'id'            => $k,
                                            'placeholder'   => $v['placeholder'],
                                        ))
                                    !!}
                                </div>
                                <div class="col-sm-2">
                                    <a id="ai_tag" class="btn btn-success btn-flat float-left px-4" href="javascript:;" onclick="tag_recommend()">產生標籤</a>
                                </div>
                            </div>
                            <div class="form-group row" style="display: none;">
                                <label class="col-sm-2 col-form-label" for=""></label>
                                <div id="tag_link" class="col-sm-10">

                                </div>
                            </div>
                            @break
                        @case('selectize_text')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="{{ $k }}">
                                    {{$v['title']}}
                                    @if($v['required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10">
                                    <input id="{{$k}}" name="{{$k}}" placeholder="{{$v['placeholder']}}" value="{{$v['value']}}">
                                </div>
                            </div>
                            @break
                        @case('master_select')
                            @include('articles.components.master_select')
                        @break
                    @endswitch
                @endforeach
            </div>
            <div class="card-footer">
                <button id="sendSubmit" type="button" class="btn btn-primary btn-flat mr-4">儲存</button>
                <input type="button" name="back" class="btn btn-outline-info" value="返回" onClick="historylist()">
            </div>
        {!! Form::close() !!}
   </div>
</div>
<div id="CategoriesModel" class="d-none">
    <div class="form-group row CategoriesArea">
        <label class="col-sm-2 col-form-label"> </label>
        <div class="col-sm-10 row">
            <div class="col-sm-3">
                {!!
                    Form::select('categories[]', $CategoriesDataList, null, array(
                        'placeholder'   => "請選擇",
                        'class'         => 'form-control categories',
                    ))
                !!}
            </div>
            <div class="col-sm-3">
                {!!
                    Form::select('subcategories[]', [], null, array(
                        'placeholder'   => "請選擇",
                        'class'         => 'form-control subcategories',
                    ))
                !!}
            </div>
            <div class="col-sm-6">
                <a class="CategoryClickMinus" href="javascript:;">
                    <span class="fa fa-solid fa-minus"></span>
                </a>
            </div>
        </div>
    </div>
</div>

@include('components.gallery_modal')
@include('components.video_modal')
@include('components.embed_modal')
@stop

@section('css')
   <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    @include('components.tinymce')
    @include('components.articles_js')
    @include('articles.components.master_select_js')
    <!-- autocomplete plugin -->
    <link rel="stylesheet" href="{{ url('css/selectize.css')}}" referrerpolicy="no-referrer"/>
    <script src="{{ url('js/selectize.min.js')}}" referrerpolicy="no-referrer"></script>
    <!-- autocomplete plugin end -->
    <script>
        $().ready(function(){
            var publishDate = $('input[name="publish"]').parent('div').parent('div').parent('div').attr('datas');
            $('input[name="publish"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                startDate: publishDate,
                timePicker: true,
                timePicker24Hour: true,
                cancelButtonClasses: "btn-danger",
                locale: {
                    format: 'YYYY-MM-DD HH:mm'
                }
            });

            $("#sendSubmit").click(function(){
                if($('#is_line_article').val() == 0 && '{{$ArticleData["is_line_article"]}}' == '1'){
                    if (!confirm("此篇文章已進入文章供稿排程\n選擇\"確定\"會將LINE供稿列表移除，若不移除請選擇\"取消\"")){
                        return false;
                    }
                }
                if($('#is_mixerbox_article').val() == 0 && '{{$ArticleData["is_mixerbox_article"]}}' == '1'){
                    if (!confirm("此篇文章已進入文章供稿排程\n選擇\"確定\"會將MixerBox供稿列表移除，若不移除請選擇\"取消\"")){
                        return false;
                    }
                }
                $("#formSubmit").submit();
            })

            // 看更多
            $(".searchEmbed").on("click",function(){
                $('#embedModal').modal('show');
            })

            $(".searchArticle").on("click",function(){
                var keywords = $("#ModalKeywords").val();
                var nowjson = $("#NowJson").val();
                if(keywords){
                    var url = '{{ url("admin/articles_manage/articles/content_extend_reading/".$id) }}' + '?keywords=' + keywords;
                    ajaxContentExtendReadingPageUrl(url, nowjson);
                } else {
                    alert("請輸入關鍵字");
                }
            })

            $("#extended_reading").on("change",function(){
                ajaxChangeExtendedReading($(this).val());
            })

            $(".embed").on("click",function(){
                var TinyMCEData = $("#CreateEmbedData").html();

                var editor = tinymce.get('article_content');
                editor.insertContent(TinyMCEData);

                $(".review_embed_div").children('div').children('div').html('');
                $(".review_embed_div").hide();
                $("#CreateEmbedData").html('');
                $("#CreateEmbedData").hide();
                $("#extended_reading").val('');
                $("#NowJson").attr('value','');
            })
            // 看更多 End

            // 醫學百科疾病
            ajaxLoadArticleSickness();
            if($("#medicine_article_sickness_One").val()){
                ajaxHealthOrgans($("#medicine_article_sickness_One").val());
            }
            $("#medicine_article_sickness_One").change(function(){
                $("#medicine_article_sickness_Two").html('<option value="">請選擇</option>');
                $("#medicine_article_sickness_Three").html('<option value="">請選擇</option>');
                ajaxHealthOrgans($(this).val());
            })
            $("#medicine_article_sickness_Two").change(function(){
                $("#medicine_article_sickness_Three").html('<option value="">請選擇</option>');
                ajaxHealthSickness($(this).val());
            })
            $("#medicine_article_sickness_join").click(function(){
                var sickness_id = $("#medicine_article_sickness_Three").val();
                if(sickness_id){
                    ajaxAddArticleSickness(sickness_id);
                }
            })
            $(".medicine_article_sickness_remove").click(function(){
                var sickness_id = $(this).attr('ids');
                if(sickness_id){
                    ajaxRemoveArticleSickness(sickness_id);
                }
            })
            // 醫學百科 End
            // Yahoo 供稿
            getArticlesForYahoo();
            if($("#is_yahoo_rss").val() == '1'){
                // 取得主分類ID
                var categories = $(".categories").serializeArray().filter(function(item) {
                    return item.name === 'categories[]';
                }).map(function(item) {
                    return item.value;
                }).filter(function(val) {
                    return val !== "" && val !== null && val !== undefined;
                });
                getCategoryArticles(categories, '{{$id}}');

                $(".yahoo_ext").show();
            } else {
                $(".yahoo_ext").hide();
                $("#yahoo_ext_list").parent('div').hide();
            }
            $("#is_yahoo_rss").on('change',function(){
                // 取得主分類ID
                var categories = $(".categories").serializeArray().filter(function(item) {
                    return item.name === 'categories[]';
                }).map(function(item) {
                    return item.value;
                }).filter(function(val) {
                    return val !== "" && val !== null && val !== undefined;
                });
                if(categories.length == 0){
                    $(this).val(0);
                    alert('請選擇分類');
                    return false;
                } else if($("#articles_status").val() != 1) {
                    $(this).val(0);
                    alert('請設定為上架');
                    return false;
                }
                getCategoryArticles(categories, '{{$id}}');

                if($(this).val() == '1'){
                    if($("input[name=adult_flag]:checked").val() == 1){
                        $(this).val(0);
                        alert('YAHOO供稿：18禁文章不可供稿！');
                        return false;
                    }
                    $(".yahoo_ext").show();
                    $("#yahoo_ext_list").parent('div').show();
                } else {
                    $(".yahoo_ext").hide();
                    $("#yahoo_ext_list").parent('div').hide();
                }
            })

            $(".insert_yahoo_ext").click(function(){
                var ext_data = $("#yahoo_ext_data").val();
                var ext_data_arr = ext_data.split(",");
                if($("#yahoo_ext").val()){
                    if(ext_data_arr.length < 3){
                        if(!ext_data_arr.includes($("#yahoo_ext").val())){
                            ext_data_arr.push($("#yahoo_ext").val());
                        }
                        ext_data_arr = ext_data_arr.filter(function(val) {
                            return val !== "" && val !== null && val !== undefined;
                        });
                        ext_data = ext_data_arr.join(',');
                        $("#yahoo_ext_data").attr('value',ext_data);

                        getArticlesForYahoo();
                    } else {
                        alert('yahoo供稿不可超過3則');
                    }
                }
            })
            // Yahoo 供稿 End
            // tag
            var tagHandler = function (name) {
                return function () {
                    switch (name) {
                        case 'onInitialize':
                            break;
                        case 'onItemRemove':
                            break;
                        case 'onItemAdd':
                            var array_value = $('#tag')[0].selectize.items.join(',').split(',');
                            $.each(array_value, function (i, v) {
                                if (v.length > 30) {
                                    alert('Tag ' + v + ' 大於三十個字');
                                    return false;
                                }
                            });
                            break;
                        case 'onChange':
                            var article_keywords = $('#tag').val();
                            break;
                        case 'onFocus':
                            $('#tag').val($('#tag')[0].selectize.items.join(','));
                            break;
                        default:
                            return false;
                            break;
                    }
                };
            };
            $('#tag').selectize({
                create: true,
                onChange: tagHandler('onChange'),
                onItemAdd: tagHandler('onItemAdd'),
                onItemRemove: tagHandler('onItemRemove'),
                onBlur: tagHandler('onBlur'),
                onInitialize: tagHandler('onInitialize'),
                delimiter: ',',
                persist: false
            });

            $('body').on('click','.googleKeyword',function(event){
                event.preventDefault();
                var selectizeA = $('#tag')[0].selectize;
                selectizeA.addOption({
                        text: $(this).val(),
                        value: $(this).val()
                    });
                selectizeA.addItem($(this).val());
                $(this).remove();
            })
            // tag end
            // 字串搜尋
            var tagHandler = function (name) {
                return function () {
                    switch (name) {
                        case 'onInitialize':
                            break;
                        case 'onItemRemove':
                            break;
                        case 'onItemAdd':
                            var array_value = $('#match_searchs')[0].selectize.items.join(',').split(',');
                            $.each(array_value, function (i, v) {
                                if (v.length > 30) {
                                    alert('字串搜尋 ' + v + ' 大於三十個字');
                                    return false;
                                }
                            });
                            break;
                        case 'onChange':
                            var article_keywords = $('#match_searchs').val();
                            break;
                        case 'onFocus':
                            $('#match_searchs').val($('#match_searchs')[0].selectize.items.join(','));
                            break;
                        default:
                            return false;
                            break;
                    }
                };
            };
            $('#match_searchs').selectize({
                create: true,
                onChange: tagHandler('onChange'),
                onItemAdd: tagHandler('onItemAdd'),
                onItemRemove: tagHandler('onItemRemove'),
                onBlur: tagHandler('onBlur'),
                onInitialize: tagHandler('onInitialize'),
                delimiter: ',',
                persist: false
            });
            // 字串搜尋 end
            // 文末廣宣
            $(".insertEndOfText").on("click",function(){
                var TinyMCEData = '<span class="endtext">' + $("#end_of_text").val() + '</span>';
                var editor = tinymce.get('article_content');
                editor.insertContent(TinyMCEData);
            })
            // 文末廣宣 End
            // 供搞
            if($('#is_line_rss').val() == 1){
                $(".line_video").show();
            }

            $("#is_line_article, #is_line_rss, #is_mixerbox_article").on('change', function(){
                // 取得主分類id array
                var categories = $(".categories").serializeArray().filter(function(item) {
                    return item.name === 'categories[]';
                }).map(function(item) {
                    return item.value;
                }).filter(function(val) {
                    return val !== "" && val !== null && val !== undefined;
                });

                if(categories.length === 0){
                    $(this).val(0);
                    alert('請選擇分類');
                    return false;
                } else if($("#articles_status").val() != 1) {
                    $(this).val(0);
                    alert('請設定為上架');
                    return false;
                }

                if($(this).val() == 1){
                    if($("input[name=adult_flag]:checked").val() == 1){
                        $(this).val(0);
                        if($(this).attr('id') == 'is_mixerbox_article'){
                            alert('MixerBox供稿：18禁文章不可供稿！');
                        }else{
                            alert('LINE供稿：18禁文章不可供稿！');
                        }
                        return false;
                    }

                    if($(this).attr('id') == 'is_line_article'){
                        $("#line_video_gallery").val('');
                        $(".line_video").hide();
                        $('#is_line_rss').val(0);
                        var LineArticleCondition = @json($LineArticleCondition);
                        if(LineArticleCondition.hasOwnProperty(categories[0]) !== true){
                            $(this).val(0);
                            alert('LINE文章供稿：只接受主分類為 ' + Object.values(LineArticleCondition) + ' ！');
                            return false;
                        }
                    }

                    if($(this).attr('id') == 'is_line_rss'){
                        $('#is_line_article').val(0);
                        $(".line_video").show();
                    }

                    if($(this).attr('id') == 'is_mixerbox_article'){
                        var MixerboxArticleCondition = @json($MixerboxArticleCondition);
                        if(MixerboxArticleCondition.hasOwnProperty(categories[0]) !== true){
                            $(this).val(0);
                            alert('MixerBox供稿：只接受主分類為 ' + Object.values(MixerboxArticleCondition) + ' ！');
                            return false;
                        }
                    }

                }else{
                    if($(this).attr('id') == 'is_line_rss'){
                        $("#line_video_gallery").val('');
                        $(".line_video").hide();
                    }
                }
            })
            // 供搞 End

            // 延伸閱讀
            if($("#further_reading").val()){
                ajaxGetFurtherLoading($("#further_reading").val());
            }
            $(".searchFurther").on("click",function(){
                var categories = $(".categories").serializeArray().filter(function(item) {
                    return item.name === 'categories[]';
                }).map(function(item) {
                    return item.value;
                }).filter(function(val) {
                    return val !== "" && val !== null && val !== undefined;
                });

                var tinymce_instance = tinymce.get('article_content');
                var content = tinymce_instance.getContent();
                var form_data = [
                    {'name':'article_content','value':content}
                ];
                ajaxSearchFurther(form_data, '{{$id}}', categories);
            })
            // 延伸閱讀 End

            // 清除影音
            $(".clearVideo").on("click",function(){
                $(this).parent('div').parent('div').children('div').eq(0).children('input').val('');
            })
            // 清除影音 End

            $('textarea#article_content').tinymce({
                'token': $('form input[name="_token"]').val()
            })
            $(".review_image_click").click(function(){
                var image = $(this).parent('div').prev('div').children('input');
                var review_image_area = $(this).parent('div').next('div');
                var review_image = $(this).parent('div').next('div').children('div').children('div').children('img.review_image');
                if(image.val()){
                    review_image.attr("src",image.val());
                    review_image_area.show();
                }
            })

            $('.gallery_image').on('click', function() {
                var parentId = $(this).parent('div').prev('div').children('input').attr('id');
                $('#galleryModal iframe').attr('data-parent', parentId );
                $('#galleryModal').modal('show');
            })

            $('.video_gallery').on('click', function() {
                var parentId = $(this).parent('div').prev('div').children('input').attr('id');
                $('#VideoGalleryModal iframe').attr('data-parent', parentId );
                $('#VideoGalleryModal').modal('show');
            })
            <?php
                $subCategoriesArr = [];
            foreach ($ArticleData->mainCategories as $k => $v) :
                if ($v['subCategoriesID']) :
                    foreach ($v['subCategoriesID'] as $vv) :
                        $subCategoriesArr[] = $vv;
                    endforeach;
                else :
                        $subCategoriesArr[] = null;
                endif;
            endforeach;
            ?>
            var subCategoriesData = '{{implode(",",$subCategoriesArr)}}';
            const subCategoriesArr = subCategoriesData.split(',');
            // 分類管理
            $.each($(".categories"),function(key, value){
                if($(this).val()){
                    var label = $(this).parent('div').parent('div').parent('div');
                    ajaxChangeCategories(label, $(this).val(), subCategoriesArr[key]);
                    initMedicine();
                }
            })
            $(".CategoryClickMinus").unbind('click');
            $(".CategoryClickMinus").on("click",function(){
                $(this).parent("div").parent("div").parent("div.CategoriesArea").remove();
                initMedicine();
            })

            $(".CategoryClickPlus").on("click",function(){
                if($(".CategoriesArea").length <= 3){
                    var CategoriesModel = $("#CategoriesModel").html();
                    var CategoriesLastKey = 0;
                    $(".CategoriesArea").each(function(e){
                        CategoriesLastKey = e;
                    })
                    CategoriesLastKey = CategoriesLastKey - 1;
                    $(".CategoriesArea").eq(CategoriesLastKey).after(CategoriesModel);

                    $(".CategoryClickMinus").unbind('click');
                    $(".CategoryClickMinus").on("click",function(){
                        $(this).parent("div").parent("div").parent("div.CategoriesArea").remove();
                        initMedicine();
                    })

                    $(".categories").unbind('change');
                    $(".categories").on("change",function(){
                        var label = $(this).parent('div').parent('div').parent('div');
                        ajaxChangeCategories(label, $(this).val());
                        initMedicine();
                    })
                } else {
                    alert('分類最多僅能增加 3 個');
                }
            })
            $(".categories").on("change",function(){
                var label = $(this).parent('div').parent('div').parent('div');
                ajaxChangeCategories(label, $(this).val());
                initMedicine();
            })
            // 分類管理 End

            window.addEventListener("message", (e) => {
                if(e.origin !== 'tvbs.com.tw' || e.origin !== 'test.health-backstage.com') {
                    $("#" + e.data.parent).val(e.data.imgUrl);
                    $("#" + e.data.parent + "_alt").val(e.data.imgAlt);
                    $(".gallery-modal-header > .close").trigger('click');
                    $(".video-modal-header > .close").trigger('click');
                    $("#review_image_div").hide();
                }
            });
        })
   </script>
@stop
