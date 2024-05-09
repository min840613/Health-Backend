<?php

namespace App\Providers;

use App\Events\ArticleStored;
use App\Events\ArticleUpdated;
use App\Events\DeepqKeywordGenerated;
use App\Events\DeepqKeywordGenerating;
use App\Listeners\DeepqKeywordGenerateListener;
use App\Listeners\IndexingArticles;
use App\Listeners\SyncOldDatabaseArticles;
use App\Listeners\SyncOldDatabaseArticlesUpdate;
use App\Listeners\SyncRssLineArticles;
use App\Listeners\SyncRssYahooArticles;
use App\Listeners\SyncSicknessArticles;
use App\Models\App\ActivitiesAnnouncementModel;
use App\Models\App\AppActivitiesModel;
use App\Models\App\NotificationsModel;
use App\Models\App\ShakeModel;
use App\Models\App\SystemAnnouncementModel;
use App\Models\App\AppVersionsModel;
use App\Models\App\AppNewsModel;
use App\Models\App\AppSplashModel;
use App\Models\Articles\EndTextModel;
use App\Models\Articles\SponsorAdModel;
use App\Models\Authors\AuthorsModel;
use App\Models\Categories\MainCategoriesModel;
use App\Models\Categories\MenuListModel;
use App\Models\Categories\SubCategoriesModel;
use App\Models\ImageAlbum;
use App\Models\VideoAlbum;
use App\Models\Encyclopedia\SicknessModel;
use App\Models\Encyclopedia\SicknessToOrganModel;
use App\Models\Encyclopedia\OrgansModel;
use App\Observers\ActivitiesAnnouncementModelObserve;
use App\Observers\AppActivitiesModelObserve;
use App\Observers\AuthorsModelObserve;
use App\Observers\EndTextModelObserve;
use App\Observers\MainCategoriesModelObserve;
use App\Observers\MenuListModelObserve;
use App\Observers\NotificationsModelObserve;
use App\Observers\ShakeModelObserve;
use App\Observers\SponsorAdModelObserve;
use App\Observers\SubCategoriesModelObserve;
use App\Observers\SystemAnnouncementModelObserve;
use App\Observers\AppVersionsModelObserver;
use App\Observers\AppNewsModelObserver;
use App\Observers\AppSplashModelObserver;
use App\Observers\ImageAlbumObserver;
use App\Observers\VideoAlbumObserver;
use App\Observers\SicknessModelObserve;
use App\Observers\SicknessToOrganModelObserve;
use App\Observers\ArticlesCategoriesObserver;
use App\Observers\OrgansModelObserve;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ArticleStored::class => [
            SyncOldDatabaseArticles::class,
            SyncRssLineArticles::class,
            SyncSicknessArticles::class,
            IndexingArticles::class,
        ],
        ArticleUpdated::class => [
            SyncOldDatabaseArticlesUpdate::class,
            SyncRssLineArticles::class,
            SyncSicknessArticles::class,
            IndexingArticles::class,
        ],
        DeepqKeywordGenerating::class => [
            DeepqKeywordGenerateListener::class
        ],
        DeepqKeywordGenerated::class => [],
    ];

    /**
     * @var \string[][]
     */
    protected $observers = [
        ShakeModel::class => [ShakeModelObserve::class],
        NotificationsModel::class => [NotificationsModelObserve::class],
        SystemAnnouncementModel::class => [SystemAnnouncementModelObserve::class],
        ActivitiesAnnouncementModel::class => [ActivitiesAnnouncementModelObserve::class],
        AppActivitiesModel::class => [AppActivitiesModelObserve::class],
        AppVersionsModel::class => [AppVersionsModelObserver::class],
        AuthorsModel::class => [AuthorsModelObserve::class],
        MainCategoriesModel::class => [MainCategoriesModelObserve::class],
        SubCategoriesModel::class => [SubCategoriesModelObserve::class],
        MenuListModel::class => [MenuListModelObserve::class],
        EndTextModel::class => [EndTextModelObserve::class],
        SponsorAdModel::class => [SponsorAdModelObserve::class],
        AppNewsModel::class => [AppNewsModelObserver::class],
        AppSplashModel::class => [AppSplashModelObserver::class],
        ImageAlbum::class => [ImageAlbumObserver::class],
        VideoAlbum::class => [VideoAlbumObserver::class],
        SicknessModel::class => [SicknessModelObserve::class],
        SicknessToOrganModel::class => [SicknessToOrganModelObserve::class],
        OrgansModel::class => [OrgansModelObserve::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
