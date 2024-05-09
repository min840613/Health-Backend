<script>
    $.fn.tinymce = function (options) {
        const settings = $.extend({
            token: 'token'
        }, options)

        tinymce.init({
            language: 'zh-Hant',
            selector: 'textarea#' + $(this).attr('id'),
            menubar: false,
            skin: settings.skin,
            content_css: settings.content_css,
            contextmenu: "cut | copy | paste | link image inserttable",
            content_style: 'body { font-family: sans-serif, Arial, Verdana, "Trebuchet MS"; }' +
                'ol[style="list-style-type: numbercircle;"] {list-style: none; overflow: hidden; counter-reset: numList;} ' +
                'ol[style="list-style-type: numbercircle;"] li {position: relative;} ' +
                'ol[style="list-style-type: numbercircle;"] li:before { counter-increment: numList; content: counter(numList); float: left; position: absolute; left: -26px; font: bold 18px sans-serif; text-align: center; color: #FFF; line-height: 18px; width: 18px; height:18px; background: #209c98; -moz-border-radius: 999px; border-radius: 999px; }',
            plugins: [
                'advlist', 'autolink', 'link', 'image', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
                'searchreplace', 'wordcount', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'media',
                'table', 'emoticons', 'template', 'help'
            ],
            paste_preprocess: function (plugin, args) { //貼上前處理，將帖上內容的 class & id 全部拿掉
                var elem = $('<div>' + args.content + '</div>');
                elem.find('*').removeAttr('id').removeAttr('class');
                args.content = elem.html();
                args.content = args.content.replace(/<br>/g, '');
                args.content = args.content.replace(/<\/br>/g, '');
            },
            link_default_target: '_blank',
            paste_as_text: false,
            toolbar1: 'code | undo redo | styles h1 h2 h3 removeformat | forecolor backcolor bold italic | alignleft aligncenter alignright alignjustify',
            toolbar2: 'emoticons | bullist numlist outdent indent | link image media galleries | preview fullscreen | visualblocks',
            link_rel_list: [
                {title: 'None', value: ''},
                {title: 'No Follow', value: 'nofollow'},
                {title: 'No Opener', value: 'noopener'},
                {title: 'No Referrer', value: 'noreferrer'},
                {title: 'External Link', value: 'external'}
            ],
            advlist_number_styles: 'default,lower-roman,upper-roman,lower-alpha,upper-alpha,numbercircle',
            image_caption: true,
            image_title: true,
            images_upload_url: '/image-gallery/tinymce?_token=' + settings.token,
            image_list: '/image-gallery/favorite',
            // automatic_uploads: true,
            file_picker_callback: (cb, value, meta) => {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');

                input.addEventListener('change', (e) => {
                    const file = e.target.files[0];

                    const reader = new FileReader();
                    reader.addEventListener('load', () => {
                        /*
                          Note: Now we need to register the blob in TinyMCEs image blob
                          registry. In the next release this part hopefully won't be
                          necessary, as we are looking to handle it internally.
                        */
                        const id = 'blobid' + (new Date()).getTime();
                        const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                        const base64 = reader.result.split(',')[1];
                        const blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);

                        /* call the callback and populate the Title field with the file name */
                        cb(blobInfo.blobUri(), {title: file.name.replace(/\.[^/.]+$/, "")});
                    });
                    reader.readAsDataURL(file);
                });

                input.click();
            },
            file_picker_types: 'file image media',
            setup: (editor) => {
                editor.ui.registry.addButton('galleries', {
                    icon: 'gallery',
                    onAction: () => editor.windowManager.openUrl({
                        title: 'Image Galleries',
                        url: '/image-gallery?top=tinymce',
                    })
                })
            },
        });
    }
</script>
