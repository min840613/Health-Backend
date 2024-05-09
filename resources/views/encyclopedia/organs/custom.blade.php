<!-- SVG -->
<div class="form-group col-12 custom_drop_area" >
    <label class="input_name " for="name" style="width: 16%;margin-left: 5vw;">
        <span class="label_text">icon</span>
        <span class="text-danger">*</span>
        <br><span class="text-danger"><small>圖片尺寸30x30</small><br><small>圖片類型svg</small></span>
    </label>

    <div class="custom_drop_content">
        <div id="upload_zone_svg" class="upload_zone" data-type="svg">
            <p class="tip">請拖曳圖檔到此</p>
        </div>
        <span class="text-danger">請將本機圖片拖曳到上方，或點選</span><br>
        <input type="file"  id="fileUploaderSvg" onchange="handleFiles(this.files, 'svg')"/>
    </div>
</div>

<!-- PNG -->
<div class="form-group col-12 custom_drop_area" >
    <label class="input_name " for="name" style="width: 16%;margin-left: 5vw;">
        <span class="label_text">Android PNG Icon：</span>
        <span class="text-danger">*</span>
        <br><span class="text-danger"><small>圖片尺寸90x90</small><br><small>圖片類型png</small></span>
    </label>

    <div class="custom_drop_content">
        <div id="upload_zone_png" class="upload_zone" data-type="png">
            <p class="tip">請拖曳圖檔到此</p>
        </div>
        <span class="text-danger">請將本機圖片拖曳到上方，或點選</span><br>
        <input type="file"  id="fileUploaderPng" onchange="handleFiles(this.files, 'png')"/>
    </div>
</div>

<!-- PDF -->
<div class="form-group col-12 custom_drop_area" >
    <label class="input_name " for="name" style="width: 16%;margin-left: 5vw;">
        <span class="label_text">IOS PDF Icon：</span>
        <span class="text-danger">*</span>
        <br><span class="text-danger"><small>圖片尺寸30x30</small><br><small>圖片類型pdf</small></span>
    </label>

    <div class="custom_drop_content" >
        <div id="pdf_content">
        </div>
        <input type="file"  id="fileUploaderPdf" />
    </div>
</div>

<div class="float-left mr-3 col-3">
    <label>上下架狀態<span class="text-danger">*</span></label>
</div>
<x-adminlte-select name="status" class="col-3" igroup-size="sm">
    <option value="1" selected>上架</option>
    <option value="0">下架</option>
</x-adminlte-select>


<style>
.custom_drop_area{
    display: flex;
    align-items: center;
    font-size: 0.8rem;
}
.custom_drop_content{
    margin-left: 1.5vw;
    display: inline-block;
}
.tip{
    font-size: 0.5rem;
}
.upload_zone {
    width: 400px;
    height: 150px;
/*    cursor: pointer;*/
    background-color: #eee;
    align-items: center;
    justify-content: center;
    min-height: 100px;
    display: flex;
    z-index: 999;
    transition: background-color .2s ease-in-out
}

.upload_zone_enter {
    border: 1px dashed black;
    background-clip: content-box;
    background-color: #999;
}

.preview_image {
/*    width: 75%;
    height: 75%;*/
    object-fit: contain;
    max-height: 150px;
}

</style>