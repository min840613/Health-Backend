@php
    $customSelect2Config = isset($value['required']) && $value['required'] == 1 ? 'required' : '';
@endphp
<x-adminlte-select2 class="form-control" id="{{$value['id']}}" name="{{$value['name']}}" :config="$customSelect2Config">
    @foreach($value['option'] as $id => $option)
        <option value="{{$id}}">{{$option}}</option>
    @endforeach
</x-adminlte-select2>
