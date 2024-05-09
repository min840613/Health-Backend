<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        ini_set("memory_limit", "4096M");
        $this->call(
            [
                HealthAuthorsSeeder::class,
                HealthCategoriesSeeder::class,
                HealthSubCategoriesSeeder::class,
                HealthMenuListSeeder::class,
                HealthEndOfTextSeeder::class,
                HealthArticlesFurtherReadingSeeder::class,
                HealthArticlesSeeder::class,
                HealthArticleCategoriesMappingsSeeder::class,
                HealthBodySeeder::class,
                HealthOrgansSeeder::class,
                HealthSicknessSeeder::class,
                HealthSicknessToOrganSeeder::class,
                HealthArticlesCategoriesSeeder::class,
                HealthArticlesSicknessSeeder::class,
                HealthPushNotificationsSeeder::class,
                HealthActivitiesAnnouncementSeeder::class,
                HealthSystemAnnouncementSeeder::class,
                HealthShakeSeeder::class,
                HealthAppActivitiesSeeder::class,
                HealthAppVersionSeeder::class,
                HealthDailyViewCountSeeder::class,
                HealthArticleTagSeeder::class,
                HealthKeywordClickCountSeeder::class,
                HealthSponsorAdSeeder::class,
            ]
        );

        // DB::transaction(function () {
        //     $this->call(
        //         [
        //             HealthAuthorsSeeder::class,
        //             HealthCategoriesSeeder::class,
        //             HealthSubCategoriesSeeder::class,
        //             HealthMenuListSeeder::class,
        //             HealthEndOfTextSeeder::class,
        //             HealthArticlesFurtherReadingSeeder::class,
        //             HealthArticlesSeeder::class,
        //             HealthBodySeeder::class,
        //             HealthOrgansSeeder::class,
        //             HealthSicknessSeeder::class,
        //             HealthSicknessToOrganSeeder::class,
        //             HealthArticlesCategoriesSeeder::class,
        //             HealthArticlesSicknessSeeder::class,
        //         ]
        //     );
        // });

        // \App\Models\User::factory(10)->create();
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
