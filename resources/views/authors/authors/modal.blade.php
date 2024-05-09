<!-- Modal Start-->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-
labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalTitle">新增上稿者</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <div id="validateMsg" class="alert alert-danger" style="display: none;">
                </div>
                <form id="editForm" name="editForm" class="form-horizontal" onsubmit="return false;">
                    {{ csrf_field() }}
                    <input type="hidden" name="author_id" id="author_id">

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
                                @endfeature
                            @break

                            @case('select')
                                @feature(isset($value['feature'])?$value['feature']:'nofeature',isset($value['feature'])?true:false)
                                    @include('components.select')
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
