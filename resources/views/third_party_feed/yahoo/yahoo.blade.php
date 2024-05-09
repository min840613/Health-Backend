<?xml version = "1.0" encoding = "UTF-8" ?>
<rss xmlns:media="http://search.yahoo.com/mrss/" xmlns:content="http://purl.org/rss/1.0/modules/content/" version="2.0" data-livestyle-extension="available">
    <channel>
        <title>{{$site_name}}</title>
        <link>{{$domain}}</link>
        <description>{{$description}}</description>
        <pubDate>{{$time}}</pubDate>
        @foreach($articles as $article)
            <item>
                <title>{{$article['title']}}</title>
                <link>{{$article['link']}}</link>
                <description>{{$article['description']}}</description>
                <pubDate>{{$article['publish_date']}}</pubDate>
                <guid>{{$article['guid']}}</guid>
                <category>{{$article['category_name']}}</category>
                @if (!empty($article['image_url']))
                    <media:content url="{{$article['image_url']}}" type="image/jpeg"/>
                @endif
                <content:encoded>
                    <![CDATA[
                        {!! $article['content'] !!}
                    ]]>
                </content:encoded>
            </item>
        @endforeach
    </channel>
</rss>
