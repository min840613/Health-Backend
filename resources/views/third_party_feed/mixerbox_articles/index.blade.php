@extends('components.index')

@section('title')
    MixerBox 文章供稿
@stop

@section('content_header')
    @php
        $startConfig = [
            "singleDatePicker" => true,
            "showDropdowns" => true,
            "startDate" => request()->input('search_release_start') ?? "js:moment()",
            "minYear" => 2000,
            "maxYear" => "js:parseInt(moment().format('YYYY'),10)+5",
            "cancelButtonClasses" => "btn-danger",
            "locale" => ["format" => "YYYY-MM-DD"],
        ];
        $endConfig = [
            "singleDatePicker" => true,
            "showDropdowns" => true,
            "startDate" => request()->input('search_release_end') ?? "js:moment()",
            "minYear" => 2000,
            "maxYear" => "js:parseInt(moment().format('YYYY'),10)+5",
            "cancelButtonClasses" => "btn-danger",
            "locale" => ["format" => "YYYY-MM-DD"],
        ];
    @endphp

    <h1>MixerBox 文章供稿</h1>
    {{ Form::open(['id' => 'search', 'route' => 'mixerbox_articles.list', 'method' => 'get', 'style' => 'border:1px solid #ccc; padding: 5px;']) }}
    <div class="row col-10" style="padding-top: 10px">
        <div class="col-6">
            <label>分類</label>
            <x-adminlte-select2 name="search_category_id" igroup-size="sm">
                <option value="0">全部</option>
                @foreach($categories as $v)
                    <option
                        value="{{$v['category_id']}}" {{request()->input('search_category_id') == $v['category_id'] ? 'selected' : ''}}>{{$v['category_name']}}</option>
                @endforeach
            </x-adminlte-select2>
        </div>
    </div>
    <div class="row col-10">
        <div class="col-6">
            <label>供稿日期</label>
            @php
                $releaseMappings = ['no' => '無配對日期', 'yes' => '有配對日期'];
            @endphp
            <x-adminlte-select2 name="search_release_date" igroup-size="sm">
                <option value="no" {{request()->input('search_release_date') === 'no' ? 'selected' : ''}}>{{$releaseMappings['no']}}</option>
                <option value="yes" {{request()->input('search_release_date') === 'yes' ? 'selected' : ''}}>{{$releaseMappings['yes']}}</option>
            </x-adminlte-select2>
        </div>
        <div class="row col-10 search-release-date {{request()->input('search_release_date') === 'yes' ? '' : 'd-none'}}">
            <div class="col-3">
                <label>供稿日期（起）</label>
                <x-adminlte-date-range name="search_release_start" igroup-size="sm" :config="$startConfig">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-date-range>
            </div>
            <div class="col-3">
                <label>供稿日期（迄）</label>
                <x-adminlte-date-range name="search_release_end" igroup-size="sm" :config="$endConfig">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-date-range>
            </div>
        </div>
    </div>
    <div class="row col-10">
        <div class="col-6">
            <label>排序</label>
            @php
                $orderMappings = ['desc' => '加入時間新到舊', 'asc' => '加入時間舊到新'];
            @endphp
            <x-adminlte-select2 name="search_order" igroup-size="sm">
                <option value="desc" {{request()->input('search_order') === 'desc' ? 'selected' : ''}}>{{$orderMappings['desc']}}</option>
                <option value="asc" {{request()->input('search_order') === 'asc' ? 'selected' : ''}}>{{$orderMappings['asc']}}</option>
            </x-adminlte-select2>
        </div>
    </div>

    <div class="row col-6">
        <div class="col-2">
            <x-adminlte-button label="查詢" type="submit" theme="success" class="btn-sm"/>
        </div>
        <div class="col-4">
            <x-adminlte-button label="取消查詢" theme="outline-success" class="btn-sm reset"/>
        </div>
    </div>
    {{ Form::close() }}
@stop

@section('create')
    @can($role_name.'-edit')
        <a href="javascript: changeStatus();" class="btn btn-sm btn-success">供稿</a>
        <a href="javascript: deleteLine();" class="btn btn-sm btn-danger">刪除</a>
    @endcan
@stop

@section('headerOthers')
@stop

@section('data_list')
    <form id="mixerbox_articles">
    @foreach ($datas as $k => $v)
        <tr>
            @if($cond['search_release_date'] == 'no')
                <td class="align-middle">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="article_id"
                            data-value = "{{$v->id}}"
                            value="{{$v->article_id}}">
                        <label class="form-check-label" for="flexCheckDefault"></label>
                    </div>
                </td>
            @endif
            <td class="align-middle">{{$v->article_id}}</td>
            <td class="align-middle" width="350px">{{$v->article->title}}</td>
            <td class="align-middle">{{$v->article->mainCategory->name}}</td>
            @if($cond['search_release_date'] == 'yes')
                <td class="align-middle">{{$v->toArray()['release_date']}}</td>
            @else
                <td class="align-middle">{{$v->article->publish}}</td>
            @endif
            <td class="align-middle">{{$v->created_at}}</td>
            <td class="align-middle">{!!$v->statusCss!!}</td>
        </tr>
    @endforeach
    </form>
@stop

@section('modal')

@stop

@section('css')
@stop

@section('js')
    <script>
        $(document).ready(function () {
            // 全選
            $('.select-all').click(function () {
                $('input[name="article_id"]').each(function (i) {
                    $(this).click()
                });
            })

            // 取消查詢
            $('.reset').click(function () {
                location.href = "{{route('mixerbox_articles.list')}}"
            })

            $('select[name=search_release_date]').on( "change", function() {
                if($(this).val() == 'yes'){
                    $('.search-release-date').removeClass('d-none');
                }else{
                    $('.search-release-date').addClass('d-none');
                }
            });
        })

        function changeStatus(){

            let mixerboxFormData = new FormData();

            let Id = new Array();

            $('input[name="article_id"]').each(function (i) {
                if($(this).is(':checked')){
                    Id.push($(this).attr('data-value'));
                }
            });

            if(Id.length == 0){
                alert('請先勾選欲供稿之文章！');
                return false;
            }

            mixerboxFormData.append('Id', Id);

            mixerboxFormData.append('_method', 'PATCH');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });

            $.ajax({
            data: mixerboxFormData,
            url: '{{route("mixerbox_articles.changeStatus")}}',
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            processData: false,
            contentType: false,
            success: function (data) {
                console.log(data);
                Swal.fire({
                    title: '儲存成功',
                    icon: 'success',
                    confirmButtonText: `確定`,
                    confirmButtonColor: '#f87e6c',
                }).then((result) => {
                    window.location.reload();
                })
            },
            error: function (err) {
                console.log(err);

                if(err.status == 400 && err.responseJSON.errMsg != undefined){
                    Swal.fire({
                        title: err.responseJSON.errMsg,
                        icon: 'error',
                        confirmButtonText: `確定`,
                        confirmButtonColor: '#f87e6c',
                    })
                }else{
                    Swal.fire({
                        title: '儲存失敗',
                        icon: 'error',
                        confirmButtonText: `確定`,
                        confirmButtonColor: '#f87e6c',
                    })
                }
            }});
        }

        function deleteLine(){

            let mixerboxFormData = new FormData();

            let Id = new Array();
            let ArticlesId = new Array();

            $('input[name="article_id"]').each(function (i) {
                if($(this).is(':checked')){
                    Id.push($(this).attr('data-value'));
                    ArticlesId.push($(this).val());
                }
            });

            if(Id.length == 0 || ArticlesId.length == 0){
                alert('請先勾選欲刪除之文章！');
                return false;
            }

            mixerboxFormData.append('Id', Id);
            mixerboxFormData.append('ArticlesId', ArticlesId);

            mixerboxFormData.append('_method', 'DELETE');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });

            $.ajax({
            data: mixerboxFormData,
            url: '{{route("mixerbox_articles.delete")}}',
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            processData: false,
            contentType: false,
            success: function (data) {
                console.log(data);
                Swal.fire({
                    title: '刪除成功',
                    icon: 'success',
                    confirmButtonText: `確定`,
                    confirmButtonColor: '#f87e6c',
                }).then((result) => {
                    window.location.reload();
                })
            },
            error: function (err) {
                console.log(err);

                if(err.status == 400 && err.responseJSON.errMsg != undefined){
                    Swal.fire({
                        title: err.responseJSON.errMsg,
                        icon: 'error',
                        confirmButtonText: `確定`,
                        confirmButtonColor: '#f87e6c',
                    })
                }else{
                    Swal.fire({
                        title: '刪除失敗',
                        icon: 'error',
                        confirmButtonText: `確定`,
                        confirmButtonColor: '#f87e6c',
                    })
                }
            }});
        }
    </script>
@stop

