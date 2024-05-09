<div class="col-md-12">
    <div class="card">
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {!! Form::open(['route' => $route, 'method' => $method, 'files' => true, 'id' => 'formMain']) !!}
        <div class="card-body row">
            {!! Form::input('hidden', 'img_src', url('/'), ['id' => 'img_src']) !!}
            @foreach ($field as $key => $value)
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
                            @include('components.text')
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
        </div>
        <div class="card-footer col-12">
            @if(isset($_GET['keywords']))
            {!! Form::input('hidden', 'keywords', isset($_GET['keywords'])?$_GET['keywords']:NULL) !!}
            @endif
            @if(isset($_GET['page']))
            {!! Form::input('hidden', 'page', isset($_GET['page'])?$_GET['page']:NULL) !!}
            @endif
            @if(isset($_GET['parent_id']))
            {!! Form::input('hidden', 'parent_id', isset($_GET['parent_id'])?$_GET['parent_id']:NULL) !!}
            @endif
            <button class="on_submit btn btn-primary btn-flat mr-4">儲存</button>
            <a href="{{ route($site_name.'.index',['page' => isset($_GET['page'])?$_GET['page']:NULL,'parent_id' => isset($_GET['parent_id'])?$_GET['parent_id']:NULL,'keywords' => isset($_GET['keywords'])?$_GET['keywords']:NULL]) }}">
                <button type="button" class="btn btn-outline-info btn-flat">返回</button>
            </a>
        </div>
        {!! Form::close() !!}
    </div>
</div>