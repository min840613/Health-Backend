@extends('components.index')

@section('title')
    推播管理
@stop

@section('content_header')
    <h1>推播管理</h1>
    {{ Form::open(['route' => 'notifications.index', 'method' => 'get', 'style' => 'border:1px solid #ccc; padding: 5px;']) }}
    @php
        $statusBadgeMappings = [
            1 => 'secondary',
            2 => 'success',
            3 => 'warning',
            4 => 'danger',
            5 => 'info',
        ];

        $startConfig = [
            "singleDatePicker" => true,
            "showDropdowns" => true,
            "startDate" => request()->input('push_date_start') ?? "js:moment().subtract(14, 'days')",
            "minYear" => 2000,
            "maxYear" => "js:parseInt(moment().format('YYYY'),10)+5",
            "cancelButtonClasses" => "btn-danger",
            "locale" => ["format" => "YYYY-MM-DD"],
        ];
        $endConfig = [
            "singleDatePicker" => true,
            "showDropdowns" => true,
            "startDate" => request()->input('push_date_end') ?? "js:moment()",
            "minYear" => 2000,
            "maxYear" => "js:parseInt(moment().format('YYYY'),10)+5",
            "cancelButtonClasses" => "btn-danger",
            "locale" => ["format" => "YYYY-MM-DD"],
        ];
    @endphp
    <div class="row col-10" style="padding-top: 10px">
        <div class="col-2">
            <label>推播時間</label>
            <x-adminlte-date-range name="push_date_start" igroup-size="sm" :config="$startConfig">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-date-range>
        </div>
        <div class="col-2">
            <label>&nbsp;</label>
            <x-adminlte-date-range name="push_date_end" igroup-size="sm" :config="$endConfig">
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
            <label>推播狀態</label>
            <x-adminlte-select2 name="push_notifications_status" igroup-size="sm">
                @foreach($filters['push_notifications_status'] as $code => $status)
                    <option
                        value="{{$code}}" {{request()->input('push_notifications_status') === (string)$code ? 'selected' : ''}}>{{$status}}</option>
                @endforeach
            </x-adminlte-select2>
        </div>
        <div class="col-2">
            <label>推播類別</label>
            <x-adminlte-select2 name="type" igroup-size="sm">
                @foreach($filters['type'] as $code => $type)
                    <option
                        value="{{$code}}" {{request()->input('type') === (string)$code ? 'selected' : ''}}>{{$type}}</option>
                @endforeach
            </x-adminlte-select2>
        </div>
    </div>

    <div class="row col-10">
        <div class="col-2">
            <label>推播ID</label>
            <x-adminlte-input name="id" igroup-size="sm"
                              value="{{request()->input('id')}}"/>
        </div>
        <div class="col-2">
            <label>推播標題</label>
            <x-adminlte-input name="message" igroup-size="sm" value="{{request()->input('message')}}"/>
        </div>
        <div class="col-1">
            <label>&nbsp;</label>
        </div>
        <div class="col-2">
            <label>創建者</label>
            <x-adminlte-input name="created_user" igroup-size="sm"
                              value="{{request()->input('created_user')}}"/>
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
    <a href="{{route('notifications.create')}}" class="btn btn-success">新增</a>
@stop

@section('data_list')
    @foreach ($datas as $key => $notification)
        <tr>
            <td class="align-middle">{{$notification['id']}}</td>
            <td class="align-middle">{{$notification['created_user']}}</td>
            <td class="align-middle">
                <label class="badge badge-{{$statusBadgeMappings[$notification['push_notifications_status']]}}">
                    {{$filters['push_notifications_status'][$notification['push_notifications_status']]}}
                </label>
            </td>
            <td class="align-middle" width="100px">
                <label>{{$filters['type'][$notification['type']]}}</label>
            </td>
            <td class="align-middle">{{$notification['message']}}</td>
            <td class="align-middle">{{$notification['member_group']}}</td>
            <td class="align-middle">{{$notification['created_at']}}</td>
            <td class="align-middle">{{$notification['prepush']}}</td>
            <td class="align-middle" width="180px">
                @if($notification['push_notifications_status'] === \App\Enums\NotificationsStatus::PENDING && $notification['pushed'] === null && \Carbon\Carbon::parse($notification['pushed'])->lt(now()))
                    <a href="{{route('notifications.edit', ['notification' => $notification->id])}}" class="btn btn-sm btn-primary">編輯</a>
                    <a href="#" class="btn btn-sm btn-secondary"
                       onclick="destroy('{{route('notifications.destroy', ['notification' => $notification["id"]])}}')">取消推播</a>
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
        function destroy(url) {
            if (confirm('是否取消推播？')) {
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
                }).then((response) => {
                    if (response.status === '00000') {
                        alert('取消推播成功！')
                        window.location.reload()
                    }
                })
            }
        }

        $(document).ready(function () {
            // 取消查詢
            $('.reset').click(function () {
                location.href = "{{route('notifications.index')}}"
            })
        })
    </script>
@stop

