@extends('components.index')

@section('title')
專家管理
@stop

@section('create')
    <div class="float-left mr-3">
        <button type="button" class="btn btn-block btn-success btn-flat float-right px-4 createBtn">新增</button>
    </div>
    <div class="float-left mr-3">
        <button type="button" class="btn btn-outline-info">總筆數
            <span class="badge">{{$counts->count()}}</span>
        </button>
    </div>
    <div class="float-left mr-3">
        <button type="button" class="btn btn-outline-info">醫師總數
            <span class="badge">{{$counts->where('type', 1)->count()}}</span>
        </button>
    </div>
    <div class="float-left mr-3">
        <button type="button" class="btn btn-outline-info">專家總數
            <span class="badge">{{$counts->where('type', 2)->count()}}</span>
        </button>
    </div>
    <div class="float-left mr-3">
        <button type="button" class="btn btn-outline-info">營養師總數
            <span class="badge">{{$counts->where('type', 3)->count()}}</span>
        </button>
    </div>
@endsection

@section('content_header')
    <h1>專家管理</h1>
    {{ Form::open(['route' => 'masters.index', 'method' => 'get', 'style' => 'border:1px solid #ccc; padding: 5px;']) }}
    @php
        $startConfig = [
            "singleDatePicker" => true,
            "showDropdowns" => true,
            "startDate" => request()->input('created_date_start') ?? "js:moment('1911-01-01')",
            "minYear" => 2000,
            "maxYear" => "js:parseInt(moment().format('YYYY'),10)+5",
            "cancelButtonClasses" => "btn-danger",
            "locale" => ["format" => "YYYY-MM-DD"],
        ];
        $endConfig = [
            "singleDatePicker" => true,
            "showDropdowns" => true,
            "startDate" => request()->input('created_date_end') ?? "js:moment()",
            "minYear" => 2000,
            "maxYear" => "js:parseInt(moment().format('YYYY'),10)+5",
            "cancelButtonClasses" => "btn-danger",
            "locale" => ["format" => "YYYY-MM-DD"],
        ];
    @endphp
    <div class="row col-10">
        <div class="col-2">
            <label>專家名稱</label>
            <x-adminlte-input name="master_name" igroup-size="sm"
                              value="{{request()->input('master_name')}}"/>
        </div>
        <div class="col-2">
            <label>專家英文名稱</label>
            <x-adminlte-input name="master_en_name" igroup-size="sm"
                              value="{{request()->input('master_en_name')}}"/>
        </div>
        <div class="col-1">
            <label>&nbsp;</label>
        </div>
        <div class="col-2">
            <label>類別</label>
            <x-adminlte-select2 name="master_type" igroup-size="sm">
                <option value="">所有類別</option>
                @foreach($filters['master_type'] as $code => $type)
                <option value="{{$code}}" {{request()->input('master_type') === (string)$code ? 'selected' : ''}}>{{$type}}</option>
                @endforeach
            </x-adminlte-select2>
        </div>
        <div class="col-2">
            <label>狀態</label>
            <x-adminlte-select2 name="master_status" igroup-size="sm">
                <option value="">所有狀態</option>
                @foreach($filters['master_status'] as $code => $status)
                <option value="{{$code}}" {{request()->input('master_status') === (string)$code ? 'selected' : ''}}>{{$status}}</option>
                @endforeach
            </x-adminlte-select2>
        </div>
    </div>
    <div class="row col-10" style="padding-top: 10px">
        <div class="col-2">
            <label>科別後台</label>
            <x-adminlte-select2 name="master_division" igroup-size="sm">
                <option value="">所有科別</option>
                @foreach($filters['master_division'] as $code => $item)
                    <option value="{{$code}}" {{request()->input('master_division') === (string)$code ? 'selected' : ''}}>{{$item}}</option>
                @endforeach
            </x-adminlte-select2>
        </div>
        <div class="col-2">
            <label>醫療院所</label>
            <x-adminlte-select2 name="master_institution" igroup-size="sm">
                <option value="">所有醫療院所</option>
                @foreach($filters['master_institution'] as $code => $item)
                    <option value="{{$code}}" {{request()->input('master_institution') === (string)$code ? 'selected' : ''}}>{{$item}}</option>
                @endforeach
            </x-adminlte-select2>
        </div>
        <div class="col-1">
            <label>&nbsp;</label>
        </div>
        <div class="col-2">
            <label>建立時間</label>
            <x-adminlte-date-range name="created_date_start" igroup-size="sm" :config="$startConfig">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-date-range>
        </div>
        <div class="col-2">
            <label>&nbsp;</label>
            <x-adminlte-date-range name="created_date_end" igroup-size="sm" :config="$endConfig">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-date-range>
        </div>
    </div>
    <div class="row col-6">
        <div class="col-2">
            <x-button type="submit" name="查詢"  />
        </div>
        <div class="col-4">
            <x-button type="button" name="取消查詢" addClass="reset" />
        </div>
    </div>
    {{ Form::close() }}
    <div id="templates" class="hide"></div>
@stop

@section('create')
    <button type="button" class="btn btn-block btn-success btn-flat float-right px-4 createBtn">新增</button>
@stop

@section('data_list')
    @foreach ($datas as $key => $data)
        <tr>
            <td class="align-middle">{{ $data->id }}</td>
            <td class="align-middle">{{ $data->name }}</td>
            <td class="align-middle">{{ $data->en_name }}</td>
            <td class="align-middle">
                @if($data->type == 1)
                    @if($data->is_contracted == 1)
                        <i style="color: green;" class="fa fa-check"></i>
                    @else
                        <i style="color: #b80000;" class="fa fa-times"></i>
                    @endif
                @endif
            </td>
            <td class="align-middle">{{ $data->MasterTypeName }}</td>
            <td class="align-middle">{!! $data->StatusCss !!}</td>
            <td class="align-middle">
                @can('masters-edit')
                    <button type="button" class="btn btn-primary btn-flat mr-4 editBtn" data-id="{{$data->id}}">編輯</button>
                @endcan
            </td>
        </tr>
    @endforeach
@stop

@section('modal')
    @include('masters.masters.modal')
    @include('components.gallery_modal')
@stop

@section('css')
@stop

@section('js')
    <script>
        $('#templates').append('<div id="experiences_template">'+$('#experiences_template').html()+'</div>')
        $('#templates').append('<div id="expertise_template">'+$('#expertise_template').html()+'</div>')
        $('#templates').append('<div id="divisions_template">'+$('#divisions_template').html()+'</div>')

        $('#editForm #experiences_template').remove()
        $('#editForm #expertise_template').remove()
        $('#editForm #divisions_template').remove()

        function initExperiences() {
            let template = $('#experiences_template')
            template.find('.plus').removeClass('hide')
            template.find('.minus').addClass('hide')
            $('#experiences_group').append(template.html())
        }

        function initExpertise() {
            let template = $('#expertise_template')
            template.find('.plus').removeClass('hide')
            template.find('.minus').addClass('hide')
            $('#expertise_group').append(template.html())
        }

        function initDivision() {
            let template = $('#divisions_template')
            template.find('.plus').removeClass('hide')
            template.find('.minus').addClass('hide')
            $('#divisions_group').append(template.html())
            $('#divisions_group .select2').select2()
        }

        function changeType(value) {
            if (value == 1) {
                $('.type_doctor').show()
            } else {
                $('.type_doctor').hide()
            }
        }

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

        $('#type').change(function () {
            changeType($(this).val())
        })

        $(document).ready(function() {
            //  Model hide 事件
            $('#editModal').on('hidden.bs.modal', function () {
                reset_modal();
            })

            $('.createBtn').on('click', function() {
                reset_modal();
                changeType(1)
                initExperiences()
                initExpertise()
                initDivision()
                $('#editModal').modal({
                    backdrop:"static",
                    keyboard:false,
                    show: true
                });
            })

            $('.editBtn').on('click', function() {
                var edit_id = $(this).data('id');
                var editUrl = "{{url()->current()}}/"+ edit_id + '/edit';
                $.get(editUrl, function (data) {
                    reset_modal();

                    $('#editModalTitle').html('編輯專家');
                    $('#id').val(edit_id);
                    $('#name').val(data.name);
                    $('#en_name').val(data.en_name);
                    $('#type').val(data.type);
                    $('#status').val(data.status);
                    $('#image').val(data.image);
                    $('#content_image').val(data.content_image);
                    $('#description').val(data.description);
                    $('#institution_id').val(data.institution_id).change();
                    $('#title').val(data.title);
                    changeType($('#type').find(":selected").val())

                    if (data.divisions.length > 0) {
                        let template = $('#divisions_template')
                        template.find('.minus').removeClass('hide')
                        template.find('.plus').addClass('hide')

                        $.each(data.divisions, function (index, item) {
                            temp = template.clone()
                            if (index === 0) {
                                temp.find('.plus').removeClass('hide')
                                temp.find('.minus').addClass('hide')
                            }
                            temp.find('.dynamic_text option[value='+item.division_id+']').attr('selected','selected')
                            temp.find('.dynamic_job').attr('value', item.description)
                            $('#divisions_group').append(temp.html())
                        })
                        $('#divisions_group .select2').select2()
                    } else {
                        initDivision()
                    }

                    if (data.experiences.length > 0) {
                        let template = $('#experiences_template')
                        template.find('.minus').removeClass('hide')
                        template.find('.plus').addClass('hide')

                        $.each(data.experiences, function (index, item) {
                            temp = template.clone()
                            if (index === 0) {
                                temp.find('.plus').removeClass('hide')
                                temp.find('.minus').addClass('hide')
                            }
                            temp.find('.dynamic_text').attr('value', item.name)
                            temp.find('.dynamic_job option[value='+item.is_current_job+']').attr('selected','selected')
                            $('#experiences_group').append(temp.html())
                        })
                    } else {
                        initExperiences()
                    }

                    if (data.expertise.length > 0) {
                        let template = $('#expertise_template')
                        template.find('.minus').removeClass('hide')
                        template.find('.plus').addClass('hide')

                        $.each(data.expertise, function (index, item) {
                            temp = template.clone()
                            if (index === 0) {
                                temp.find('.plus').removeClass('hide')
                                temp.find('.minus').addClass('hide')
                            }
                            temp.find('.dynamic_text').attr('value', item.name)
                            $('#expertise_group').append(temp.html())
                        })
                    } else {
                        initExpertise()
                    }

                    $('#is_contracted').val(data.is_contracted).change();
                    if($("#image").val()){
                        $("#review_image").attr("src",$("#image").val());
                        $("#review_image_div").show();
                    }
                    if($("#content_image").val()){
                        $("#review_content_image").attr("src",$("#content_image").val());
                        $("#review_content_image_div").show();
                    }
                    $('#editModal').modal({
                        backdrop:"static",
                        keyboard:false,
                        show: true
                    });
                });
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

            $("#review_image_click").click(function(){
                if($("#image").val()){
                    $("#review_image").attr("src",$("#image").val());
                    $("#review_image_div").show();
                }
            })

            $("#review_content_image_click").click(function(){
                if($("#content_image").val()){
                    $("#review_content_image").attr("src",$("#content_image").val());
                    $("#review_content_image_div").show();
                }
            })

            $('#gallery_image').on('click', function() {
                var parentId = 'image';
                $('#galleryModal iframe').attr('data-parent', parentId );
                $('#galleryModal').modal('show');
            })

            $('#gallery_content_image').on('click', function() {
                var parentId = 'content_image';
                $('#galleryModal iframe').attr('data-parent', parentId );
                $('#galleryModal').modal('show');
            })

            window.addEventListener("message", (e) => {
                if(e.origin !== 'tvbs.com.tw' || e.origin !== '127.0.0.1') {
                    $("#" + e.data.parent).val(e.data.imgUrl);
                    $(".gallery-modal-header > .close").trigger('click');
                    $("#review_image_div").hide();
                }
            });

            // 取消查詢
            $('.reset').click(function () {
                location.href = "{{route('masters.index')}}"
            });
        });
    </script>

    <script>
        function reset_modal() {
            $('#validateMsg').html('');
            $('#validateMsg').css('display', 'none');
            $('#editModalTitle').html('新增專家');
            $('#id').val('');
            $('#name').val('');
            $('#en_name').val('');
            $('#image').val('');
            $('#content_image').val('');
            $('#description').val('');
            $('#institution_id').val(0).change();
            $('#title').val('');
            $('#experiences_group').html('')
            $('#expertise_group').html('')
            $('#divisions_group').html('')
            $('#status').val(1);
            $('#type').val(1);
            $('#is_contracted').val(1);
            $('#review_image_div img').attr('src', '');
            $('#review_content_image_div img').attr('src', '');
            $('#description').attr('rows', '3');
            $("#is_contracted").val(1);
        }

        function saveAction() {
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

                    $('#editModal').find('.modal-body').animate({
                        scrollTop: 0
                    }, 'slow');

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
