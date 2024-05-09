<!-- Modal Start-->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-
labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalTitle">新增常用圖片</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <div id="validateMsg" class="alert alert-danger" style="display: none;">
                </div>
                <form id="editForm" name="editForm" class="form-horizontal" onsubmit="return false;">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">

                    @foreach ($editField as $key => $value)
                        @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                        @if($value['type'] !== 'hidden')
                            <div class="form-group col-12 {!! isset($value['main_class'])?$value['main_class']:'' !!}">
                                <label class="input_{{ $value['name'] }} @error($value['name']) text-red @enderror"
                                    for="{{ $value['name'] }}">
                                    <span class="label_text">{{ $value['title'] }}</span>
                        @if ( (isset($value['required']) && $value['required'] == true) || (isset($_GET['parent_id']) && (isset($value['sub_required']) && $value['sub_required'] == true)) )
                            <span class="text-danger">*</span>
                        @endif
                        @if (isset($value['hint']))
                            <span class="text-danger">{{ $value['hint'] }}</span>
                        @endif
                                </label>
                        @endif
                        @endfeature

                        @switch($value['type'])
                            @case('text')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                    {!! Form::input('text',
                                                    $value['name'],
                                                    '',
                                                    ['placeholder' => isset($value['placeholder']) ? $value['placeholder'] : $value['name'],
                                                    'class' => isset($value['class']) ? 'form-control ' . $value['class'] : 'form-control',
                                                    'id' => isset($value['id']) ? $value['id'] : '',
                                                    ((isset($value['required']) && $value['required'] == 1)) ? 'required' : '']) !!}
                                    @if ($value['id'] == 'url')
                                    <div class="form-group row"></div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <a id="gallery_image" class="btn btn-success btn-flat float-left px-4 master-img" href="javascript:;">圖庫</a>
                                            <a id="review_image_click" class="btn btn-success btn-flat float-left px-4 ml-4" href="javascript:;">預覽</a>
                                        </div>
                                    </div>
                                    <div id="review_image_div" class="col-sm-12 mt-4" style="display: none;">
                                        <div class="row">
                                            <label class="col-sm-2"></label>
                                            <div class="col-sm-10">
                                                <img id="review_image" style="height: 100px;" src="" />
                                            </div>
                                        </div>
                                    </div>

                                    @endif
                                @endfeature
                            @break
                           @case('custome-date-start-common')
                                <x-adminlte-date-range name="{{$value['name']}}" >
                                    <x-slot name="appendSlot">
                                    <div class="input-group-text bg-dark">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    </x-slot>
                                </x-adminlte-date-range>
                                
                            @break

                            @case('custome-date-end-common')
                                <x-adminlte-date-range name="{{$value['name']}}" >
                                    <x-slot name="appendSlot">
                                    <div class="input-group-text bg-dark">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    </x-slot>
                                </x-adminlte-date-range>
                                
                            @break
                            @case('select')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                    @include('components.select')
                                @endfeature
                            @break

                            @case('textarea')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                    @include('components.textarea')
                                @endfeature
                            @break

                            @case('datetime')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                @include('components.datetime_local')
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
