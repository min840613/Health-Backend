<?xml version="1.0" encoding="UTF-8" standalone="no"?>
    <articles>
        <UUID>{{ $UUID }}</UUID>
        <time>{{ $time }}</time>
        @foreach ($articles as $article)
            <article>
                <ID>{{ $article['article_id'] }}</ID>
                <nativeCountry>TW</nativeCountry>
                <language>zh</language>
                <startYmdtUnix>{{ $article['rss_created'] }}</startYmdtUnix>
                <endYmdtUnix>2524579200000</endYmdtUnix>
                <title>{{ $article['title'] }}</title>
                <category>{{ $article['category_name'] }}</category>
                <publishTimeUnix>{{ $article['rss_created'] }}</publishTimeUnix>
                <updateTimeUnix>{{ $article['rss_updated'] }}</updateTimeUnix>
                <contentType>0</contentType>
                <contents>
                    <image>
                        <url>{{ $article['image'] }}</url>
                    </image>
                    <text>
                        <content>
                            <![CDATA[ {!! $article['content'] !!} ]]>
                        </content>
                    </text>
                </contents>
                <sourceUrl>
                    <![CDATA[ {!! $article['url'] !!} ]]>
                </sourceUrl>
            </article>
        @endforeach
    </articles>
