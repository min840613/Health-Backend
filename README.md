## 環境
- Laravel 9
- PHP 8.0

## using package
[Laravel-AdminLTE](https://github.com/jeroennoten/Laravel-AdminLTE)

[Feature Flags for Laravel](https://github.com/ylsideas/feature-flags)

[Carbon](https://carbon.nesbot.com/docs/)

## documents
[AdminLTE](https://adminlte.io/docs/3.0/)

## install
- 從git取檔案：
```
git clone https://github.com/tvbstw/tvbsback_template.git
```
- 根目錄加入 .env 並且調整內容對應至 本機環境（MySQL or MariaDB)
```
cp .env.example .env
```
- 執行 artisan 初始化：
```
php artisan key:generate
```
- 執行 composer 指令：
```
composer install
```
- 安裝 adminlte 套件（碰上重複輸入『yes』）
```
php artisan adminlte:plugins install --plugin=select2 --plugin=datatables --plugin=datatablesPlugins --plugin=daterangepicker --plugin=sweetalert2 --plugin=toastr
```
- 建立 Table 指令：
```
php artisan migrate -seed
```
- 執行 Storage link 指令：
```
php artisan storage:link
```
- 建立權限資料指令：
```
php artisan db:seed --class=PermissionTableSeeder
```
- 建立預設管理者並給予權限指令：
```
php artisan db:seed --class=CreateAdminUserSeeder
```
- 預設登入帳密
帳密：dev001@tvbs.com.tw / dev001dev001dev001

- 建立部門資料庫資料：
```
php artisan db:seed --class=CreateDepartmentSeeder
```
---
- 每次增加 config->adminlte.php 中之 Menu 後，需執行下列命令
```
php artisan EditPermissionForUser:Run
```
---
## 將舊資料庫之資料搬移至新資料庫（一次跑完）
```
php artisan db:seed
```
---

## 將舊資料庫之資料搬移至新資料庫
- heath_authors（上稿者管理）
```
php artisan db:seed --class=HealthAuthorsSeeder
```
- heath_categories（主分類管理）
```
php artisan db:seed --class=HealthCategoriesSeeder
```
- heath_sub_categories（子分類管理）
```
php artisan db:seed --class=HealthSubCategoriesSeeder
```
- heath_menu_list（導覽列管理）
```
php artisan db:seed --class=HealthMenuListSeeder
```
- heath_end_of_text（文末廣宣管理）
```
php artisan db:seed --class=HealthEndOfTextSeeder
```
- health_articles_further_reading（文章總覽-延伸閱讀）
```
php artisan db:seed --class=HealthArticlesFurtherReadingSeeder
```
- heath_articles（文章總覽-網站編輯）
```
php artisan db:seed --class=HealthArticlesSeeder
```
- heath_articles（文章總覽-分類）
```
php artisan db:seed --class=HealthArticleCategoriesMappingsSeeder
```
- heath_body（醫學百科-身體部位）
```
php artisan db:seed --class=HealthBodySeeder
```
- heath_organs（醫學百科-器官組織）
```
php artisan db:seed --class=HealthOrgansSeeder
```
- heath_sickness（醫學百科-疾病）
```
php artisan db:seed --class=HealthSicknessSeeder
```
- health_sickness_to_organ（醫學百科-疾病對應之器官）
```
php artisan db:seed --class=HealthSicknessToOrganSeeder
```
- health_articles_categories（醫學百科-各文章類型）
```
php artisan db:seed --class=HealthArticlesCategoriesSeeder
```
- health_articles_sickness（文章總覽-對應之醫學百科疾病）
```
php artisan db:seed --class=HealthArticlesSicknessSeeder
```
- health_articles_sickness（App-頭條管理）
```
php artisan db:seed --class=HealthAppActivitiesSeeder
```
---
## feature flags
[feature-flags](https://github.com/ylsideas/feature-flags)
---

## croppie
[套件DEMO網站](http://foliotek.github.io/Croppie/)

### step1
載入相關程式
```
<link href="{{ asset('croppie/croppie.css') }}" rel="stylesheet">
```
```
<script type="text/javascript" src="{{ URL::asset('croppie/croppie.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('croppie/croppie_init.js') }}"></script>
```
### step2
參考或直接注入以下blade
```
@include('components.croppie')
```
### step3
```
croppie_init(your ID, width, height,crop_type(square/circle),壓縮比,圖片格式);
```
---
## Form表單共用組件
### 元件
> 共用blade路徑：views/components  
> 主框架：views/components/formbase.blade.php  

### 使用方式
> Step1 : controller宣告
```
public $field = [
        [
            'title' => '分類名稱',
            'type' => 'show',
            'name' => 'category_name',
        ],
        [
            'title' => '播放清單PlayList',
            'type' => 'show',
            'name' => 'playlist_id',
        ],
        ...
        ...
        ];
```
> Step2 : 於對應的create/edit function 叫出傳到view 

> Step3 : 對應的view 表單區塊
```
@include('components.formbase')
```
> 若需要特殊版型也可直接指定相關組件不使用此版型
### 組件說明($field)
＊field 資料順序即為表單輸出順序
參數|選項|說明|搭配選項|必填|預設值
-|-|-|-|-|-
title||label顯示名稱||TRUE
name||input name||TRUE
type||指定選用的版型||TRUE
||text|文字欄位|class/id/placeholder/search|-
||show|僅顯示不可變更的文字欄位|id|-
||textarea|文字輸入框|class/id/placeholder/row/col|-
||fileupload|fileupload套件|set(array)=>data-min-file-count,folder,max_size,verify|-
||cropic|舊裁圖套件|class|-
||croppie|新裁圖套件||-
||date|Y-m-d 格式 |class/id|-
||date|Y-m-d\TH:i:s 格式 |class/id|-
||select|選單|class/id/option|-
||hidden|加入隱藏欄位||-
||custom|套用自訂版型=>include(blade)||-
option(array)||指定select option 陣列內填寫option選項||if(select)=>TRUE
set(array)||陣列內填寫套件設定資訊 data-min-file-count,verify,folder,max_size,||if(fileupload)=>TRUE
||data-min-file-count|指定fileupload套件參數||FALSE
||verify|指定fileupload套件驗證規則||FALSE
||folder|指定fileupload套件暫存資料夾(需跟table同步名稱)||FALSE
||max_size|指定fileupload套件最大上傳尺寸||FALSE
required||欄位是否必填||FALSE|FALSE
hint||欄位提示文字||FALSE
placeholder||指定表單提示文字||FALSE
row||指定textarea rows||FALSE
col||指定textarea cols||FALSE
feature||指定feature flag||FALSE
search||text欄位指定搜尋鈕=>include(blade)||FALSE
class||指定class||FALSE
id||指定id||FALSE

> 若是type指定為custom可自由新增需要的參數  
> 未完善之處請隨時更新修改



### 後台按鈕顏色

#### btn-success
* 查詢

#### btn-outline-success
* 取消查詢

#### btn-secondary
* 預覽

#### btn-primary
* 儲存排序
* 儲存發布
* 編輯
* 首頁頭條
* 推播

### 拖曳按鈕（例如排序）

> 排序拖曳按鈕的classs請加入health_sort（health2.0-backstage/public/css/custom_color.css）
例如```<div class="handle health_sort"><i class="fa fa-fw fa-sort"></i></div>```

### 左側選單
* 第一層統一使用fa fa-bars
* 第二層不使用任何icon





---
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
