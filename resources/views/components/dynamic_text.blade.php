<div id="{{$value['id']}}_template" class="hide">
    <div class="row">
        <div class="col-11">{!! $value['elements'] !!}</div>
        <div class="col-1 plus">
            <a href="#"><i class="fa fa-solid fa-plus"></i></a>
        </div>
        <div class="col-1 minus hide">
            <a href="#"><i class="fa fa-solid fa-minus"></i></a>
        </div>
    </div>
</div>

<div id="{{$value['id']}}_group"></div>

@push('css')
    <style>
        .hide {
            display: none;
        }
    </style>
@endpush

@push('js')
    <script>
        $(document).ready(function () {
            $('#{{$value['id']}}_group').html($('#{{$value['id']}}_template').html())

            let template = $('#{{$value['id']}}_template')
            template.find('.minus').removeClass('hide')
            template.find('.plus').addClass('hide')

            $('#{{$value['id']}}_group').on('click', '.plus', function () {
                template.find('.minus').removeClass('hide')
                template.find('.plus').addClass('hide')
                if ({{$value['max_count'] ??  0}} > 0 && $('#{{$value['id']}}_group .dynamic_text').length >= {{$value['max_count'] ?? 0}}){
                    Swal.fire({
                        title: '超過可增加上限',
                        icon: 'error',
                        confirmButtonText: `確定`,
                        confirmButtonColor: '#f87e6c',
                    })
                    return
                }
                $('#{{$value['id']}}_group').append(template.html())
                $('#{{$value["id"]}}_group .select2').select2()
            })
            $('#{{$value['id']}}_group').on('click', '.minus', function () {
                $(this).parents('.row').remove()
            })
        })
    </script>
@endpush
