@extends('adminlte::page')

@section('title', 'AiWize文章列表')

@section('content_header')
    <h1>AiWize文章列表</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
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
                                    {{ $data['ai_wize_id'] }}
                                </td>
                                <td class="align-middle">
                                    {{ $data['ai_wize_publish'] }}
                                </td>
                                <td class="align-middle">
                                    {{ $data['health_article_id'] }}
                                </td>
                                <td class="align-middle">
                                    {{ $data['table_title'] }}
                                </td>
                                <td class="align-middle">
                                    {{ $data['choose_user'] }}
                                </td>
                                <td class="align-middle">
                                    {!! $data['StatusCss'] !!}
                                </td>
                                <td class="align-middle">
                                    @can($role_name.'-edit')
                                        <button type="button" class="btn btn-primary btn-flat mr-4 editBtn" data-id="{{$data->id}}">查看</button>
                                    @endcan
                                    @can($role_name.'-delete')
                                        <!-- <button type="button" class="btn btn-danger btn-flat mr-4 deleteBtn" data-id="{{$data->id}}" {{ $data['health_article_id'] ? 'disabled' : ''}}>刪除</button> -->
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        {!! $datas->withQueryString()->render() !!}
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
            $('.modal-dialog').css('max-width', '50%');

            // 點擊編輯的按鈕
            $('.editBtn').on('click', function () {
                var edit_id = $(this).data('id');
                var editUrl = "{{url()->current()}}/" + edit_id + '/edit';

                $.get(editUrl, function (data) {
                    reset_modal();
                    $('#editModalTitle').html('AI Wize文章');

                    $('#editId').val(data.id)
                    $('#ai_wize_id').val(data.ai_wize_id);
                    $('#ai_wize_publish').val(data.ai_wize_publish);
                    $('#health_article_id').val(data.health_article_id);
                    if (data.health_article_id !== null) {
                        $('#saveBtn').prop('disabled', true);
                    }
                    // deal with long title and short title
                    let long_regex1 = /\(([^)]+)\)/g;
                    let long_result1 = data.long_title.replace(long_regex1, "");
                    let long_regex2 = /\d+\./g;
                    let long_result2 = long_result1.replace(long_regex2, "");
                    let long_lines = long_result2.split("\n");
                    let long_title_result = "";
                    long_lines.forEach(line => {
                        long_title_result += '<option value="' + line + '">' + line + '</option>';
                    });
                    $('#long_title').append(long_title_result);
                    let short_regex1 = /\(([^)]+)\)/g;
                    let short_result1 = data.short_title.replace(short_regex1, "");
                    let short_regex2 = /\d+\./g;
                    let short_result2 = short_result1.replace(short_regex2, "");
                    let short_lines = short_result2.split("\n");
                    let short_title_result = "";
                    short_lines.forEach(line => {
                        short_title_result += '<option value="' + line + '">' + line + '</option>';
                    });
                    $('#short_title').append(short_title_result);
                    $('#content').html(data.content).prop('disabled', true);
                    $('#keyword').val(data.keyword);
                    $('#saveBtn').text('使用');
                    $('#editModal').modal({
                        backdrop:"static",
                        keyboard:false,
                        show: true
                    });
                })
            })

            $('#saveBtn').on('click', function () {
                Swal.fire({
                    title: '確定使用？',
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
        });

        // 展開modal時，reset裡面的規則
        function reset_modal() {
            $('#validateMsg').html('');
            $('#validateMsg').css('display', 'none');
            $('#editModalTitle').html('新增關鍵字');
            $('#editId').val('');
            $('#editForm input[type="text"]').val('');
            $('#long_title option').remove();
            $('#short_title option').remove();
            $('#content').html('');
            $('#saveBtn').prop('disabled', false);
        }

        // 新增/編輯的儲存動作
        function saveAction() {
            $('#validateMsg').html('');
            $('#validateMsg').css('display', 'none');

            var url = "{{ route('articles.create') }}";
            url = url + '?aiwize=' + $('#editId').val() + '&lt=' + $('#long_title').val() + '&st=' + $('#short_title').val();

            location.href = url;

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
    </script>
@stop
