@extends('adminlte::page')

@section('title', '首頁右側管理 - 編輯')

@section('content_header')
    <h1>首頁右側管理 - 編輯</h1>
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
            {!! Form::open(['url' => route('right_bar.update', ['right_bar' => $result->id]),'method'=>'PUT']) !!}
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required" name="name_label">版位名稱</label>
                    <div class="col-sm-10">
                        <x-adminlte-input name="name" value="{{$result->name}}" required/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">對應主分類</label>
                    <div class="col-sm-10">
                        <x-adminlte-select name="main_category" required>
                            <option value="0">----請選取----</option>
                            @foreach($categories as $val)
                                <option value={{$val->categories_id}} {{$val->categories_id == $result->main_category ? 'selected' : ''}}>{{$val->name}}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">對應子分類</label>
                    <div class="col-sm-10">
                        <x-adminlte-select name="sub_category">
                            @if($categories[$result->main_category]->subCategories->isEmpty())
                                <option value="0" selected>----無子分類----</option>
                            @else
                                <option value="0" {{$result->main_category == '0' ? 'selected' : ''}}>----全部----</option>
                                @foreach($categories[$result->main_category]->subCategories as $val)
                                    <option value={{$val->sub_categories_id}} {{$val->sub_categories_id == $result->sub_category ? 'selected' : ''}}>{{$val->name}}</option>
                                @endforeach
                            @endif
                        </x-adminlte-select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">文章需含區塊</label>
                    <div class="col-sm-10">
                        <x-adminlte-select name="article_require_master">
                            <option value="0" {{$result->article_require_master == '0' ? 'selected' : ''}}>無需包含</option>
                            <option value="1" {{$result->article_require_master == '1' ? 'selected' : ''}}>名醫</option>
                            <option value="2" {{$result->article_require_master == '2' ? 'selected' : ''}}>食譜達人</option>
                        </x-adminlte-select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">狀態</label>
                    <div class="col-sm-10">
                        <x-adminlte-select name="status">
                            <option value="1" {{$result->status == '1' ? 'selected' : ''}}>發佈</option>
                            <option value="0" {{$result->status == '0' ? 'selected' : ''}}>下架</option>
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

            if('{{$categories[$result->main_category]->subCategories->isEmpty()}}'){
                $('select[name=sub_category]').attr('disabled', true);
            }

            $('select[name=main_category]').on('change', function () {

                $('select[name=sub_category]').attr('disabled', true);
                let ajaxUrl = "{{ route('sub_categories.by-main', ':id') }}";
                ajaxUrl = ajaxUrl.replace(':id', $(this).val());
                $.get(ajaxUrl, function (data) {
                    $('select[name=sub_category]').html('<option value="0" selected>----全部----</option>');

                    $.each(data,function(index, child){
                        $('select[name=sub_category]').append('<option value="'+child.sub_categories_id+'">'+child.name+'</option>')
                    })

                    if(data.length !== 0){
                        $('select[name=sub_category]').attr('disabled', false);
                    }else{
                        $('select[name=sub_category]').html('<option value="0" selected>----無子分類----</option>');
                        $('select[name=sub_category]').attr('disabled', true);
                    }
                })
            })
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
