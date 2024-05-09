<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'TVBS管理後台',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '後台',
    'logo_img' => 'https://web.tvbs.com.tw/eip/images/login_logo.png',
    'logo_img_class' => 'brand-image',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'TVBS Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => true,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => false,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => false,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'admin',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        [
            'text' => 'search',
            'search' => false,
            'topnav' => true,
        ],
        [
            'text' => '帳號管理',
            'role_name' => 'admin',
            'icon' => 'fa fa-bars',
            'submenu' => [
                [
                    'text' => '權限群組管理',
                    'role_name' => 'admin_roles',
                    'url' => 'admin/roles',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => '使用者管理',
                    'role_name' => 'admin_users',
                    'url' => 'admin/users',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
            ],
        ],
        [
            'text' => 'Feature管理',
            'role_name' => 'feature',
            'icon' => 'fa fa-bars',
            'submenu' => [
                [
                    'text' => 'Feature設定',
                    'role_name' => 'feature_manage',
                    'url' => 'admin/feature/feature_manage',
                    'icon' => '',
                    'shift' => 'ml-2',
                ]
            ],
        ],
        [
            'text' => '圖片管理',
            'role_name' => 'image_manage',
            'icon' => 'fa fa-bars',
            'submenu' => [
                [
                    'text' => '相簿管理',
                    'role_name' => 'image_album',
                    'url' => 'admin/galleries/image_album',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => '常用圖片管理',
                    'role_name' => 'image_favorite',
                    'url' => 'admin/galleries/image_favorite',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
            ],
        ],
        [
            'text' => '影片管理',
            'role_name' => 'video_manage',
            'icon' => 'fa fa-bars',
            'submenu' => [
                [
                    'text' => '分類管理',
                    'role_name' => 'video_album',
                    'url' => 'admin/video/album',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => '影片列表',
                    'role_name' => 'video_list',
                    'url' => 'admin/video/list',
                    'icon' => '',
                    'shift' => 'ml-2',
                ]
            ],
        ],
        [
            'text' => '作者管理',
            'role_name' => 'authors_manage',
            'icon' => 'fa fa-bars',
            'submenu' => [
                [
                    'text' => '上稿者管理',
                    'role_name' => 'authors',
                    'url' => 'admin/authors_manage/authors',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
            ],
        ],
        [
            'text' => '分類管理',
            'role_name' => 'categories_manage',
            'icon' => 'fa fa-bars',
            'submenu' => [
                [
                    'text' => '主分類管理',
                    'role_name' => 'main_categories',
                    'url' => 'admin/categories_manage/main_categories',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => 'WEB 導覽列管理',
                    'role_name' => 'web_menu',
                    'url' => 'admin/categories_manage/web_menu',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => 'APP 導覽列管理',
                    'role_name' => 'app_menu',
                    'url' => 'admin/categories_manage/app_menu',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
            ],
        ],
        [
            'text' => '文章管理',
            'role_name' => 'articles_manage',
            'icon' => 'fa fa-bars',
            'submenu' => [
                [
                    'text' => '文章總覽-網站編輯',
                    'role_name' => 'articles',
                    'url' => 'admin/articles_manage/articles',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => '文章總覽-廣告業務',
                    'role_name' => 'articles_ad',
                    'url' => 'admin/articles_manage/articles_ad',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => '廣編稿管理',
                    'role_name' => 'sponsorlist',
                    'url' => 'admin/articles_manage/sponsorlist',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => '文末廣宣管理',
                    'role_name' => 'end_text',
                    'url' => 'admin/articles_manage/end_text',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
            ],
        ],
        [
            'text' => '首頁區塊管理',
            'role_name' => 'home_area',
            'icon' => 'fa fa-bars',
            'submenu' => [
                [
                    'text' => '頭條管理',
                    'role_name' => 'keyvisual',
                    'url' => 'admin/articles_manage/keyvisual',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => '首頁分類單元管理',
                    'role_name' => 'home_taxon',
                    'url' => 'admin/home_area/home_taxon',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => '小工具量測管理',
                    'role_name' => 'measure',
                    'url' => 'admin/home_area/measure',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => '首頁右側管理',
                    'role_name' => 'right_bar',
                    'url' => 'admin/home_area/right_bar',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => '首頁右側管理-內容置頂',
                    'role_name' => 'right_bar_detail',
                    'url' => 'admin/home_area/right_bar/0/detail',
                    'icon' => '',
                    'shift' => 'ml-2',
                ]
            ],
        ],
        [
            'text' => 'APP管理',
            'role_name' => 'app',
            'icon' => 'fa fa-bars',
            'submenu' => [
                [
                    'text' => '推播管理-網站編輯',
                    'role_name' => 'notifications',
                    'url' => 'admin/articles_app/notifications',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => 'APP-搖一搖',
                    'role_name' => 'shake',
                    'url' => 'admin/articles_app/shake',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => 'APP-頭條管理',
                    'role_name' => 'app_activities',
                    'url' => 'admin/articles_app/app_activities',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => 'APP-版本管理',
                    'role_name' => 'app_versions',
                    'url' => 'admin/articles_app/app_versions',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => 'APP-訊息管理',
                    'role_name' => 'app_news',
                    'url' => 'admin/articles_app/app_news',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => 'APP-Splash管理',
                    'role_name' => 'app_splash',
                    'url' => 'admin/articles_app/app_splash',
                    'icon' => '',
                    'shift' => 'ml-2',
                ]
            ],
        ],
        [
            'text' => '專家管理',
            'role_name' => 'masters_manage',
            'icon' => 'fa fa-bars',
            'submenu' => [
                [
                    'text' => '專家管理',
                    'role_name' => 'masters',
                    'url' => 'admin/masters_manage/masters',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => '科別後台',
                    'role_name' => 'divisions',
                    'url' => 'admin/masters_manage/divisions',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => '醫療院所',
                    'role_name' => 'institutions',
                    'url' => 'admin/masters_manage/institutions',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => '醫級專家 - Banner管理',
                    'role_name' => 'masters_banner',
                    'url' => 'admin/masters_manage/masters_banner',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
            ],
        ],
        [
            'text' => '醫學百科',
            'role_name' => 'encyclopedia_manage',
            'icon' => 'fa fa-bars',
            'submenu' => [
                [
                    'text' => '文章類型',
                    'role_name' => 'article_type',
                    'url' => 'admin/encyclopedia_manage/article_type',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => '疾病名稱',
                    'role_name' => 'sickness',
                    'url' => 'admin/encyclopedia_manage/sickness?body_id=1',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => '器官與組織',
                    'role_name' => 'organs',
                    'url' => 'admin/encyclopedia_manage/organs?body_id=1',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
            ],
        ],
        [
            'text' => '文章供稿',
            'role_name' => 'third_party_feed_manage',
            'icon' => 'fa fa-bars',
            'submenu' => [
                [
                    'text' => 'Line文章供稿條件',
                    'role_name' => 'line_articles_condition',
                    'url' => 'admin/third_party_feed_manage/line_articles_condition',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => 'Line文章供稿',
                    'role_name' => 'line_articles',
                    'url' => 'admin/third_party_feed_manage/line_articles',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => 'Yahoo供稿',
                    'role_name' => 'yahoo_articles',
                    'url' => 'admin/third_party_feed_manage/yahoo_articles',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => 'MixerBox供稿條件',
                    'role_name' => 'mixerbox_articles_condition',
                    'url' => 'admin/third_party_feed_manage/mixerbox_articles_condition',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => 'MixerBox供稿',
                    'role_name' => 'mixerbox_articles',
                    'url' => 'admin/third_party_feed_manage/mixerbox_articles',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
            ],
        ],
        [
            'text' => 'AI 小助理',
            'role_name' => 'deepq',
            'icon' => 'fa fa-bars',
            'submenu' => [
                [
                    'text' => '關鍵字管理',
                    'role_name' => 'deepq_keywords',
                    'url' => 'admin/deepq/keywords',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
                [
                    'text' => 'Banner管理',
                    'role_name' => 'deepq_banners',
                    'url' => 'admin/deepq/banners',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
            ],
        ],
        [
            'text' => 'AI Wize',
            'role_name' => 'aiwize',
            'icon' => 'fa fa-bars',
            'submenu' => [
                [
                    'text' => 'AiWize文章列表',
                    'role_name' => 'aiwize_articles',
                    'url' => 'admin/aiwize/articles',
                    'icon' => '',
                    'shift' => 'ml-2',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        App\Http\Controllers\Admin\Filters\MenuController::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '/vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@10',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
        'TempusDominusBs4' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/moment/moment.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css',
                ],
            ],
        ],
        'TVBS_Health' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '/js/tvbs.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '/js/tinymce6/tinymce.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//code.jquery.com/ui/1.13.2/jquery-ui.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css',
                ],
            ],
        ],
        'custom_color' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '/css/custom_color.css',
                ],
            ],
        ],
        'status_color_change' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '/js/status_color_change.js',
                ],
            ],
        ],
        'DateRangePicker' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/moment/moment.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/daterangepicker/daterangepicker.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/daterangepicker/daterangepicker.css',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
