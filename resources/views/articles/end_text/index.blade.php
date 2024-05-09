@extends('adminlte::page')

@section('title', '文末廣宣管理')

@section('content_header')
    <h1>文末廣宣管理</h1>
@stop



@section('content')

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                @if(!isset($create) || $create == true)
                @can($role_name.'-create')
                <div class="float-left mr-3">
                    <button type="button" class="btn btn-block btn-success btn-flat float-right px-4 createBtn">新增</button>
                </div>
                <div class="float-left mr-3">
                    <button type="button" class="btn btn-block btn-primary btn-flat float-right px-4 saveAllBtn">儲存排序</button>
                </div>
                @endcan
                @endif
                <div class="float-right ml-1 d-flex">
                    <div class="input-group btn btn-warning">
                        {{$select_type['title']}}：
                        {!! Form::select($select_type['name'], $select_type['option'], isset($_GET['select_type']) ? $_GET['select_type'] : 0, ['class' => 'px-4']) !!}
                    </div>
                </div>
                @if(isset($search) && $search == true)
                <div class="float-right ml-1 d-flex">
                    @if(!$__env->yieldContent('push'))
                    @if($keywords)
                    <button class="reset_search mr-1 btn btn-outline-secondary">重置搜尋</button>
                    @endif
                    {!! Form::open(['method' => 'GET', 'route' => [$site_name.'.index'],'id' => 'search_box']) !!}
                    <div class="input-group">
                        {!! Form::text('keywords','', ['class' => 'form-control','placeholder'=>($keywords)?$keywords:"Search"]) !!}
                        <div class="input-group-append">
                            {!! Form::submit('搜尋', ['class' => 'btn btn-secondary']) !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                    @else
                        @yield('search')
                    @endif
                </div>
                @endif
            </div>
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p class="m-0">{{ $message }}</p>
                </div>
            @endif
            <div class="card-body p-0">
                <form id="end_text">
                    {{ csrf_field() }}
                    <table id="sort_table" class="table sort_table">
                        <thead>
                            <tr>
                                @if(!isset($no_id) || $no_id == false)
                                <th style="width: 10px">ID</th>
                                @endif
                                @foreach ($field as $value)
                                    <th>{{ $value }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $key => $data)
                                <tr data-id="{{$key+1}}">
                                    <td class="align-middle">
                                        {{ $data->text_id }}
                                        {!! Form::input('hidden', "end_text[".($key+1)."][text_id]", $data->text_id ) !!}
                                    </td>
                                    <td class="align-middle" style="width:25%;">
                                        <?= $data->short_title ?>
                                    </td>
                                    <td class="align-middle">
                                        <?= $data->TextTypeWording ?>
                                    </td>
                                    <td class="align-middle">
                                        <?= $data->created_user ?>
                                    </td>
                                    <td class="align-middle">
                                        {!! $data->published_at !!} <br> – {{ $data->published_end }}
                                    </td>
                                    <td class="align-middle">
                                        {!! $data->StatusCss !!}
                                    </td>
                                    <td class="align-middle">
                                        @can('end_text-edit')
                                            <button type="button" class="btn btn-primary btn-flat mr-4 editBtn" data-id="{{$data->text_id}}">編輯</button>
                                        @endcan
                                    </td>
                                    <td class="align-middle">
                                        @can('end_text-delete')
                                            <button type="button" class="btn btn-danger btn-flat mr-4 deleteBtn" data-id="{{$data->text_id}}">刪除</button>
                                        @endcan
                                    </td>
                                    <td class="align-middle">
                                        <div class="handle health_sort"><i class="fa fa-fw fa-sort"></i></div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

    @include('components.formbase_modal')
@stop

@section('css')
    @yield('js')
@stop

@section('js')
    @include('components.tinymce')
<script>
    //排序功能
    $(".sort_table tbody").sortable({handle: ".handle"});

    $('textarea#content').tinymce({
        'token': $('form input[name="_token"]').val()
    })

    $(document).on('focusin', function(e) {
        // 此段未解決bootstrap modal 交互作用下，tinyMCE 的 text 無法輸入問題
        if ($(e.target).closest(".tox-textfield").length){
            e.stopImmediatePropagation();
        }

        // 此段未解決bootstrap modal 交互作用下，tinyMCE 的 textarea 無法輸入問題
        if ($(e.target).closest(".tox-textarea").length) {
            e.stopImmediatePropagation();
        }
    });
</script>
<script>
    $(document).ready(function() {




        $('.modal-dialog').width('70%');
        $('.modal-dialog').css('max-width', '80%');

        $('select[name=select_type]').on('change', function() {
            var search_parameter = '';
            if(this.value != 0){
                search_parameter += '?select_type='+this.value;
            }
            window.location.href = '{{URL::current()}}' + search_parameter;
        });

        $('input[name=url]').parent().css('display', 'none');
        $('input[name=published_at]').parent().parent().parent().css('display', 'none');
        $('input[name=published_end]').parent().parent().parent().css('display', 'none');

        $('select[name=text_type]').on('change', function() {
            if(this.value == 1){
                showTypeOne();
            }
            if(this.value == 2){
                showTypeTwo();
            }
        });


        //  Model hide 事件
        $('#editModal').on('hidden.bs.modal', function () {
            reset_modal();
        })

        $('.createBtn').on('click', function() {

           $('input[name="published_at"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                startDate:  moment().startOf('hour'),
                timePicker: true,
                timePicker24Hour: true,
                cancelButtonClasses: "btn-danger",
                locale: {
                    format: 'YYYY-MM-DD HH:mm',
                },
            });

           $('input[name="published_end"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                startDate:  moment().add(7, 'days').format('YYYY-MM-DD 23:59'),
                timePicker: true,
                timePicker24Hour: true,
                cancelButtonClasses: "btn-danger",
                locale: {
                    format: 'YYYY-MM-DD HH:mm'
                }
            });
            
            reset_modal();
            $('#editModal').modal('show');
        })

        $('.editBtn').on('click', function() {
            var edit_id = $(this).data('id');
            var editUrl = "{{url()->current()}}/"+ edit_id + '/edit';
            $.get(editUrl, function (data) {
                reset_modal();
                $('#editModalTitle').html('編輯文末廣宣');
                $.each(data,function(index, child){
                    if(index == 'content'){
                        tinymce.get('content').setContent(child);
                    }else{
                        $('#'+index).val(child);
                    }
                })
                $('#editId').val(edit_id);
                $('select[name=text_type]').attr("disabled", true);
                if(data.text_type == 1){
                    showTypeOne();
                }
                if(data.text_type == 2){
                    showTypeTwo();
                }

               $('input[name="published_at"]').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    startDate:  data.published_at,
                    timePicker: true,
                    timePicker24Hour: true,
                    cancelButtonClasses: "btn-danger",
                    locale: {
                        format: 'YYYY-MM-DD HH:mm'
                    }
                });

               $('input[name="published_end"]').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    startDate:  data.published_end,
                    timePicker: true,
                    timePicker24Hour: true,
                    cancelButtonClasses: "btn-danger",
                    locale: {
                        format: 'YYYY-MM-DD HH:mm'
                    }
                });



                $('#editModal').modal('show');
            })
        })

        $('.saveAllBtn').on('click', function() {
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
                    saveAllAction();
                }
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

        $('.deleteBtn').on('click', function() {
            Swal.fire({
                title: '確定刪除？',
                icon: 'question',
                iconColor: '#f87e6c',
                showDenyButton: true,
                confirmButtonText: `確定`,
                confirmButtonColor: '#f87e6c',
                denyButtonText: `取消`,
                denyButtonColor: '#9c9996'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteAction($(this).attr('data-id'));
                }
            })

        })
    });



    function showTypeOne(){
        $('input[name=url]').parent().css('display', 'none');
        $('input[name=published_at]').parent().parent().parent().css('display', 'none');
        $('input[name=published_end]').parent().parent().parent().css('display', 'none');
        $('#content').parent().css('display', 'block');
    }

    function showTypeTwo(){
        $('input[name=url]').parent().css('display', 'block');
        $('input[name=published_at]').parent().parent().parent().css('display', 'block');
        $('input[name=published_end]').parent().parent().parent().css('display', 'block');
        $('#content').parent().css('display', 'none');
    }

    function reset_modal(){
        $('#validateMsg').html('');
        $('#validateMsg').css('display', 'none');
        $('#editModalTitle').html('新增文末廣宣');
        $('#editId').val('');
        $('#editForm input[type="text"]').val('');
        $('#editForm textarea').val('');
        tinymce.get('content').setContent('');
        $("#editForm select option:first").prop("selected", 'selected');
        $('select[name=text_type]').attr("disabled", false);
        showTypeOne();
    }

    function saveAllAction()
    {
        let EndTextFormData = new FormData($('#end_text')[0]);

        let sortId = new Array();

        $("form#end_text tbody tr").each(function(idx){
            sortId.push($(this).attr('data-id'));
        });

        EndTextFormData.append('sortId', sortId);

        let ajaxUrl2 = '';
        ajaxUrl2 = "{{url()->current()}}/"+'save_sort';
        EndTextFormData.append('_method', 'PATCH');


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('#end_text input[name="_token"]').val()
            }
        });

        $.ajax({
            data: EndTextFormData,
            url: ajaxUrl2,
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
                if (err.status == 422) {
                    printErrorMessage(err.responseJSON.errors);
                }

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
            }
        });
    }

    function saveAction()
    {
        $('#validateMsg').html('');
        $('#validateMsg').css('display', 'none');

        let formData = new FormData($('#editForm')[0]);

        let ajaxUrl = '';
        let ajaxMethod = '';

        if($('#editId').val() == ''){
            ajaxUrl = "{{url()->current()}}";
        }else{
            ajaxUrl = "{{url()->current()}}/"+$('#editId').val();
            formData.append('_method', 'PATCH');
            formData.append('text_type', $('select[name=text_type]').val());
        }

        formData.append('content', tinymce.get('content').getContent());

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
                console.log(err);
                if (err.status == 422) {
                    printErrorMessage(err.responseJSON.errors);
                }

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
            }
        });
    }

    function deleteAction(text_id)
    {
        let deleteFormData = new FormData();
        deleteFormData.append('_method', 'DELETE');


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('#end_text input[name="_token"]').val()
            }
        });

        $.ajax({
            data: deleteFormData,
            url: "{{url()->current()}}/"+text_id,
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
                if (err.status == 422) {
                    printErrorMessage(err.responseJSON.errors);
                }

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
            }
        });
    }

    function printErrorMessage(errorMessage){

        let validateMsg = '<ul class="mb-0">';

        $.each(errorMessage, function(key, value){
            $.each(value, function(k, v){
                validateMsg += '<li>' + value + '</li>';
            });
        });

        validateMsg += '</ul>'

        $('#validateMsg').html(validateMsg);
        $('#validateMsg').css('display', 'block');
    }
</script>
@stop
