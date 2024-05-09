{{header('Content-type: text/xml');}}
<?xml version="1.0" encoding="UTF-8"?>
    <urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<!-- created with Free Online Sitemap Generator www.xml-sitemaps.com -->

@foreach( $urls_arr as $k => $v)
    <url>
        <loc>{{$v}}</loc>
        @if (isset($image_arr) && $image_arr[$k])
        <image:image>
            <image:loc>{{$image_arr[$k]}}</image:loc>
        </image:image>
        @endif
        <lastmod>{{date(DATE_W3C, strtotime($updated_at_arr[$k]))}}</lastmod>
        <changefreq>hourly</changefreq>
    </url>
@endforeach

</urlset>
