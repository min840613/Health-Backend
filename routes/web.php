<?php

use App\Http\Controllers\App\NotificationsController;
use App\Http\Controllers\App\ShakeController;
use App\Http\Controllers\Deeq\DeepqKeywordController;
use App\Http\Controllers\Deeq\DeepqBannerController;
use App\Http\Controllers\Encyclopedia\SicknessController;
use App\Http\Controllers\Masters\DivisionsController;
use App\Http\Controllers\Masters\InstitutionsController;
use App\Http\Controllers\Notifications\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
// 套件
use App\Http\Controllers\shared\ImageCropController;
use App\Http\Controllers\shared\UploadFileController;
use App\Http\Controllers\shared\ReorderController;
use App\Http\Controllers\CroppicController;
use App\Http\Controllers\Feature\FeatureController;
//相簿圖片管理
use App\Http\Controllers\Galleries\ImageAlbumController;
use App\Http\Controllers\Galleries\ImageFavoriteController;
// 影片管理
use App\Http\Controllers\Video\VideoAlbumController;
// 作者管理
use App\Http\Controllers\Authors\AuthorsController;
//分類管理
use App\Http\Controllers\Categories\MainCategoriesController;
use App\Http\Controllers\Categories\SubCategoriesController;
use App\Http\Controllers\Categories\WebMenuController;
use App\Http\Controllers\Categories\AppMenuController;
//文章管理
use App\Http\Controllers\Articles\EndTextController;
//首頁區塊管理
use App\Http\Controllers\HomeArea\HomeTaxonController;
//App管理
use App\Http\Controllers\App\AppActivitiesController;
use App\Http\Controllers\App\AppVersionsController;
use App\Http\Controllers\App\AppNewsController;
use App\Http\Controllers\App\AppSplashController;
// 文章管理
use App\Http\Controllers\Articles\ArticlesController;
// 廣編稿管理
use App\Http\Controllers\Articles\SponsorController;
// 首頁區塊管理
use App\Http\Controllers\Articles\KeyvisualController;
use App\Http\Controllers\HomeArea\MeasureController;
use App\Http\Controllers\HomeArea\RightBarController;
use App\Http\Controllers\HomeArea\RightBarDetailController;
// 達人管理
use App\Http\Controllers\Masters\MastersController;
use App\Http\Controllers\Masters\MastersBannerController;
//醫學百科
use App\Http\Controllers\Encyclopedia\ArticleTypeController;
use App\Http\Controllers\Encyclopedia\OrgansController;
// 文章供稿
use App\Http\Controllers\ThirdPartyFeed\LineArticlesController;
use App\Http\Controllers\ThirdPartyFeed\YahooArticlesController;
use App\Http\Controllers\ThirdPartyFeed\MixerboxArticlesController;
// AI Wize
use App\Http\Controllers\AiWize\AiWizeArticlesController;
use App\Http\Controllers\ImageGalleryController;
use App\Http\Controllers\VideoGalleryController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/admin');
});

Auth::routes();
Route::get('/admin', [HomeController::class, 'index'])->name('admin_home');

// Route::prefix('croppic')->group(function () {
//             Route::post('/store', [CroppicController::class, 'store']);
//             Route::post('/crop', [CroppicController::class, 'crop']);
//         });

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::resource('/users', UserController::class);
    Route::resource('/roles', RoleController::class);

    Route::prefix('feature')->group(function () {
        Route::resource('/feature_manage', FeatureController::class);
    });

    Route::prefix('galleries')->group(function () {
        Route::resource('/image_album', ImageAlbumController::class);
    });

    Route::prefix('galleries')->group(function () {
        Route::resource('/image_favorite', ImageFavoriteController::class);
    });

    // 影片管理
    Route::prefix('video')->group(function () {
        Route::resource('/album', VideoAlbumController::class);
        Route::get('/list', [VideoGalleryController::class, 'list'])->name('video.list');
        Route::get('/create', [VideoGalleryController::class, 'create'])->name('video.create');
        Route::get('/list/{id}/edit', [VideoGalleryController::class, 'edit'])->name('video.edit');
        Route::put('/list/{id}', [VideoGalleryController::class, 'update'])->name('video.update');
    });

    Route::prefix('authors_manage')->group(function () {
        Route::middleware('feature:feature_authors')->resource('/authors', AuthorsController::class);
    });

    Route::prefix('categories_manage')->group(function () {
        Route::middleware('feature:feature_main_categories')->resource('/main_categories', MainCategoriesController::class);
        Route::middleware('feature:feature_main_categories')->resource('/main_categories/{main_category}/sub_categories', SubCategoriesController::class);
        Route::middleware('feature:feature_main_categories')->put('/sub_categories/sort', [SubCategoriesController::class, 'sort'])->name('sub_categories.sort');
        Route::middleware('feature:feature_web_menu')->resource('/web_menu', WebMenuController::class);
        Route::middleware('feature:feature_app_menu')->resource('/app_menu', AppMenuController::class);
    });

    Route::prefix('articles_manage')->group(function () {
        // 看更多：找文章
        Route::middleware('feature:feature_articles')->get('/articles/change_extended_reading', [ArticlesController::class, 'ChangeExtendedReading'])->name('articles.change.extendedreading');

        // 看更多
        Route::middleware('feature:feature_articles')->get('/articles/content_extend_reading/{id}', [ArticlesController::class, 'ContentExtendReading'])->name('articles.change.contentextendreading');

        // 取得延伸閱讀
        Route::middleware('feature:feature_articles')->post('/articles/getFurtherData', [ArticlesController::class, 'GetFurtherData'])->name('articles.get.furtherdata');
        Route::middleware('feature:feature_articles')->get('/articles/getFurtherLoading', [ArticlesController::class, 'getFurtherLoading'])->name('articles.get.furtherloading');

        // 找分類
        Route::middleware('feature:feature_articles')->post('/articles/changeCategories', [ArticlesController::class, 'changeCategories'])->name('articles.change.categories');

        // 自產Tag
        if (env('CHATGPTTAG', false)) :
            Route::middleware('feature:feature_articles')->post('/articles/tag_recommend', [ArticlesController::class, 'getTagRecommentForChatGPT'])->name('articles.get.tag_recommend');
        else :
            Route::middleware('feature:feature_articles')->post('/articles/tag_recommend', [ArticlesController::class, 'getTagRecomment'])->name('articles.get.tag_recommend');
        endif;

        // 取得Yahoo供稿延伸閱讀
        Route::middleware('feature:feature_articles')->get('/articles/getArticleForYahoo', [ArticlesController::class, 'getArticlesForYahoo'])->name('articles.get.ArticlesYahoo');

        // 取得Yahoo文章
        Route::middleware('feature:feature_articles')->get('/articles/yahoo_ext', [ArticlesController::class, 'getHealthCategoryArticles'])->name('articles.get.yahoo_ext');

        // 醫學百科
        Route::middleware('feature:feature_articles')->get('/articles/health_organs', [ArticlesController::class, 'GetHealthOrgans'])->name('articles.get.health_organs');
        Route::middleware('feature:feature_articles')->get('/articles/health_sickness', [ArticlesController::class, 'GetHealthSickness'])->name('articles.get.health_sickness');
        Route::middleware('feature:feature_articles')->get('/articles/load_article_sickness', [ArticlesController::class, 'LoadArticleSickness'])->name('articles.load.article_sickness');

        // 名醫與食譜List
        Route::middleware('feature:feature_articles')->get('/articles/get_master_list', [ArticlesController::class, 'GetMastersList'])->name('articles.get.master_list');

        Route::middleware(['feature:feature_articles','type:ad'])->resource('/articles_ad', ArticlesController::class);
        Route::middleware(['feature:feature_articles','type:web'])->resource('/articles', ArticlesController::class);

        // 廣編稿管理
        Route::middleware(['feature:feature_sponsor'])->resource('/sponsorlist', SponsorController::class);

        Route::get('/search_articles', [ArticlesController::class, 'searchArticles'])->name('articles.search');
        Route::middleware('feature:feature_articles')->post('/articles/categories', [ArticlesController::class, 'appendCategories'])->name('articles.append.categories');
        Route::middleware('feature:feature_end_text')->resource('/end_text', EndTextController::class);
        Route::middleware('feature:feature_keyvisual')->resource('/keyvisual', KeyvisualController::class);
    });

    Route::prefix('home_area')->group(function () {
        Route::middleware('feature:feature_home_taxon')->resource('/home_taxon', HomeTaxonController::class);
        Route::middleware('feature:feature_home_taxon')->post('/home_taxon/save_sort', [HomeTaxonController::class, 'saveSort'])->name('home_taxon.save_sort');
        Route::middleware('feature:feature_measure')->resource('/measure', MeasureController::class);
        Route::middleware('feature:feature_right_bar')->resource('/right_bar', RightBarController::class);
        Route::middleware('feature:feature_right_bar')->post('/right_bar/save_sort', [RightBarController::class, 'saveSort'])->name('right_bar.save_sort');
        Route::middleware('feature:feature_right_bar_detail')->resource('/right_bar/{right_bar}/detail', RightBarDetailController::class)->except(['show', 'destroy']);
        Route::middleware('feature:feature_right_bar')->post('/right_bar/{right_bar}/detail/save_sort', [RightBarDetailController::class, 'saveSort'])->name('detail.save_sort');
    });

    Route::prefix('articles_app')->group(function () {
        Route::middleware('feature:feature_notifications')->resource('/notifications', NotificationsController::class)->except(['show']);
        Route::middleware('feature:feature_notifications')->get('/notifications/validate-repeat-article/{articleId}', [NotificationsController::class, 'validateArticleId'])->name('notifications.validate-repeat-article');
        Route::middleware('feature:feature_shake')->resource('/shake', ShakeController::class)->except(['show', 'destroy']);
        Route::middleware('feature:feature_shake')->get('/shake/download', [ShakeController::class, 'download'])->name('shake.download');
        Route::middleware('feature:feature_app_activities')->resource('/app_activities', AppActivitiesController::class);
        Route::middleware('feature:feature_app_versions')->resource('/app_versions', AppVersionsController::class);
        Route::middleware('feature:feature_app_news')->resource('/app_news', AppNewsController::class);
        Route::middleware('feature:feature_app_splash')->resource('/app_splash', AppSplashController::class);
    });

    Route::get('/sub-category-by-main/{main_category_id}', [SubCategoriesController::class, 'getCategoryByMain'])->name('sub_categories.by-main');

    Route::prefix('masters_manage')->group(function () {
        Route::middleware('feature:feature_masters')->resource('/masters', MastersController::class);
        Route::middleware('feature:feature_masters_banner')->resource('/masters_banner', MastersBannerController::class)->except(['create', 'destroy']);
        Route::middleware('feature:feature_masters_banner')->post('/masters_banner/sort', [MastersBannerController::class, 'saveSort'])->name('masters_banner.save.sort');
        Route::middleware('feature:feature_masters_banner')->get('/masters_banner/get_master/{division_id}/{institution_id}', [MastersBannerController::class, 'getMaster'])->name('masters_banner.get.master');
        Route::middleware('feature:feature_divisions')->patch('/divisions/sort', [DivisionsController::class, 'saveSort'])->name('divisions.save.sort');
        Route::middleware('feature:feature_divisions')->resource('/divisions', DivisionsController::class);
        Route::middleware('feature:feature_institutions')->patch('/institutions/sort', [InstitutionsController::class, 'saveSort'])->name('institutions.save.sort');
        Route::middleware('feature:feature_institutions')->resource('/institutions', InstitutionsController::class);
        Route::middleware('feature:feature_masters_banner')->resource('/masters_banner', MastersBannerController::class);
    });

    Route::prefix('encyclopedia_manage')->group(function () {
        Route::middleware('feature:feature_article_type')->resource('/article_type', ArticleTypeController::class)->except(['show', 'destroy']);
        Route::middleware('feature:feature_article_type')->post('/article_type/save_sort', [ArticleTypeController::class, 'saveSort'])->name('article_type.save_sort');
        Route::middleware('feature:feature_sickness')->patch('/sickness/sort', [SicknessController::class, 'saveSort'])->name('sickness.save.sort');
        Route::middleware('feature:feature_sickness')->resource('/sickness', SicknessController::class)->except(['show','destroy']);
        Route::middleware('feature:feature_organs')->patch('/organs/sort', [OrgansController::class, 'saveSort'])->name('organs.save.sort');
        Route::middleware('feature:feature_organs')->resource('/organs', OrgansController::class);
    });

    // 文章供稿
    Route::prefix('third_party_feed_manage')->group(function () {
        Route::middleware('feature:feature_line_articles_condition')->get('/line_articles_condition', [LineArticlesController::class, 'conditionList'])->name('line_articles.condition.list');
        Route::middleware('feature:feature_line_articles_condition')->post('/line_articles_condition', [LineArticlesController::class, 'conditionSave'])->name('line_articles.condition.save');
        Route::middleware('feature:feature_line_articles')->get('/line_articles', [LineArticlesController::class, 'articlesList'])->name('line_articles.list');
        Route::middleware('feature:feature_line_articles')->patch('/line_articles', [LineArticlesController::class, 'changeStatus'])->name('line_articles.changeStatus');
        Route::middleware('feature:feature_line_articles')->delete('/line_articles', [LineArticlesController::class, 'delete'])->name('line_articles.delete');
        Route::middleware('feature:feature_yahoo_articles')->get('/yahoo_articles', [YahooArticlesController::class, 'articlesList'])->name('yahoo_articles.list');
        Route::middleware('feature:feature_yahoo_articles')->delete('/yahoo_articles', [YahooArticlesController::class, 'delete'])->name('yahoo_articles.delete');
        Route::middleware('feature:feature_mixerbox_articles_condition')->get('/mixerbox_articles_condition', [MixerboxArticlesController::class, 'conditionList'])->name('mixerbox_articles.condition.list');
        Route::middleware('feature:feature_mixerbox_articles_condition')->post('/mixerbox_articles_condition', [MixerboxArticlesController::class, 'conditionSave'])->name('mixerbox_articles.condition.save');
        Route::middleware('feature:feature_mixerbox_articles')->get('/mixerbox_articles', [MixerboxArticlesController::class, 'articlesList'])->name('mixerbox_articles.list');
        Route::middleware('feature:feature_mixerbox_articles')->patch('/mixerbox_articles', [MixerboxArticlesController::class, 'changeStatus'])->name('mixerbox_articles.changeStatus');
        Route::middleware('feature:feature_mixerbox_articles')->delete('/mixerbox_articles', [MixerboxArticlesController::class, 'delete'])->name('mixerbox_articles.delete');
    });

    // deepQ AI
    Route::prefix('deepq')->name('deepq.')->group(function () {
        Route::middleware('feature:feature_deepq_keywords')->get('/keywords/generate', [DeepqKeywordController::class, 'generate'])->name('keyword.generate');
        Route::middleware('feature:feature_deepq_keywords')->resource('/keywords', DeepqKeywordController::class);
        Route::middleware('feature:feature_deepq_banners')->resource('/banners', DeepqBannerController::class);
        Route::middleware('feature:feature_deepq_banners')->post('/banners/save_sort', [DeepqBannerController::class, 'saveSort'])->name('banners.save_sort');
    });

    // AI Wize
    Route::prefix('aiwize')->name('aiwize.')->group(function () {
        // Route::middleware('feature:feature_aiwize_articles')->get('/articles', [AiWizeArticlesController::class, 'index'])->name('aiwize.articles');
        Route::middleware('feature:feature_aiwize_articles')->resource('/articles', AiWizeArticlesController::class);
    });
});



// Route::prefix('shared')->middleware('auth')->group(function () {
//     //圖片裁切
//     Route::prefix('image_crop')->group(function () {
//         Route::post('/img_save_to_file', [ImageCropController::class,'img_save_to_file']);
//         Route::post('/img_crop_to_file', [ImageCropController::class,'img_crop_to_file']);
//     });
//     //圖片上傳套件
//     Route::prefix('fileupload')->group(function () {
//         Route::post('/upload_file', [UploadFileController::class,'upload_file']);
//         Route::post('/delete_file', [UploadFileController::class,'delete_file']);
//     });
//     //共用儲存排序
//     Route::post('/save_reorder', [ReorderController::class,'save_reorder']);
// });

Route::get('/home', function () {
    return view('home');
})->name('home')->middleware('auth');

Route::get('image-gallery', [ImageGalleryController::class, 'index']);
Route::get('image-gallery/favorite', [ImageGalleryController::class, 'favorite']);
Route::post('image-gallery', [ImageGalleryController::class, 'upload']);
Route::post('image-gallery/tinymce', [ImageGalleryController::class, 'tinymceupload']);
Route::delete('image-gallery/{id}', [ImageGalleryController::class, 'destroy']);

Route::get('video-gallery', [VideoGalleryController::class, 'index']);
Route::post('video-gallery', [VideoGalleryController::class, 'upload'])->name('video.upload');
Route::delete('video-gallery/{id}', [VideoGalleryController::class, 'destroy']);
