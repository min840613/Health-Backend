<rss xmlns:media="{{$link}}" version="2.0">
    <channel>
        <title>{{$title}}</title>
        <description>{{$description}}</description>
        <link>{{$link}}</link>
        <lastBuildDate>{{$today}}</lastBuildDate>
        <pubDate>{{$today}}</pubDate>
        <language>{{$language}}</language>
        @foreach ($articles as $article)
            <item>
                <title>{{ $article['title'] }}</title>
                <media:thumbnail url="{{ $article['image'] }}" />
                <link>{{ $article['url'] }}</link>
                <pubDate>{{$today}}</pubDate>
                <category>{{ $article['category_name'] }}</category>
            </item>
        @endforeach
    </channel>
</rss>
