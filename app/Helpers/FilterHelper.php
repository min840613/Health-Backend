<?php

namespace App\Helpers;

/**
 * Class ImageHelper
 * @package App\Helpers
 */
class FilterHelper
{
    /* 移除字串裡的link */
    /**
     * @param string $str
     * @return string
     */
    public static function removeLink($str): string
    {
        $regex = '/<a (.*)<\/a>/isU';
        preg_match_all($regex, $str, $result);

        $exclude_regex = '/<blockquote(.*)<\/blockquote>/isU';
        preg_match_all($exclude_regex, $str, $exclude_result);
        $exclude_datas = [];
        foreach($exclude_result[0] as $value){
            preg_match_all($regex, $value, $exclude_data);
            foreach($exclude_data[0] as $data){
                $exclude_datas[] = $data;
            }
        }

        $result = array_diff($result[0],$exclude_datas);

        foreach ($result as $rs) {
            $regex = '/<a (.*)>(.*)<\/a>/isU';
            $text = preg_replace($regex, '$2', $rs);
            $str = str_replace($rs, $text, $str);
        }
        return $str;
    }

    /* 移除endtext */
    /**
     * @param string $str
     * @return string
     */
    public static function removeEndtext($str): string
    {
        $str = preg_replace('/<p class="endtext">([\s\S]*?)<\/p>/','',$str);
        $str = preg_replace('/<p><span class="endtext">([\s\S]*?)<\/span><\/p>/','',$str);
        $str = preg_replace('/<p><span[^>]+class="endtext">([\s\S]*?)<\/span><\/p>/', '', $str);
        $str = preg_replace('(<p[^>]+\><span[^>]+class="endtext">([\s\S]*?)<\/span><\/p>)', '', $str);
        $str = preg_replace('(<div[^>]+class="endtext">([\s\S]*?)<\/div>)', '', $str);
        $str = preg_replace('(<h1><span[^>]+class="endtext">([\s\S]*?)<\/span><\/h1>)', '', $str);
        $str = preg_replace('(<h2><span[^>]+class="endtext">([\s\S]*?)<\/span><\/h2>)', '', $str);
        $str = preg_replace('(<h3><span[^>]+class="endtext">([\s\S]*?)<\/span><\/h3>)', '', $str);
        $str = preg_replace('(<h4><span[^>]+class="endtext">([\s\S]*?)<\/span><\/h4>)', '', $str);
        return $str;
    }

    /* 過濾末段延伸閱讀 */
    /**
     * @param string $str
     * @return string
     */
    public static function removeReadMore($str): string
    {
        $str = preg_replace('/<p><span class="readmore" style="color:#([0-9]*?);">([\s\S]*?)延伸閱讀：([\s\S]*?)<\/p>/','',$str);
        return $str;
    }

    /* 過濾文中看更多 */
    /**
     * @param string $str
     * @return string
     */
    public static function removeReadMoreInContent($str): string
    {
        $str = preg_replace('/<p><strong>看更多：<a[^>]*>([\s\S]*?)<\/a><\/strong><\/p>/', '', $str);
        $str = preg_replace('/<p[^>]*><span[^>]*><strong>看更多：<a[^>]*>([\s\S]*?)<\/a><\/strong><\/span><\/p>/', '', $str);
        $str = preg_replace('/<p[^>]*><span[^>]*><strong>看更多：<a[^>]*>([\s\S]*?)<\/a><\/strong><br><\/span><\/p>/', '', $str);
        $str = preg_replace('/<p[^>]*><span[^>]*><strong>看更多：<a[^>]*>([\s\S]*?)<\/a><\/strong><br><\/span><\/p>/', '', $str);
        $str = preg_replace('/看更多：<a[^>]*>([\s\S]*?)<\/a>/', '', $str);
        return $str;
    }

}
