<div class="form-group row">
    <label class="col-sm-2 col-form-label" for="{{ $k }}">
        {{$v['title']}}
        @if($v['required'])
            <span class="text-danger">*</span>
        @endif
    </label>
    @php
        $master_value = (!empty($v['value'])) ? $v['value'][0] : '';
    @endphp
    <div class="col-sm-4">
        <select class="form-control" name="master_type" id="master_type">
            <option value="">無</option>
            <option value="1" {{old('master_type', $master_value)==1?'selected':''}}>醫師</option>
            <option value="2" {{old('master_type', $master_value)==2?'selected':''}}>專家</option>
            <option value="3" {{old('master_type', $master_value)==3?'selected':''}}>營養師</option>
        </select>
    </div>
    <div class="col-sm-4 masters">
        <x-adminlte-select2 class="form-control" id="talent_category_id" name="talent_category_id[]">
            <option value="">暫無資料</option>
        </x-adminlte-select2>
    </div>
    <div class="col-sm-2 masters_up_scale_down">
        <div class="fa fa-solid fa-plus master_plus" style="color: #007bff; cursor: pointer;"></div>
    </div>
</div>
