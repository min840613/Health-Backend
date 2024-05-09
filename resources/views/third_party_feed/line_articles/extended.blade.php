<p>
    <a href='https://bit.ly/3qdDVni'>健康資訊不漏接！點我加入【健康2.0 LINE好友】</a>
</p><br/>
@if($extended_articles->first())
    <h3>延伸閱讀：</h3>
    @foreach($extended_articles as $key => $value)
        @if($key > 1)
            @break
        @endif

        <p>
            <a href='{{ config('constants.frontend_url').'/'.$value->mainCategory->en_name.'/'.$value->articles_id.'?utm_source=linetoday&utm_medium=line_readmore&utm_campaign=articleid_'.$value->articles_id }}' target='_blank'>{{ $value->title }}</a>
        </p>
    @endforeach
@endif
