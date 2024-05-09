@extends('adminlte::page')

@section('title', 'APP搖一搖 - 修改')

@section('content_header')
    <h1>APP搖一搖 - 修改</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            {!! Form::open(['url' => route('shake.update', ['shake' => $shake->shake_id]),'method'=>'PUT']) !!}
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">活動類型</label>
                    <div class="col-sm-10">
                        <x-adminlte-select name="shake_type" igroup-size="sm" required>
                            <option value="2">一般活動</option>
                        </x-adminlte-select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">開啟類型</label>
                    <div class="col-sm-10">
                        <x-adminlte-select name="shake_content_type" igroup-size="sm" required>
                            @if ( is_numeric(old('shake_content_type')) )
                                <option value="1" {{old('shake_content_type') == 1 ? 'selected' : ''}}>網址</option>
                                <option value="2" {{old('shake_content_type') == 2 ? 'selected' : ''}}>文章ID</option>
                            @else
                                <option value="1" {{$shake->shake_content_type == '1' ? 'selected' : ''}}>網址</option>
                                <option value="2" {{$shake->shake_content_type == '2' ? 'selected' : ''}}>文章ID</option>
                            @endif
                        </x-adminlte-select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required" name="shake_url_label">輸入URL</label>
                    <div class="col-sm-10">
                        @if ( !empty(old('shake_url')) )
                            <x-adminlte-input name="shake_url" igroup-size="sm" value="{{old('shake_url')}}"  required/>
                        @else
                            <x-adminlte-input name="shake_url" igroup-size="sm" value="{{$shake->shake_url}}" required/>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">是否為EC連結</label>
                    <div class="col-sm-10">
                        <x-adminlte-select name="is_ec_connect" igroup-size="sm">
                            @if ( is_numeric(old('is_ec_connect')) )
                                <option value="1" {{old('is_ec_connect') == 1 ? 'selected' : ''}} >是</option>
                                <option value="0" {{old('is_ec_connect') == 0 ? 'selected' : ''}}>否</option>
                            @else
                                <option value="1" {{$shake->is_ec_connect == '1' ? 'selected' : ''}}>是</option>
                                <option value="0" {{$shake->is_ec_connect == '0' ? 'selected' : ''}}>否</option>
                            @endif
                        </x-adminlte-select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">標題</label>
                    <div class="col-sm-10">
                        @if ( !empty(old('shake_title')) )
                            <x-adminlte-input name="shake_title" igroup-size="sm" value="{{old('shake_title')}}" required/>
                        @else
                            <x-adminlte-input name="shake_title" igroup-size="sm" value="{{$shake->shake_title}}" required/>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">文案內容</label>
                    <div class="col-sm-10">
                        @if ( !empty(old('content')) )
                            <x-adminlte-input name="content" igroup-size="sm" value="{{old('content')}}" required/>
                        @else
                            <x-adminlte-input name="content" igroup-size="sm" value="{{$shake->content}}" required/>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">活動開始時間</label>
                    <div class="col-sm-10">
                        @if(\Carbon\Carbon::parse($shake['shake_time_start'])->lessThan(now()))
                            <label>{{$shake['shake_time_start']}}</label>
                            <input type="hidden" name="shake_time_start" value="{{$shake['shake_time_start']}}">
                        @else
                            @if ( old('shake_time_start') )
                                <input type="hidden" id="shake_time_start_origin" value="{{old('shake_time_start')}}">
                            @else
                                <input type="hidden" id="shake_time_start_origin" value="{{$shake['shake_time_start']}}">
                            @endif
                                <x-adminlte-date-range name="shake_time_start" igroup-size="sm" autocomplete="off">
                                    <x-slot name="appendSlot">
                                    <div class="input-group-text bg-dark">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    </x-slot>
                                </x-adminlte-date-range>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">活動結束時間</label>
                    <div class="col-sm-10">
                        @if(\Carbon\Carbon::parse($shake['shake_time_end'])->lessThan(now()))
                            <label>{{$shake['shake_time_end']}}</label>
                            <input type="hidden" name="shake_time_end" value="{{$shake['shake_time_end']}}">
                        @else
                            @if ( old('shake_time_end') )
                                <input type="hidden" id="shake_time_end_origin" value="{{old('shake_time_end')}}">
                            @else
                                <input type="hidden" id="shake_time_end_origin" value="{{$shake['shake_time_end']}}">
                            @endif
                                <x-adminlte-date-range name="shake_time_end" igroup-size="sm" >
                                    <x-slot name="appendSlot">
                                    <div class="input-group-text bg-dark">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    </x-slot>
                                </x-adminlte-date-range>
                            
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">活動狀態</label>
                    <div class="col-sm-10">
                        <x-adminlte-select name="shake_status" igroup-size="sm">
                            @if ( is_numeric(old('shake_status')) )
                                <option value="1" {{old('shake_status') == 1 ? 'selected' : ''}} >發佈</option>
                                <option value="0" {{old('shake_status') == 0 ? 'selected' : ''}}>下架</option>
                            @else
                                <option value="1" {{$shake->shake_status == '1' ? 'selected' : ''}}>發佈</option>
                                <option value="0" {{$shake->shake_status == '0' ? 'selected' : ''}}>下架</option>
                            @endif
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
    @include('components.gallery_modal')
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
        init('init', $('[name=shake_content_type]').val())

        function init(type, target) {
            switch (target) {
                case '1':
                    $('[name=shake_url_label]').html('輸入URL')
                    break
                case '2':
                    $('[name=shake_url_label]').html('文章ID')
                    break
            }
            if(type !== 'init'){
                $('[name=shake_url]').val('')
            }
        }

        $(document).ready(function () {

           $('input[name="shake_time_start"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                startDate:  $("#shake_time_start_origin").val(),
                timePicker: true,
                timePicker24Hour: true,
                cancelButtonClasses: "btn-danger",
                locale: {
                    format: 'YYYY-MM-DD HH:mm'
                }
            });
           $('input[name="shake_time_end"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                startDate:  $("#shake_time_end_origin").val(),
                timePicker: true,
                timePicker24Hour: true,
                cancelButtonClasses: "btn-danger",
                locale: {
                    format: 'YYYY-MM-DD HH:mm'
                }
            });
            $('[name=shake_content_type]').on('change', function () {
                init('action', $(this).val())
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
