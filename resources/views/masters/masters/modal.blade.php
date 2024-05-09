<!-- Modal Start-->
<div class="modal fade" id="editModal" role="dialog" aria-
labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
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
                                    @if (in_array($value['id'], ['image', 'content_image']))
                                    <div class="form-group row"></div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <a id="gallery_{{$value['id']}}" class="btn btn-success btn-flat float-left px-4 master-img" href="javascript:;">圖庫</a>
                                            <a id="review_{{$value['id']}}_click" class="btn btn-success btn-flat float-left px-4 ml-4" href="javascript:;">預覽</a>
                                        </div>
                                    </div>
                                    <div id="review_{{$value['id']}}_div" class="col-sm-12 mt-4" style="display: none;">
                                        <div class="row">
                                            <label class="col-sm-2"></label>
                                            <div class="col-sm-10">
                                                <img id="review_{{$value['id']}}" style="height: 100px;" src="" />
                                            </div>
                                        </div>
                                    </div>

                                    @endif
                                @endfeature
                            @break

                            @case('dynamic_text')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                    @include('components.dynamic_text')
                                @endfeature
                            @break

                            @case('select')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                    @include('components.select')
                                @endfeature
                            @break

                            @case('select2')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                    @include('components.select2')
                                @endfeature
                            @break

                            @case('textarea')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                    @include('components.textarea')
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
