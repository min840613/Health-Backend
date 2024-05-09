@extends('app.news.base')

@section('title', 'APP-訊息管理')

@section('content_header')
    <h1>APP-訊息管理</h1>
@stop

@section('data_list')
        @if(!empty($datas))
        <tr>
            <td class="align-middle">{{ $datas->message }}</td>
            <td class="align-middle">{{ $datas->start }}</td>
            <td class="align-middle">{{ $datas->end }}</td>
            <td class="align-middle">
                @can('app_news-edit')
                    <button type="button" class="btn btn-primary btn-flat mr-4 editBtn" data-id="{{$datas->id}}">編輯</button>
                @endcan
            </td>
        </tr>
        @endif
@stop

@section('modal')
    @include('app.news.modal')
@stop

@section('css')
@stop

@section('js')

<script>

    // 解決不能使用兩個modal popup問題
    $(document).on('show.bs.modal', '.modal', function(event) {
        $(this).appendTo($('body'));
    }).on('shown.bs.modal', '.modal.in', function(event) {
        setModalsAndBackdropsOrder();
    }).on('hidden.bs.modal', '.modal', function(event) {
        setModalsAndBackdropsOrder();
    });

    // 解決不能使用兩個modal popup問題

    $(document).ready(function() {

        $('.modal-dialog').width('70%');
        $('.modal-dialog').css('max-width', '80%');

        //  Model hide 事件
        $('#editModal').on('hidden.bs.modal', function () {
            reset_modal();
        })

        $('.createBtn').on('click', function() {
            reset_modal();
            $('#editModal').modal('show');
        })

        $('.editBtn').on('click', function() {
            var edit_id = $(this).data('id');
            var editUrl = "{{url()->current()}}/"+ edit_id + '/edit';
            $.get(editUrl, function (data) {

                reset_modal();
                $('#editModalTitle').html('編輯APP訊息');
                $('#id').val(edit_id);
                $('#message').val(data.message);
                $('#start').val(data.start);
                $('#end').val(data.end);

               $('input[name="start"]').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    startDate:  data.start,
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
                    startDate:  data.end,
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
                    $('#saveBtn').prop('disabled', true);
                    saveAction();
                }
            })

        })
    });

    function reset_modal(){
        $('#editForm').css('padding-top', '30px');
        $('#validateMsg').html('');
        $('#validateMsg').css('display', 'none');
        $('#editModalTitle').html('編輯APP訊息');
        $('#editId').val('');
        $('#editForm text').val('');
        $('#message').attr('rows', '3');
    }

    function saveAction()
    {
        let formData = new FormData($('#editForm')[0]);

        let ajaxUrl = '';
        let ajaxMethod = '';

        if($('#id').val() == ''){
            ajaxUrl = "{{url()->current()}}";
        }else{
            ajaxUrl = "{{url()->current()}}/"+$('#id').val();
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
