<div class="row col-12">
    {!! Form::input('datetime-local', 'start_at', (old('start_at'))?old('start_at'):(isset($data->start_at) ? $data->start_at : date('Y-m-d\TH:i')), ['class' => isset($value['class'])?'form-control col-6 '.$value['class']:'form-control col-6', 'id' => isset($value['id'])?$value['id'][0]:'', isset($value['required']) && $value['required'] == 1 ? 'required' : '']) !!}

    {!! Form::input('datetime-local', 'end_at', (old('end_at'))?old('end_at'):(isset($data->end_at) ? $data->end_at : date('Y-m-d\TH:i')), ['class' => isset($value['class'])?'form-control col-6 '.$value['class']:'form-control col-6', 'id' => isset($value['id'])?$value['id'][1]:'', isset($value['required']) && $value['required'] == 1 ? 'required' : '']) !!}
</div>