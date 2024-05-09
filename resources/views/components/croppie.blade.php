<div class="row">
    <div class="col-2 text-center">
        <label class="btn btn-primary"><input class="upload_{{ $value['name'] }}" style="display:none;" type="file" accept="image/*">
        <i class="fas fa-images"></i> 上傳圖片
        </label>
        <label class="crop_{{ $value['name'] }} btn btn-success">
            <i class="fas fa-cut"></i> 裁剪圖片
        </label>
    </div>
    <div class="col-10 text-center">
        <div class="col-12">
            <div id="old_{{ $value['name'] }}" style="display:none;"></div>
        </div>
        <div class="col-12 text-center">
            <div id="new_{{ $value['name'] }}">
                <img src='{!! old($value['name']) ? old($value['name']) : (isset($data[$value['name']]) ? $data[$value['name']] : '') !!}' />
            </div>
        </div>
    </div>
</div>
{!! Form::input('hidden',$value['name'],old($value['name']) ? old($value['name']) : (isset($data[$value['name']]) ? $data[$value['name']] : ''),['id' => 'output_'.$value['name']]) !!}