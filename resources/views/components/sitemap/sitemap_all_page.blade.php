{{header('Content-type: text/xml');}}
<?xml version="1.0" encoding="UTF-8"?>
    <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
        @foreach( $sitemap_arr as $k => $v)
            <sitemap><loc>{{$v}}</loc></sitemap>
        @endforeach
    </sitemapindex>

