{!! Form::input('date', $value['name'], old($value['name']) ? old($value['name']) : (isset($data)?$data[$value['name']]:date('Y-m-d', time())), ['class' => isset($value['class'])?'form-control col-12 col-sm-6 col-md-4 '.$value['class']:'form-control col-12 col-sm-6 col-md-4','id'=>isset($value['id'])?$value['id']:'', isset($value['required']) && $value['required'] == 1 ? 'required' : '']) !!}