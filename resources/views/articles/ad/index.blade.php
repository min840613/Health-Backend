@extends('components.index')

@section('title')
    文章總覽
@stop

@section('content_header')
    <h1>文章總覽</h1>
    {{ Form::open(['route' => 'articles_ad.index', 'method' => 'get', 'style' => 'border:1px solid #ccc; padding: 5px;']) }}
    @php
        $startConfig = [
            "singleDatePicker" => true,
            "showDropdowns" => true,
            "startDate" => request()->input('search_publish_start') ?? "js:moment()",
            "minYear" => 2000,
            "maxYear" => "js:parseInt(moment().format('YYYY'),10)+5",
            "cancelButtonClasses" => "btn-danger",
            "locale" => ["format" => "YYYY-MM-DD"],
        ];
        $endConfig = [
            "singleDatePicker" => true,
            "showDropdowns" => true,
            "startDate" => request()->input('search_publish_end') ?? "js:moment()",
            "minYear" => 2000,
            "maxYear" => "js:parseInt(moment().format('YYYY'),10)+5",
            "cancelButtonClasses" => "btn-danger",
            "locale" => ["format" => "YYYY-MM-DD"],
        ];
    @endphp
    <div class="row col-10" style="padding-top: 10px">
        <div class="col-2">
            <label>發佈區間（起）</label>
            <x-adminlte-date-range name="search_publish_start" igroup-size="sm" :config="$startConfig">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-date-range>
        </div>
        <div class="col-2">
            <label>發佈區間（迄）</label>
            <x-adminlte-date-range name="search_publish_end" igroup-size="sm" :config="$endConfig">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-date-range>
        </div>
        <div class="col-1">
            <label>&nbsp;</label>
        </div>
        <div class="col-2">
            <label>主分類</label>
            <x-adminlte-select2 name="search_main_category_id" igroup-size="sm">
                <option value="-1">全部</option>
                @foreach($filters['main_categories'] as $mainCategory)
                    <option
                        value="{{$mainCategory['categories_id']}}" {{request()->input('search_main_category_id') == $mainCategory['categories_id'] ? 'selected' : ''}}>{{$mainCategory['name']}}</option>
                @endforeach
            </x-adminlte-select2>
        </div>
        <div class="col-2">
            <label>子分類</label>
            <x-adminlte-select2 name="search_sub_category_id" igroup-size="sm">
                <option value="-1">全部</option>
            </x-adminlte-select2>
        </div>
    </div>

    <div class="row col-10">
        <div class="col-2">
            <label>發佈狀態</label>
            @php
                $statusMappings = [-1 => '全部', 0 => '下架', 1 => '上架'];
            @endphp
            <x-adminlte-select2 name="search_articles_status" igroup-size="sm">
                @foreach($filters['publish_status'] as $status)
                    <option
                        value="{{$status}}" {{request()->input('search_articles_status') === (string)$status ? 'selected' : ''}}>{{$statusMappings[$status]}}</option>
                @endforeach
            </x-adminlte-select2>
        </div>
        <div class="col-2">
            <label>上稿者</label>
            <x-adminlte-select2 name="search_author" igroup-size="sm">
                <option value="-1">全部</option>
                @foreach($filters['authors'] as $author)
                    <option
                        value="{{$author['id']}}" {{request()->input('search_author') == $author['id'] ? 'selected' : ''}}>{{$author['name']}}</option>
                @endforeach
            </x-adminlte-select2>
        </div>
        <div class="col-1">
            <label>&nbsp;</label>
        </div>
        <div class="col-2">
            <label>文章ID</label>
            <x-adminlte-input name="search_articles_id" igroup-size="sm"
                              value="{{request()->input('search_articles_id')}}"/>
        </div>
        <div class="col-2">
            <label>關鍵字</label>
            <x-adminlte-input name="search_keyword" igroup-size="sm" value="{{request()->input('search_keyword')}}"/>
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
    <a href="{{route('articles_ad.create')}}" class="btn btn-sm btn-success">新增</a>
@stop

@section('headerOthers')
{{--    <div class="row col-12">--}}
{{--        <div class="col-1">--}}
{{--            <a href="{{route('articles_ad.create')}}" class="btn btn-sm btn-success">新增</a>--}}
{{--        </div>--}}
{{--        <div class="col-1">--}}
{{--            <label>搜尋主/子類別</label>--}}
{{--        </div>--}}
{{--        <div class="col-1">--}}
{{--            <x-adminlte-select2 name="list_main_category_id" igroup-size="sm">--}}
{{--                <option value="-1">全部</option>--}}
{{--                @foreach($filters['main_categories'] as $mainCategory)--}}
{{--                    <option--}}
{{--                        value="{{$mainCategory['categories_id']}}" {{request()->input('list_main_category_id') == $mainCategory['categories_id'] ? 'selected' : ''}}>{{$mainCategory['name']}}</option>--}}
{{--                @endforeach--}}
{{--            </x-adminlte-select2>--}}
{{--        </div>--}}
{{--        <div class="col-1">--}}
{{--            <x-adminlte-select2 name="list_sub_category_id" igroup-size="sm">--}}
{{--                <option value="-1">全部</option>--}}
{{--            </x-adminlte-select2>--}}
{{--        </div>--}}
{{--        <div class="col-2">--}}
{{--            <x-adminlte-button label="批次加入多則於此類別" theme="info" class="btn-sm quick_append_categories"/>--}}
{{--        </div>--}}
{{--    </div>--}}
@stop

@section('data_list')
    @foreach ($datas as $key => $article)
        <tr>
            <td class="align-middle">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="article_status_id"
                           value="{{$article->articles_id}}">
                    <label class="form-check-label" for="flexCheckDefault"></label>
                </div>
            </td>
            <td class="align-middle">{{$article->articles_id}}</td>
            <td class="align-middle" width="350px">{{$article->title}}</td>
            <td class="align-middle"><img src="{{$article->image}}" width="100px"/></td>
            <td class="align-middle" width="100px" style="white-space: nowrap; min-width: 100px;">
                @if(!empty($article->mainCategories))
                    {!!$article->mainCategories->pluck('name')->implode('<div>')!!}
                @endif
            </td>
            <td class="align-middle" width="100px" style="white-space: nowrap">
                @if(!empty($article->subCategories))
                    @php
                        $mainCategories = $article->mainCategories->pluck('categories_id');
                        $subCategoriesIds = $article->subCategories->pluck('categories_id');
                    @endphp
                    @foreach($mainCategories as $main)
                        @if($subCategoriesIds->contains($main))
                            <div>{{$article->subCategories[$subCategoriesIds->search($main)]->name}}</div>
                        @else
                            <div>&nbsp;</div>
                        @endif
                    @endforeach
                @endif
            </td>
            <td class="align-middle" width="100px"
                style="min-width: 100px">{{optional($article->authorModel)->name}}</td>
            <td class="align-middle" width="100px">{{$article->publish}}</td>
            <td class="align-middle" width="100px">{{$article->updated_at->toDateTimeString()}}</td>
            <td class="align-middle" width="100px">
                {!!$article->articles_status == 0 ? '<i style="color: #b80000;" class="fa fa-times"></i>' : '<i style="color: green;" class="fa fa-check"></i>'!!}
            </td>
            <td class="align-middle" style="min-width: 160px">
                <a href="{{route('articles_ad.show', $article->articles_id)}}" class="btn btn-sm btn-secondary" target="_blank">全文瀏覽</a>
                @can('articles_ad-edit')
                <a href="{{route('articles_ad.edit', $article->articles_id)}}" class="btn btn-sm btn-primary">編輯</a>
                @endcan
            </td>
        </tr>
    @endforeach
@stop

@section('modal')

@stop

@section('css')
@stop
@section('js')
    <script>
        let urlParams = new URLSearchParams(window.location.search);
        document.cookie = "article_ad_query_string=" + (urlParams || "") + "; path=/";

        syncSubCategoriesOptions($('#search_main_category_id').val(), 'search_sub_category_id', '{{request()->input('search_sub_category_id')}}')

        function openBlank(url) {
            const a = document.createElement('a')
            a.href = url
            a.target = '_blank'
            a.click()
        }

        // 首頁頭條
        function key_visual(url) {
            const question = confirm('是否加入頭條?')

            if (question) {
                openBlank(url)
            }
        }

        // 推播
        function notifications(url) {
            const question = confirm('是否加入推播?')

            if (question) {
                openBlank(url)
            }
        }

        function syncSubCategoriesOptions(mainId, appendToId, selectedId = null) {
            if (mainId > 0) {
                fetch('/admin/sub-category-by-main/' + mainId)
                    .then((response) => {
                        return response.json()
                    }).then((response) => {
                    if (response.length > 0) {
                        response.forEach((element) => {
                            option = document.createElement('option')
                            option.text = element.name
                            option.value = element.sub_categories_id
                            if (selectedId !== null && element.sub_categories_id == selectedId) {
                                option.selected = 'selected'
                            }
                            document.getElementById(appendToId).appendChild(option)
                        })

                    }
                })
            }
        }

        $(document).ready(function () {
            let option

            function resetToOneOption(target) {
                target.html('<option value="-1">全部</option>')
            }

            // 全選
            $('.select-all').click(function () {
                $('input[name="article_status_id"]').each(function (i) {
                    $(this).click()
                });
            })

            // 主分類連動子分類
            $('#search_main_category_id').on('change', function () {
                resetToOneOption($('#search_sub_category_id'))
                syncSubCategoriesOptions($(this).val(), 'search_sub_category_id')
            })

            // 列表主分類連動子分類
            $('#list_main_category_id').on('change', function () {
                resetToOneOption($('#list_sub_category_id'))
                syncSubCategoriesOptions($(this).val(), 'list_sub_category_id')
            })

            // 批次加入多則於此類別
            $('.quick_append_categories').on('click', function () {
                const checkedArticleIds = [];

                $('input[name="article_status_id"]:checked').each(function () {
                    checkedArticleIds.push($(this).val())
                })

                fetch("{{route('articles.append.categories')}}", {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        '_token': '{{ csrf_token() }}',
                        'article_ids': checkedArticleIds,
                        'main_category': $('#list_main_category_id').val(),
                        'sub_category': $('#list_sub_category_id').val(),
                    })
                })
            })

            // 取消查詢
            $('.reset').click(function () {
                location.href = "{{route('articles_ad.index')}}"
            })
        })
    </script>
@stop

