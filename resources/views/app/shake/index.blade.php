@extends('components.index')

@section('title')
    APP-搖一搖
@stop

@section('content_header')
    <h1>APP-搖一搖</h1>
    {{ Form::open(['route' => 'shake.index', 'method' => 'get', 'style' => 'border:1px solid #ccc; padding: 5px;']) }}
    @php
        $startConfig = [
            "singleDatePicker" => true,
            "showDropdowns" => true,
            "startDate" => request()->input('search_shake_time_start') ?? "js:moment().subtract(14, 'days')",
            "minYear" => 2000,
            "maxYear" => "js:parseInt(moment().format('YYYY'),10)+5",
            "cancelButtonClasses" => "btn-danger",
            "locale" => ["format" => "YYYY-MM-DD"],
            "autoUpdateInput" => false,
            "autoApply" => true,
        ];
        $endConfig = [
            "singleDatePicker" => true,
            "showDropdowns" => true,
            "startDate" => request()->input('search_shake_time_end') ?? "js:moment()",
            "minYear" => 2000,
            "maxYear" => "js:parseInt(moment().format('YYYY'),10)+5",
            "cancelButtonClasses" => "btn-danger",
            "locale" => ["format" => "YYYY-MM-DD"],
            "autoUpdateInput" => false,
            "autoApply" => true,
        ];
    @endphp
    <div class="row col-6">
        <div class="col-4">
            <label>活動ID</label>
            <x-adminlte-input name="search_shake_id" igroup-size="sm"
                              value="{{request()->input('search_shake_id')}}"/>
        </div>
    </div>
    <div class="row col-6">
        <div class="col-4">
            <label>活動名稱</label>
            <x-adminlte-input name="search_shake_title" igroup-size="sm"
                              value="{{request()->input('search_shake_title')}}"/>
        </div>
    </div>
    <div class="row col-6" style="padding-top: 10px">
        <div class="col-4">
            <label>開始時間</label>
            <x-adminlte-date-range name="search_shake_time_start" autocomplete="off" igroup-size="sm" :config="$startConfig">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-date-range>
        </div>
        <div class="col-4">
            <label>&nbsp;</label>
            <x-adminlte-date-range name="search_shake_time_end" autocomplete="off" igroup-size="sm" :config="$endConfig">
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
@stop

@section('create')
    <a href="{{route('shake.create')}}" class="btn btn-sm btn-success">新增</a>
    <a href="#" class="btn btn-sm btn-info" id="download">活動報表匯出</a>
@stop

@section('data_list')
    @php
        $shakeTypeMappings = [1 => '電視活動', 2 => '一般活動'];
    @endphp
    @foreach ($datas as $key => $shake)
        @php
            $isInTime = now()->lessThan($shake['shake_time_end']);
        @endphp
        <tr>
            <td class="align-middle">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="shake_id"
                           value="{{$shake['shake_id']}}">
                    <label class="form-check-label" for="flexCheckDefault"></label>
                </div>
            </td>
            <td class="align-middle">{{$shake['shake_id']}}</td>
            <td class="align-middle">{{$shake['shake_title']}}</td>
            <td class="align-middle">{{$shake['content']}}</td>
            <td class="align-middle">{{$shakeTypeMappings[$shake['shake_type']]}}</td>
            <td class="align-middle">{{$shake['shake_time_start']}}</td>
            <td class="align-middle">{{$shake['shake_time_end']}}</td>
            <td class="align-middle">{{$shake['membersCount']}}</td>
            <td class="align-middle">
                {!!$shake['shake_status'] === 1 && $isInTime ? '<i style="color: green;" class="fa fa-check"></i>' : '<i style="color: #b80000;" class="fa fa-times"></i>'!!}
            </td>
            <td class="align-middle" width="180px">
                <a href="{{route('shake.edit', ['shake' => $shake['shake_id']])}}" class="btn btn-sm btn-primary">編輯</a>
                @if(now()->lessThan($shake['shake_time_end']))
                    <a href="#" class="btn btn-sm btn-secondary"
                       onclick="notifications('{{route('notifications.create', ['shake_id' => $shake['shake_id']])}}')">推播</a>
                @endif
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
        function openBlank(url) {
            const a = document.createElement('a')
            a.href = url
            a.target = '_blank'
            a.click()
        }

        // 推播
        function notifications(url) {
            const question = confirm('是否加入推播?')

            if (question) {
                openBlank(url)
            }
        }
        $(document).ready(function () {
            let shakeIds;

            // 全選
            $('.select-all').click(function () {
                $('input[name="shake_id"]').each(function (i) {
                    $(this).click()
                });
            })

            // 下載
            $('#download').click(function () {
                shakeIds = [];
                $('input[name="shake_id"]:checked').each(function (i) {
                    shakeIds.push($(this).val())
                });

                location.href = '{{route('shake.download')}}?shake_ids=' + shakeIds
            })

            // 取消查詢
            $('.reset').click(function () {
                location.href = "{{route('shake.index')}}"
            })

            $('input[name="search_shake_time_start"]').val('{{request()->input('search_shake_time_start')}}')
            $('input[name="search_shake_time_end"]').val('{{request()->input('search_shake_time_end')}}')

            $('input[name="search_shake_time_start"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD'));
            });
            $('input[name="search_shake_time_end"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD'));
            });
        })
    </script>
@stop

