function ajax_fileupload()
{
    $(".ajaxfileupload").each(function() { 
        set_fileupload($(this));
    });
}

function set_fileupload(obj)
{
    var init_files = [];
    var initialPreview = [];
    var initialPreviewConfig = [];
    
    if(obj.val()){
        init_files = JSON.parse(obj.val());
    }
    init_files.forEach(function(file,i) {
        if(parseInt(file.indexOf("http")) > -1){
            initialPreview.push("<img style='max-width:100%;max-height:160px;' src='"+file+"'>");
        } else {
            initialPreview.push("<img style='max-width:100%;max-height:160px;' src='"+$("#img_src").val()+"/storage/"+obj.attr("folder")+"/"+file+"'>");
        }
        var preview = new Object;
        preview.caption = file;
        preview.width = "120px";
        preview.url = $("#img_src").val()+"/shared/fileupload/delete_file";
        preview.key = file;
        preview.extra = {folder:obj.attr("folder")};
        initialPreviewConfig.push(preview);
    });
    var setting = {
        language: "zh-TW",
        uploadUrl: $("#img_src").val()+"/shared/fileupload/upload_file",
        allowedFileExtensions: ["jpg", "jpeg", "png", "gif"],
        uploadAsync: true,
        overwriteInitial: false,
        validateInitialCount: true,
        showUpload: false, // hide upload button
        showRemove: false, // hide remove button
        showCancel: false,
        showClose: false,
        initialPreview: initialPreview,
        initialPreviewConfig: initialPreviewConfig,
        uploadExtraData: {
            folder: obj.attr("folder"),
            verify: obj.attr("verify"),//自訂驗證規則
            max_size: obj.attr("max_size"),//自訂最大尺寸限制
        },
    };

    f_html = $('<input type="file" accept="image/*" />');
    
    //是否為多檔上傳
    if(obj.attr("multiple"))
    {
        f_html.prop("multiple" , true);
        if(obj.attr("maxFileCount"))
            setting["maxFileCount"] = parseInt(obj.attr("maxFileCount"));
    }
    else
        setting["maxFileCount"] = 1;
        
    obj.after(f_html);
    var f_obj = obj.next();
    f_obj.fileinput(setting).on("filebatchselected", function(event, files) {
        f_obj.fileinput("upload");
    }).on('filedeleted', function(event, key) {
        new_init_files = [];
        init_files.forEach(function(file,i) {
            if(file != key)
                new_init_files.push(file);
        });
        init_files = new_init_files;
        obj.val(JSON.stringify(init_files));
    }).on('filesorted', function(event, key) {
        new_init_files = [];
        key.stack.forEach(function(file,i) {
            new_init_files.push(file.key);
        });
        init_files = new_init_files;
        obj.val(JSON.stringify(init_files));
    }).on('fileuploaded', function(event, data, previewId, index) {
        var files = data.response.initialPreviewConfig;
        files.forEach(function(file) {
            init_files.push(file.key);
        });
        obj.val(JSON.stringify(init_files));
    });
}