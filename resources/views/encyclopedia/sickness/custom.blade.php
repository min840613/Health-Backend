

<div>

    <div id="copy_select_model" style="display:flex;">
        <div class="float-left mr-3 col-3">
            <label>身體部位</label>
            <x-adminlte-select  name="create_body_id" class="create_body_id" igroup-size="sm">
                @foreach($filters['bodies'] as $body)
                    <option
                        value="{{$body['id']}}" >{{$body['name']}}</option>
                @endforeach
            </x-adminlte-select>
        </div>

        

        <div class="float-left mr-3 col-3">
            <label>器官組織<span class="text-danger">*</span></label>
            <x-adminlte-select name="create_organ_id" class="create_organ_id" igroup-size="sm">
                <option value="-1">請選擇</option>
            </x-adminlte-select>
        </div>
        <div style="display:flex;align-items: center;">
            <x-button addClass="selectAddBtn" type="button" name="新增" />
        </div>
    </div>

</div>
<style>
.block{
    border-style: solid;
    border-width: thin;
    border-radius: 5px;
    box-shadow: 3px 3px 2px 0px #9b9292;
    width: 50%;
}
</style>
<div id="selectAddBlock" >
<ul id="selectAddContent">
</ul>
</div>

