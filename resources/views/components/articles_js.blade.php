<script>
	function historylist(){
        if (history.length > 1) {
            history.back();
        } else {
            window.close();                
        }
    }

    function ajaxChangeCategories(label, categories_id, subcategories_id = '') {
        subcategories_item = label.children('div').children('div').eq(1).children('select.subcategories');
        $.ajax({
            url:'{{ route("articles.change.categories") }}',
            type: "POST",
            data: {'categories_id':categories_id,"_token":$("input[name='_token']").val()},
            dataType: 'json',
            async: false,
            success: function(result){
                if(result.length > 0){
                    var option = "<option value=''>請選擇</option>";
                    $.each(result, function(index, value){
                        if(subcategories_id == value.sub_categories_id){
                            option = option + "<option value='" + value.sub_categories_id + "' selected>" + value.name + "</option>";
                        } else {
                            option = option + "<option value='" + value.sub_categories_id + "'>" + value.name + "</option>";
                        }
                    })
                    subcategories_item.html(option);
                } else {
                    var option = "<option value='0'>無子分類</option>";
                    subcategories_item.html(option);
                }
            }
        });
    }

    // 延伸閱讀
    function ajaxSearchFurther(form_data, article_id, categories_id) {
        $(".searchFurther").hide();
        $("#further_loading").show();
        $.ajax({
            url: "{{ route('articles.get.furtherdata') }}",
            type: "POST",
            data: {
                'form_data': form_data,
                "article_id": article_id,
                "categories_id": categories_id,
                "_token":$("input[name='_token']").val()
            },
            dataType: 'json',
            success: function(result){
                $("#further_reading").val(result.ids);
                ajaxGetFurtherLoading(result.ids);
            }
        });
    }
    function ajaxGetFurtherLoading(ids) {
        $.ajax({
            url: "{{ route('articles.get.furtherloading') }}",
            type: "GET",
            data: {'further_reading': ids},
            dataType: 'json',
            success: function(result){
                var string = '';
                $.each(result,function(key,value){
                    string = string + '<p style="color:blue"><a href="{{ url('admin/articles_manage/articles') }}/' + value.articles_id + '" target="_blank">' + value.articles_id + ' ' + value.title + '</a></p>';
                })
                $(".review_further_div").children('div').children('div').html(string);
                $(".review_further_div").show();

                $(".searchFurther").show();
                $("#further_loading").hide();
            }
        });
    }

    /* 推薦tag */
    function tag_recommend() {
        var requestURL = "{{ route('articles.get.tag_recommend') }}";
        var editor = tinymce.get('article_content');
        var edata = editor.getContent(); 
        if(edata == ""){
            alert("請輸入內文");
            return false;
        }
        $("#tag_link").empty();
        var dataJSON = { 'article' : edata, "_token" : $("input[name='_token']").val()};
        $.ajax({
            url: requestURL,
            data : dataJSON,                
            type: "POST",
            success: function(returnData){
                if(returnData.status){
                    var result = returnData.data.recomment_tag.split(',');
                    for (var tagKey in result) {
                        if(tagKey < 0){
                                selectizeA.addOption({
                                text: result[tagKey],
                                value: result[tagKey]
                            });
                            selectizeA.addItem(result[tagKey], 'silent');
                        } else {
                            $("#tag_link").append('<input type="button" class="googleKeyword" value='+result[tagKey]+'> ');
                            $("#tag_link").parent('div').show();
                        }
                    }
                }
            }
        });
    }

    //取得YAHOO 延伸閱讀
    function getCategoryArticles(array_categories_id, now_articles_id) {
        var yahoo_ext_value = [];
        yahoo_ext_value =  <?php
            if (!empty($yahoo_ext['value'])) {
                echo json_encode($yahoo_ext['value'], JSON_UNESCAPED_UNICODE);
            } else {
                echo '[]';
            }
        ?>;
        var selected_articles = [];
        selected_articles = <?php
            if (!empty($yahoo_ext['array_value'])) {
                echo json_encode($yahoo_ext['array_value'], JSON_UNESCAPED_UNICODE);
            } else {
                echo '[]';
            }
        ?>;
        var setValue = [];
        $.each(selected_articles, function (item, data) {
            setValue[item] = data.articles_id;
            control.addOption({
                id: data.articles_id,
                title: data.articles_id + ' - ' + data.title
            });
        });
        $.ajax({
            url: '{{ route('articles.get.yahoo_ext') }}',
            type: 'get',
            data: {'category': array_categories_id.join("-"),"articles_id": now_articles_id},
            dataType: 'json',
            success: function (json) {
                $option = '<option value="">請選擇</option>';
                $.each(json, function (key, data) {
                    $option = $option + '<option value="' + data.articles_id + '">' + data.title + '</option>';
                });
                $("#yahoo_ext").html($option);
            }
        });
    }

    // 文章資料
    function getArticlesForYahoo() {
        articles_id = $("#yahoo_ext_data").val();
        if(articles_id) {
            $.ajax({
                url: '{{ route("articles.get.ArticlesYahoo") }}',
                type: 'get',
                data: {'articles_id': articles_id},
                dataType: 'json',
                success: function (result) {
                    $("#yahoo_ext_list").html(result);
                    $("#yahoo_ext_list").parent('div').show();
                    removeArticle();
                }
            });
        } else {
            $("#yahoo_ext_list").parent('div').hide();
        }
    }

    function removeArticle() {
        $(".removeArticle").unbind('click');
        $(".removeArticle").click(function(){
            var ext_data = $("#yahoo_ext_data").val();
            var ext_data_arr = ext_data.split(',');
            ext_data_arr.splice(jQuery.inArray($(this).attr('ids'),ext_data_arr),1);
            ext_data = ext_data_arr.join(',');
            $("#yahoo_ext_data").attr('value',ext_data);
            getArticlesForYahoo();
        })
    }

    function initMedicine(){
        // 取得主分類ID
        var categories = $(".categories").serializeArray().filter(function(item) {
            return item.name === 'categories[]';
        }).map(function(item) {
            return item.value;
        }).filter(function(val) {
            return val !== "" && val !== null && val !== undefined;
        });
        if(categories.indexOf("{{$Encyclopedia['categories_id']}}") >= 0){
            $("#medicine_article_sickness").show();
            $("#medicine_article_categories").show();
        } else {
            $("#medicine_article_sickness").hide();
            $("#medicine_article_categories").hide();
        }
    }

    function ajaxHealthOrgans(body_id){
        $.ajax({
            url: '{{ route("articles.get.health_organs") }}',
            type: 'get',
            data: {"body_id":body_id},
            dataType: 'json',
            success: function(result) {
                var OrganList = '<option value="">請選擇</option>';
                $.each(result, function(key, value) {
                    OrganList = OrganList + '<option value="'+ value.id +'">'+ value.name +'</option>';
                })
                $("#medicine_article_sickness_Two").html(OrganList);
            }
        });
    }

    function ajaxHealthSickness(organs_id) {
        $.ajax({
            url: '{{ route("articles.get.health_sickness") }}',
            type: 'get',
            data: {"organs_id":organs_id},
            dataType: 'json',
            success: function(result) {
                var SicknessList = '<option value="">請選擇</option>';
                $.each(result, function(key, value) {
                    SicknessList = SicknessList + '<option value="'+ value.id +'">'+ value.name +'</option>';
                })
                $("#medicine_article_sickness_Three").html(SicknessList);
            }
        });
    }

    function ajaxAddArticleSickness(sickness_id) {
        const medicine_article_sickness_id = $("#medicine_article_sickness_id").val().split(',');
        if(medicine_article_sickness_id.indexOf(sickness_id) < 0){
            medicine_article_sickness_id.push(sickness_id);
            let new_arr = [];
            $.each(medicine_article_sickness_id, function(key, value){
                if(value != '' && value != null && value != undefined){
                    new_arr.push(value);
                }
            })
            $("#medicine_article_sickness_id").attr('value',new_arr.join(','));
            ajaxLoadArticleSickness();
        } else {
            alert('此項目已加入');
        }
    }

    function ajaxLoadArticleSickness() {
        var medicine_article_sickness_id = $("#medicine_article_sickness_id").val();
        if(medicine_article_sickness_id){
            $.ajax({
                url: '{{ route("articles.load.article_sickness") }}',
                type: 'get',
                data: {'sickness_id':medicine_article_sickness_id},
                dataType: 'json',
                success: function(result) {
                    var str = '';
                    $.each(result, function(key, value){
                        if(str){
                            str = str + "，<a class='medicine_article_sickness_remove' href='javascript:;' ids='" + value.id + "'>" + value.name + "</a>";
                        } else {
                            str = "<a class='medicine_article_sickness_remove' href='javascript:;' ids='" + value.id + "'>" + value.name + "</a>";
                        }
                    })
                    $("#medicine_article_sickness_list").parent('div').parent('div').show();
                    $("#medicine_article_sickness_list").html(str);
                    $(".medicine_article_sickness_remove").click(function(){
                        var sickness_id = $(this).attr('ids');
                        if(sickness_id){
                            ajaxRemoveArticleSickness(sickness_id);
                        }
                    })
                    if(!str){
                        $("#medicine_article_sickness_list").parent('div').parent('div').hide();
                    }
                }
            });
        } else {
            $("#medicine_article_sickness_list").parent('div').parent('div').hide();
        }
    }

    function ajaxRemoveArticleSickness(sickness_id) {
        const medicine_article_sickness_id = $("#medicine_article_sickness_id").val().split(',');
        var index = $.inArray(sickness_id,medicine_article_sickness_id);
        medicine_article_sickness_id.splice(index,1);
        str = medicine_article_sickness_id.join();
        $("#medicine_article_sickness_id").attr('value',str);
        ajaxLoadArticleSickness();
    }

    function ajaxContentExtendReadingPageUrl(pageUrl, nowjson) {
        $.ajax({
            url: pageUrl,
            type: "get",
            data: {'nowjson':nowjson},
            dataType: 'json',
            success: function(result){
                var TableData = '';
                $.each(result.data, function(key, value){
                    TableData = TableData + "<tr><td><input class='embedChk' type='checkbox' name='chk[]' value='" + value.articles_id + "' " + (value.isCheck?'checked':'') + "></td><td>" + value.articles_id + "</td><td>" + value.title + "</td><td>" + value.publish + "</td></tr>";
                });
                $("#searchArticleTable").html(TableData);
                var LinksData = '<ul class="pagination">';
                $.each(result.links, function(key, value){
                    var active = "";
                    if(value.active){
                        active = "active";
                    }
                    LinksData = LinksData + "<li class='page-item " + active + "'><a class='page-link' href='" + value.url + "'>" + value.label + "</a></li>";
                });
                LinksData = LinksData + '</ul>';

                // 處理NowJson
                var CheckArrJSON = $("#NowJson").val();
                if(CheckArrJSON){
                    var CheckArr = JSON.parse(CheckArrJSON);
                } else {
                    var CheckArr = [];
                }
                $('input.embedChk').on('change', function() {
                    var value = $(this).val();
                    if ($(this).is(':checked')) {
                        CheckArr.push(value);
                    } else {
                        var index = CheckArr.indexOf(value);
                        if (index !== -1) {
                            CheckArr.splice(index, 1);
                        }
                    }
                    var CheckArrJSON = JSON.stringify(CheckArr);
                    $("#NowJson").attr('value',CheckArrJSON);
                    clickPageLink();
                    clickSendEmbedCheck();
                });
                // 處理NowJson End

                $(".embedNavigation").html(LinksData);
                $(".embedNavigation").show();
                clickPageLink();
                clickSendEmbedCheck();
            }
        });
    }

    function clickPageLink() {
        var nowjson = $("#NowJson").val();
        $(".page-link").unbind("click");
        $(".page-link").on("click", function(){
            ajaxContentExtendReadingPageUrl($(this).attr("href") + "&nowjson=" + nowjson);
            return false;
        })
    }

    function clickSendEmbedCheck() {
        var content_extended_dialog_ids_json = $("#NowJson").val();
        if(content_extended_dialog_ids_json.length != 0){
            content_extended_dialog_ids = JSON.parse(content_extended_dialog_ids_json);
        } else {
            content_extended_dialog_ids = [];
        }
        $("#sendEmbedCheck").unbind("click");
        $("#sendEmbedCheck").on("click", function () {
            if (content_extended_dialog_ids.length) {
                var extendedText = $("#extended_reading").val();
                var extendedArr = extendedText.split(",");
                $.each(content_extended_dialog_ids, function(index, value){
                    if(extendedArr && extendedArr.indexOf(value) == -1){
                        extendedArr.push(value);
                    }
                })
                extendedArr = extendedArr.filter(function(val) {
                    return val !== "" && val !== null && val !== undefined;
                });
                extendedText = extendedArr.join(',');
                $("#extended_reading").val(extendedText);
                ajaxChangeExtendedReading(extendedText);
                extendedText = '';
            }
            $("#ModalKeywords").val("");
            $('.closeModal').click();
        });
    }

    function ajaxChangeExtendedReading(article_ids){
    	$.ajax({
            url:'{{ route("articles.change.extendedreading") }}',
            type: "GET",
            data: {'articles_ids':article_ids},
            dataType: 'json',
            success: function(result){
            	var HtmlText = '';
            	var TinyMCEData = '<p class="endtext">';
            	$.each(result,function(key, value){
            		if(HtmlText == ''){
            			HtmlText = '<p><a href="https://health.tvbs.com.tw/nutrition/' + value.articles_id + '" target="_blank">' + value.articles_id + '：' + value.title + '</a></p>';
            			TinyMCEData = '<strong>看更多：<a href="https://health.tvbs.com.tw/nutrition/' + value.articles_id + '" target="_blank">' + value.title + '</a></strong><br />';
            		} else {
            			HtmlText = HtmlText + '<p><a href="javascript:;" target="_blank">' + value.articles_id + '：' + value.title + '</a></p>';
            			TinyMCEData = TinyMCEData + '<strong>看更多：<a href="https://health.tvbs.com.tw/nutrition/' + value.articles_id + '" target="_blank">' + value.title + '</a></strong><br />';
            		}
            	})
            	var TinyMCEData = TinyMCEData + '</p>';
            	$("#CreateEmbedData").html(TinyMCEData);
            	$(".review_embed_div").children('div').children('div').html(HtmlText);
            	$(".review_embed_div").show();
            }
        });
    }
</script>