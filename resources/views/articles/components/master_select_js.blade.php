<script>
    expertUpScaleDown()

    @php
        $masterIds = [];
        if(!empty(old('talent_category_id'))){
            $masterIds = old('talent_category_id');
        }else{
            $masterIds = isset($ArticleData) && $ArticleData["masters"]->isNotEmpty() ? $ArticleData["masters"]->pluck('id')->toArray() : [null];
        }
    @endphp

    if ($("#master_type").val()) {
        $.ajax({
            url: '{{ route("articles.get.master_list") }}',
            type: "get",
            data: {'master_type': $("#master_type").val()},
            dataType: 'json',
            success: function (result) {
                if (result) {
                    var options = '<option value="0">請選擇</option>';
                    var masterIds = @json($masterIds);

                    $.each(result, function (key, value) {
                        valueData = $("#master_type").val() == 1 && value.institution != null ? value.name + '(' + value.institution.nick_name + ')' : value.name
                        if (value.id == masterIds[0]) {
                            options = options + '<option value="' + value.id + '" selected>' + valueData + '</option>';
                        } else {
                            options = options + '<option value="' + value.id + '">' + valueData + '</option>';
                        }
                    })

                    $("#talent_category_id").html(options);

                    if ($("#master_type").val() == 1 && masterIds.length > 1) {
                        masterIds.shift()
                        $.each(masterIds, function (key, value) {
                            appendExpert(value)
                        })
                    }
                }
            }
        });
    }

    $("#master_type").on('change', function () {
        expertUpScaleDown()
        $.ajax({
            url: '{{ route("articles.get.master_list") }}',
            type: "get",
            data: {'master_type': $(this).val()},
            dataType: 'json',
            success: function (result) {
                if (result) {
                    var options = '<option value="">請選擇</option>';
                    $.each(result, function (key, value) {
                        valueData = $("#master_type").val() == 1 && value.institution != null ? value.name + '(' + value.institution.nick_name + ')' : value.name
                        options = options + '<option value="' + value.id + '">' + valueData + '</option>';
                    })
                    $("#talent_category_id").html(options);
                }
            }
        });
        $('.masters .form-group:not(:first)').remove()
    })

    function expertUpScaleDown() {
        if ($('#master_type').val() == 1) {
            $('.masters_up_scale_down').show()
        } else {
            $('.masters_up_scale_down').hide()
        }
    }

    function appendExpert(selectedId = null) {
        var options = $('#talent_category_id').clone()

        if (selectedId !== null) {
            options.find(':selected').removeAttr('selected');
            options.find(`[value="${selectedId}"]`).attr('selected', 'selected')
        }

        $('.masters').append(`
                    <x-adminlte-select2 class="form-control experts" id="talent_category_id" name="talent_category_id[]">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fa fa-ellipsis-v" aria-hidden="true" style="font-size:15px;cursor:all-scroll;"></i>
                        </div>
                    </x-slot>
                    <x-slot name="appendSlot">
                        <div class="input-group-text close">
                            <i class="fa fa-times" aria-hidden="true" style="font-size: 70%; cursor:pointer; color:red;"></i>
                        </div>
                    </x-slot>
                    ` + options.html() + `
                    </x-adminlte-select2>
                `)
        $('.experts').select2()
        $('.masters').sortable()
    }

    $('.master_plus').click(function () {
        appendExpert()
    })

    $(document).on('click', '.masters .close', function () {
        $(this).parent('.input-group-append').parent('.input-group').parent('.form-group').remove()
    })
</script>
