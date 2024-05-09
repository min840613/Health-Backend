<div id="crop_{{$value['name']}}"
style="background-image: url({{ old($value['name']) ? old($value['name']) : (isset($data[$value['name']])?$data[$value['name']]:'') }})" class="border">
</div>
{!! Form::input('hidden', $value['name'], isset($data[$value['name']]) ? $data[$value['name']] : '', ['class' => isset($value['class'])?'form-control '.$value['class']:'form-control', 'id' => 'cropOutput_'.$value['name']]) !!}