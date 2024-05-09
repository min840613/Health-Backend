@extends('components.index')

@section('title')
    廣編稿管理
@stop

@section('content_header')
    <h1>廣編稿管理</h1>
@stop

@section('create')
    <a href="{{route('sponsorlist.create')}}" class="btn btn-sm btn-success">新增</a>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="float-left ml-1 d-flex">
                    <form method="GET" action="{{route('sponsorlist.index')}}" accept-charset="UTF-8" id="search_box">
                        <div class="input-group">
                            <div class="mr-2">請選擇位置：</div>
                            <select class="form-control" id="searchMainCategories" name="searchMainCategories">
                                <option value="0">首頁</option>
                                @foreach($MainCategoriesList as $key=>$value)
                                <option value="{{$value['categories_id']}}" {{request()->query('searchMainCategories') == $value['categories_id'] ? 'selected' : ''}}>
                                    {{$value['name']}}
                                </option>
                                @endforeach
                            </select>
                            <select class="form-control ml-2 d-none" id="searchSubCategories" name="searchSubCategories">
                                <option value="">無子分類</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            @if ($message = Session::get('error'))
                <div class="alert alert-danger">
                    <p class="m-0">{{ $message }}</p>
                </div>
            @endif
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p class="m-0">{{ $message }}</p>
                </div>
            @endif
            <div class="card-body p-0">
                <form id="keyvisual">
                    {{ csrf_field() }}
                    <table id="sort_table" class="table sort_table">
                        <thead>
                            <tr>
                                @foreach ($field as $value)
                                <th>{{ $value }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $key => $data)
                                <tr data-id="{{$key+1}}">
                                    <td class="align-middle">
                                        {{$data['position']}}
                                    </td>
                                    <td class="align-middle">
                                        <img src="{{$data['article']?$data['article']['image']:''}}" style="width: 100px;">
                                    </td>
                                    <td class="align-middle">
                                        {{$data['article']?$data['article']['title']:''}}
                                    </td>
                                    <td class="align-middle">
                                        <?= date('Y-m-d H:i',strtotime($data->start)) ?>
                                    </td>
                                    <td class="align-middle">
                                        <?= date('Y-m-d H:i',strtotime($data->end)) ?>
                                    </td>
                                    <td class="align-middle" width="100px">
                                        @php
                                            if($data['article']):
                                                $is_show = true;
                                                if(strtotime($data['start']) > time() || strtotime($data['end']) < time()):
                                                    $is_show = false;
                                                endif;
                                                if($data['article']['articles_status'] == 0):
                                                    $is_show = false;
                                                endif;
                                                if(strtotime($data['article']['publish']) > time()):
                                                    $is_show = false;
                                                endif;
                                                if($is_show):
                                                    echo '<i style="color: green;" class="fa fa-check"></i>';
                                                else: 
                                                    echo '<i style="color: #b80000;" class="fa fa-times"></i>';
                                                endif;
                                            else:
                                                echo '<i style="color: #b80000;" class="fa fa-times"></i>';
                                            endif;
                                        @endphp
                                    </td>
                                    <td class="align-middle">
                                        @can($role_name.'-edit')
                                        <a class="btn btn-primary btn-flat editBtn" data-id="{{$data->id}}" href="javascript:;">編輯</a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
    @include('articles.sponsorlist.modal')
@stop

@section('css')
@stop

@section('js')
<script>
    $().ready(function(){
        var searchSubCategoriesId = '{{request()->query("searchSubCategories")}}';
        if( searchSubCategoriesId ){
            ajaxChangeCategories($("#searchMainCategories"), $("#searchMainCategories").val(), searchSubCategoriesId);
        } else {
            if($("#searchMainCategories").val() && $("#searchMainCategories").val() > 0){
                ajaxChangeCategories($("#searchMainCategories"), $("#searchMainCategories").val());
            }
        }

        $("#searchMainCategories").change(function(){
            $("#searchSubCategories").val('');
            $("#search_box").submit();
        })

        $("#searchSubCategories").change(function(){
            $("#search_box").submit();
        })

        //  Model hide 事件
        $('#editModal').on('hidden.bs.modal', function () {
            reset_modal();
        })

        $('.editBtn').on('click', function() {
            var edit_id = $(this).data('id');
            var editUrl = "{{url()->current()}}/"+ edit_id + '/edit';
            $.get(editUrl, function (data) {
                reset_modal();
                $('#editModalTitle').html('編輯廣編稿');
                $('#sponsor_id').val(edit_id);
                $('#article_id').val(data.article_id);
                $('#start').val(data.start);
                $('#end').val(data.end);
                $('#editModal').modal('show');
                
                $('input[name="start"]').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    startDate: data.start,
                    timePicker: true,
                    timePicker24Hour: true,
                    cancelButtonClasses: "btn-danger",
                    locale: {
                        format: 'YYYY-MM-DD HH:mm'
                    }
                });
                $('input[name="end"]').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    startDate: data.end,
                    timePicker: true,
                    timePicker24Hour: true,
                    cancelButtonClasses: "btn-danger",
                    locale: {
                        format: 'YYYY-MM-DD HH:mm'
                    }
                });
            })
        })

        $('#saveBtn').on('click', function() {
            Swal.fire({
                title: '確定儲存？',
                icon: 'question',
                iconColor: '#f87e6c',
                showDenyButton: true,
                confirmButtonText: `確定`,
                confirmButtonColor: '#f87e6c',
                denyButtonText: `取消`,
                denyButtonColor: '#9c9996'
            }).then((result) => {
                if (result.isConfirmed) {
                    saveAction();
                }
            })
        })
    })

    function ajaxChangeCategories(label, categories_id, subcategories_id = '') {
        subcategories_item = label.next('select');
        $.ajax({
            url:'{{ route("articles.change.categories") }}',
            type: "POST",
            data: {'categories_id':categories_id,"_token":$("input[name='_token']").val()},
            dataType: 'json',
            async: false,
            success: function(result){
                if(result.length > 0){
                    var option = "<option value=''>主分類列表</option>";
                    $.each(result, function(index, value){
                        if(subcategories_id == value.sub_categories_id){
                            option = option + "<option value='" + value.sub_categories_id + "' selected>" + value.name + "</option>";
                        } else {
                            option = option + "<option value='" + value.sub_categories_id + "'>" + value.name + "</option>";
                        }
                    })
                    subcategories_item.html(option);
                } else {
                    var option = "<option value=''>無子分類</option>";
                    subcategories_item.html(option);
                }
            }
        });
    }

    function reset_modal(){
        $('#validateMsg').html('');
        $('#validateMsg').css('display', 'none');
        $('#editModalTitle').html('編輯廣編稿');
        $('#sponsor_id').val('');
        $('#article_id').val('');
        $('#start').val('');
        $('#end').val('');
    }

    function saveAction() {
        let formData = new FormData($('#editForm')[0]);
        let ajaxUrl = '';
        let ajaxMethod = '';

        if($('#sponsor_id').val() == ''){
            ajaxUrl = "{{url()->current()}}";
        }else{
            ajaxUrl = "{{url()->current()}}/"+$('#sponsor_id').val();
            formData.append('_method', 'PATCH');
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

        $.ajax({
            data: formData,
            url: ajaxUrl,
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
                if (err.status == 422) {
                    printErrorMessage(err.responseJSON.errors);
                }
                Swal.fire({
                    title: '儲存失敗',
                    icon: 'error',
                    confirmButtonText: `確定`,
                    confirmButtonColor: '#f87e6c',
                })
            }
        });
    }

    function printErrorMessage(errorMessage){
        let validateMsg = '<ul class="mb-0">';
        $.each(errorMessage, function(key, value){
            validateMsg += '<li>' + value + '</li>';
        });
        validateMsg += '</ul>';
        
        console.log(validateMsg);

        $('#validateMsg').html(validateMsg);
        $('#validateMsg').css('display', 'block');
    }
</script>
@stop

