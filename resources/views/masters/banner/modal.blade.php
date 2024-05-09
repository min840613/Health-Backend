<!-- Modal Start-->
<div class="modal fade" id="editModal" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <div id="validateMsg" class="alert alert-danger" style="display: none;">
                </div>
                <form id="editForm" name="editForm" class="form-horizontal" onsubmit="return false;">
                    {{ csrf_field() }}
                    <input type="hidden" name="editId" id="editId">

                    @foreach ($editField as $key => $value)
                        @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                        @if($value['type'] !== 'hidden')
                        <div class="form-group col-12 {!! isset($value['main_class'])?$value['main_class']:'' !!}">
                            <label class="input_{{ $value['name'] }} @error($value['name']) text-red @enderror"
                                for="{{ $value['name'] }}"><span class="label_text">{{ $value['title'] }}</span>
                                @if ( (isset($value['required']) && $value['required'] == true) || (isset($_GET['parent_id']) && (isset($value['sub_required']) && $value['sub_required'] == true)) )
                                    <span class="text-danger">*</span>
                                @endif
                                @if (isset($value['hint']))
                                    <span class="text-danger">{{ $value['hint'] }}</span>
                                @endif
                            </label>
                            @if(isset($value['warning_word']) && !empty($value['warning_word']))
                                <span class="text-danger"><small>{{ $value['warning_word'] }}</small></span>
                            @endif
                        @endif
                        @endfeature

                        @switch($value['type'])
                            @case('show')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                @include('components.show_only')
                                @endfeature
                            @break

                            @case('select')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)

                                    @include('components.select')

                                @endfeature
                            @break

                            @case('adminlte_select')
                                <x-adminlte-select2 name="{{$value['name']}}" id="{{$value['name']}}" igroup-size="md">
                                    @foreach($value['option'] as $k => $v)
                                        <option value="{{$k}}">{{$v}}</option>
                                    @endforeach
                                </x-adminlte-select2>
                            @break

                            @case('show_select')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                @include('components.show_select')
                                @endfeature
                            @break

                            @case('textarea')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                @include('components.textarea')
                                @endfeature
                            @break

                            @case('text')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)

                                @if(isset($value['image_gallery']) && $value['image_gallery'] == true)
                                    <div class="form-inline">
                                        @include('components.text')
                                        <div>
                                            <a id="gallery_{{$value['name']}}" style='margin-left: 10px;' class="btn btn-success btn-flat float-left px-4" href="javascript:;">圖庫</a>
                                            <a id="review_{{$value['name']}}_click" class="btn btn-success btn-flat float-left px-4 ml-4" href="javascript:;">預覽</a>
                                        </div>
                                    </div>
                                    <div id="review_{{$value['name']}}_div" class="col-sm-12 mt-4" style="display: none;">
                                        <img id="review_{{$value['name']}}" style="width: 100%" src="" />
                                    </div>
                                    @if($value['comment'])
                                        <div class="col-sm-2">
                                        </div>
                                        <div class="col-sm-10">
                                            <span style="color: red">備註：{{$value['comment']}}</span>
                                        </div>
                                    @endif
                                @else
                                    @include('components.text')
                                @endif

                                @if (isset($value['search']) && $value['search'] !== '')
                                    {!! Form::button('搜尋', [
                                        'class' => 'search_' . $value['name'] . ' mt-2 btn btn-outline-info btn-flat',
                                        'data-id' => $value['name'],
                                    ]) !!}
                                    @include($value['search'])
                                @endif
                                @endfeature
                            @break

                            @case('croppie')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                @include('components.croppie')
                                @endfeature
                            @break

                            @case('cropic')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                @include('components.cropic')
                                @endfeature
                            @break

                            @case('fileupload')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                    @include('components.fileupload')
                                @endfeature
                            @break

                            @case('date')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                @include('components.date')
                                @endfeature
                            @break

                            @case('custome-date-start')
                                @php
                                    $StartDateConfig = ['format' => 'YYYY-MM-DD HH:mm'];
                                @endphp
                                <x-adminlte-input-date name="{{$value['name']}}" value="{{date('Y-m-d H:i')}}" :config="$StartDateConfig">
                                    <x-slot name="appendSlot">
                                        <div class="input-group-text bg-gradient-info">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    </x-slot>
                                </x-adminlte-input-date>
                            @break

                            @case('custome-date-end')
                                @php
                                    $EndDateConfig = ['format' => 'YYYY-MM-DD HH:mm'];
                                @endphp
                                <x-adminlte-input-date name="{{$value['name']}}" value="{{date('Y-m-d 23:59',strtotime('+1 week'))}}" :config="$EndDateConfig">
                                    <x-slot name="appendSlot">
                                        <div class="input-group-text bg-gradient-info">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    </x-slot>
                                </x-adminlte-input-date>
                            @break

                            @case('datetime')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                @include('components.datetime_local')
                                @endfeature
                            @break

                            @case('start-end')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                @include('components.start_end')
                                @endfeature
                            @break

                            @case('custom')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                @include($value['custom'])
                                @endfeature
                            @break

                            @case('hidden')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                {!! Form::input('hidden', $value['name'], $value['value']) !!}
                                @endfeature
                            @break

                            @default
                            @break
                        @endswitch
                        @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                        @if($value['type'] !== 'hidden')
                            </div>
                        @endif
                        @endfeature
                    @endforeach
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                <button id="saveBtn" type="button" class="btn btn-primary">儲存</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal End-->
