@extends('adminlte::page')

@section('title', '關鍵字管理')

@section('content_header')
    <h1>關鍵字管理</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    @if(!isset($create) || $create == true)
                        @can($role_name.'-create')
                            <div class="float-left mr-3">
                                <button type="button" class="btn btn-block btn-success btn-flat float-right px-4 createBtn">新增</button>
                            </div>
                        @endcan
                    @endif
                </div>
            </div>
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p class="m-0">{{ $message }}</p>
                </div>
            @endif
            <div class="card-body p-0">
                <form>
                    {{ csrf_field() }}
                    <table class="table">
                        <thead>
                        <tr>
                            @foreach ($field as $value)
                                <th>{{ $value }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($datas as $key => $data)
                            <tr>
                                <td class="align-middle">
                                    {{ $data['id'] }}
                                </td>
                                <td class="align-middle">
                                    {{ $data['keyword'] }}
                                </td>
                                <td class="align-middle">
                                    {{ $data['start_at'] }}
                                </td>
                                <td class="align-middle">
                                    {{ $data['end_at'] }}
                                </td>
                                <td class="align-middle">
                                    {{ $data->questions->count() }}
                                </td>
                                <td class="align-middle">
                                    @can($role_name.'-edit')
                                        <button type="button" class="btn btn-primary btn-flat mr-4 editBtn" data-id="{{$data->id}}">編輯</button>
                                    @endcan
                                    @can($role_name.'-delete')
                                        <button type="button" class="btn btn-danger btn-flat mr-4 deleteBtn" data-id="{{$data->id}}">刪除</button>
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
    <div class="loading-parent" style="display: none;">
        <div class="loading" style="display:flex;">
            <x-adminlte-small-box title="Loading" text="產生問題中..." icon="fas fa-chart-bar" theme="info" style="width:50%; margin:auto;"/>
        </div>
    </div>
    @include('components.formbase_modal')
@stop

@section('css')
    <style>
        .modal-body {
            height: 600px;
        }
        .close {
            cursor: pointer;
        }
        #questions {
            border: solid 1px #ccc;
            padding-top: 15px;
            border-radius: 5px;
        }
        #questions .text {
            cursor: all-scroll;
        }
        #sum-area {
            margin-top: 10px;
            font-weight: bold;
        }
        .loading {
            width: 100%;
            height: 100vh;
            z-index: 9999999;
            position: fixed;
            top: 0;
            left: 0;
            background: white;
            opacity: 0.9;
        }
    </style>
@stop

@section('js')
    @vite('resources/js/app.js')
    <script type="module">
        $(document).ready(function () {
            var edit_id = null
            let uuid

            // 設定modal的寬度
            $('.modal-dialog').width('100%');
            $('.modal-dialog').css('max-width', '45%');

            // 點擊新增的按鈕
            $('.createBtn').on('click', function () {
                reset_modal();

                $('#count').val(3)
                calculateCount()

                $('#editModal').modal({
                    backdrop:"static",
                    keyboard:false,
                    show: true
                });
                $('#editModal').modal('handleUpdate');
            })

            // 點擊編輯的按鈕
            $('.editBtn').on('click', function () {
                var edit_id = $(this).data('id');
                var editUrl = "{{url()->current()}}/" + edit_id + '/edit';
                $.get(editUrl, function (data) {
                    reset_modal();
                    $('#editModalTitle').html('編輯關鍵字');

                    $('#editId').val(data.id)
                    $('#keyword').val(data.keyword);
                    $('#start_at').val(data.start_at);
                    $('#end_at').val(data.end_at);
                    $('#count').val(data.count);

                    let str = ``
                    data.questions.forEach(item => {
                        str += appendDynamicColumns(item.question)
                    })
                    $('#questions').html(str)
                    $('#questions').sortable();
                    enableCloseButton()
                    calculateCount()

                    $('#editModal').modal({
                        backdrop:"static",
                        keyboard:false,
                        show: true
                    });
                })
            })

            $('#saveBtn').on('click', function () {
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
                        $('#saveBtn').prop('disabled', true);
                        saveAction();
                    }
                })
            })

            $('.deleteBtn').on('click', function () {
                let url = '{{route('deepq.keywords.destroy', ':keyword')}}'
                url = url.replace(':keyword', $(this).attr('data-id'))

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
                        fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                '_token': '{{ csrf_token() }}',
                            })
                        }).then((response) => {
                            return response.json()
                        }).then(res => {
                            if (res.status === '00000') {
                                window.location.reload()
                            } else {
                                alert('刪除失敗')
                            }
                        })
                    }
                })
            })

            $('.generateQuestion').on('click', function () {
                $('#editModal .modal-body').append($('.loading-parent').html());

                uuid = window.uuid_v4()

                window.Echo.channel(`health.deepq-keyword.{{auth()->user()->id}}.${uuid}`)
                    .listen('.DeepqKeywordGenerated', (data) => {
                        if (data.questions.length === 0) {
                            Swal.fire({
                                title: '未取得問題',
                                icon: 'error',
                                confirmButtonText: `確定`,
                                confirmButtonColor: '#f87e6c',
                            })
                            $('#editModal .modal-body .loading').remove()
                        } else {
                            Swal.fire({
                                title: '產生問題成功',
                                icon: 'success',
                                confirmButtonText: `確定`,
                            })

                            let str = ``
                            data.questions.forEach(item => {
                                str += appendDynamicColumns(item)
                            })
                            $('#questions').html(str)
                            $('#questions').sortable();
                            calculateCount()

                            $('#editModal .modal-body .loading').remove()
                        }
                        enableCloseButton()
                    });

                const params = new URLSearchParams();
                params.append('keyword', $('#keyword').val());
                params.append('count', $('#count').val());
                params.append('id', edit_id ?? null);
                params.append('uuid', uuid);

                fetch('{{route('deepq.keyword.generate')}}?' + params).then((response) => {
                    return response.json()
                }).then((res) => {
                    if (res.status !== '00000') {
                        $('#editModal .modal-body .loading').remove()
                        Swal.fire({
                            title: '儲存失敗',
                            icon: 'error',
                            confirmButtonText: `確定`,
                            confirmButtonColor: '#f87e6c',
                        })
                    }
                });

            })
        });

        // 展開modal時，reset裡面的規則
        function reset_modal() {
            $('#validateMsg').html('');
            $('#validateMsg').css('display', 'none');
            $('#editModalTitle').html('新增關鍵字');
            $('#editId').val('');
            $('#editForm input[type="text"]').val('');
            $('#editForm #start_at').val('{{now()->format('Y-m-d H:i')}}');
            $('#editForm #end_at').val('{{now()->addDays(7)->endOfDay()->format('Y-m-d H:i')}}');
            $('#editForm #questions').html('');
        }

        // 新增/編輯的儲存動作
        function saveAction() {
            $('#validateMsg').html('');
            $('#validateMsg').css('display', 'none');

            let formData = new FormData($('#editForm')[0]);

            let ajaxUrl = '';

            if ($('#editId').val() == '') {
                ajaxUrl = "{{url()->current()}}";
            } else {
                ajaxUrl = "{{url()->current()}}/" + $('#editId').val();
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
                    $('#saveBtn').prop('disabled', false);
                    if (err.status == 422) {
                        printErrorMessage(err.responseJSON.errors);
                    }

                    if (err.status == 400 && err.responseJSON.errMsg != undefined) {
                        Swal.fire({
                            title: err.responseJSON.errMsg,
                            icon: 'error',
                            confirmButtonText: `確定`,
                            confirmButtonColor: '#f87e6c',
                        })
                    } else {
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

        function printErrorMessage(errorMessage) {
            let validateMsg = '<ul class="mb-0">';

            $.each(errorMessage, function (key, value) {
                $.each(value, function (k, v) {
                    validateMsg += '<li>' + v + '</li>';
                    return false;
                });
                return false;
            });

            validateMsg += '</ul>'

            $('#validateMsg').html(validateMsg);
            $('#validateMsg').css('display', 'block');
        }

        function calculateCount() {
            $('#sum-area').html(`刊登數量: `+$('#questions .close').length)
        }

        function appendDynamicColumns(question) {
            return `<div class="input-group mb-3" style="width:90%">
                        <div class="form-control text">${question}</div>
                        <input type="hidden" name="question[]" value="${question}">
                        <div class="input-group-append">
                            <span class="input-group-text close"><i class="fa fa-times" aria-hidden="true" style="font-size: 70%"></i></span>
                        </div>
                    </div>`
        }

        function enableCloseButton() {
            $('#questions .close').on('click', function(){
                $(this).parents('.input-group').remove()
                calculateCount()
            })
        }
    </script>
@stop
