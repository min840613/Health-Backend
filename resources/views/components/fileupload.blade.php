{!! Form::input('hidden', $value['name'], 
isset($data[$value['name']]) && $data[$value['name']] != '' ? '["' . $data[$value['name']] . '"]' : null, 
[
    'class' => 'file ajaxfileupload',
    'data-theme' => 'fas',
    'data-min-file-count' => $value['set']['data-min-file-count'],
    'folder' => $value['set']['folder'],
    'verify' => isset($value['set']['verify']) ? $value['set']['verify'] : '',
    isset($value['required']) && $value['required'] == 1 ? 'required' : '',
]) !!}