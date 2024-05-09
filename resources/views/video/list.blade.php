@extends('adminlte::page')

@section('title', '影片管理')



@section('content_header')
    <h1>影片管理</h1>


    <!-- 篩選功能 -->
    {{ Form::open(['route' => 'video.list', 'method' => 'get', 'style' => 'border:1px solid #ccc; padding: 5px;']) }}
    <div class="row col-10" style="padding-top: 10px">

        <div class="col-2">
            <label>分類</label>
            <x-adminlte-select2 name="album_id" igroup-size="sm">
                <option value="0">全部</option>
                @foreach($videoAlbumData as $videoAlbumDataObj)
                    <option
                        value="{{$videoAlbumDataObj->id}}" {{request()->input('album_id') == $videoAlbumDataObj->id ? 'selected' : ''}}>{{$videoAlbumDataObj->title}}</option>
                @endforeach
            </x-adminlte-select2>
        </div>

        <div class="col-2">
            <label>標題</label>
            <x-adminlte-input name="search_title" igroup-size="sm" value="{{request()->input('search_title')}}"/>
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

@section('content')
    <div class="col-md-12">
        <div class="card">

            <div class="card-header">
                @can($role_name.'-create')
                <div class="float-left mr-3">
                    <button type="button" class="btn btn-block btn-success  createBtn">新增</button>
                </div>
                @endcan
            </div>

            <div class="card-body p-0">
                <form id="video_list">
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

                            @foreach ($videoGalleryData as $data)

                                <tr >
                                    <!-- 分類名稱 -->
                                    <td class="align-middle">
                                        <?= $data->album_title ?>
                                    </td>

                                    <!-- 影片標題 -->
                                    <td class="align-middle" >
                                        <?= $data->title ?>
                                    </td>

                                    <!-- 影片內容 -->
                                    <td class="align-middle" >
                                        <video width="200" height="120" controls>
                                            <source src="{{ config('constants.cdn.url') . $data->path . $data->video }}" type="video/mp4">
                                        </video>
                                    </td>
                                    
                                    <!-- 編輯 -->
                                    <td class="align-middle">
                                        @can($role_name.'-edit')
                                            <button type="button" class="btn btn-primary btn-flat mr-4 editBtn" data-id="{{$data->id}}">編輯</button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                        {{ $videoGalleryData->appends(request()->query())->links() }}
                </form>
            </div>

        </div>
    </div>
@include('components.formbase_modal')
@stop



@section('js')
<script>
    $('.reset').click(function () {
        location.href = "{{route('video.list')}}"
    })
    $('.createBtn').click(function(){
        location.href = "{{route('video.create')}}"
    })

    function reset_modal(){
        $('#validateMsg').html('');
        $('#validateMsg').css('display', 'none');
        $('#editModalTitle').html('');
        $('#editId').val('');
        $('#editForm input[type="text"]').val('');
    }

    function saveAction()
    {
        $('#validateMsg').html('');
        $('#validateMsg').css('display', 'none');


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

        $.ajax({
            data: JSON.stringify({
                "title":$("#editForm #title").val(),
                "album_id":$("#edit_album_id").val(),
            }),
            url: "{{url()->current()}}/"+$('#editId').val(),
            type: 'PUT',
            dataType: 'JSON',
            cache: false,
            processData: false,
            contentType: 'application/json',
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

    $(document).ready(function() {

        $('.modal-dialog').width('70%');
        $('.modal-dialog').css('max-width', '80%');

        //  Model hide 事件
        $('#editModal').on('hidden.bs.modal', function () {
            reset_modal();
        })

        $('.editBtn').on('click', function() {

            $('#editModal').modal('show');
            var edit_id = $(this).data('id');
            var editUrl = "{{url()->current()}}/"+ edit_id + '/edit';

            $.get(editUrl, function (data) {
                reset_modal();
                $('#editModalTitle').html('');
                $.each(data,function(index, child){
                    $('#'+index).val(child);
                })
                // console.log(data.album);
                $("#edit_album_id").val(data.album).change()
                

                $('#editId').val(edit_id);

                $('#editModal').modal('show');
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
</script>
@stop