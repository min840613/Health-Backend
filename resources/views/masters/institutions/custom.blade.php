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
