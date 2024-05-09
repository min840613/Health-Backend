<?php

use App\Http\Controllers\Api\App\AIApiController;
use App\Http\Controllers\Api\ArticleApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\KeywordApiController;
use App\Http\Controllers\Api\ExhibitionApiController;
use App\Http\Controllers\Api\MasterApiController;
use App\Http\Controllers\Api\ToolsApiController;
use App\Http\Controllers\Api\EncyclopediaApiController;
use App\Http\Controllers\Api\ThirdPartyFeedController;
use App\Http\Controllers\Api\MemberApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AIApiController::class)->group(function () {
    Route::get('ai-prompt', 'aiPrompt')->name('api.app.ai.prompt');
});

Route::controller(KeywordApiController::class)->group(function () {
    Route::get('trending-search-keywords', 'index')->name('api.home.keywords');
});

Route::controller(ArticleApiController::class)->group(function () {
    Route::get('news', 'news')->name('api.home.news');
    Route::get('hot-articles', 'hot')->name('api.home.hot');
    Route::get('blocks/{block_num}', 'block')->name('api.home.block');
    Route::get('right_blocks/{block_num}', 'right_block')->name('api.home.right_block');
});

Route::controller(CategoryApiController::class)->group(function () {
    Route::get('nav', 'nav')->name('api.home.nav');
    Route::get('categories', 'index')->name('api.category.index');
});

Route::controller(ToolsApiController::class)->group(function () {
    Route::get('tools', 'tools')->name('api.home.tools');
});

Route::controller(ExhibitionApiController::class)->group(function () {
    Route::get('topic', 'topic')->name('api.home.topic');
});

Route::controller(EncyclopediaApiController::class)->group(function () {
    Route::get('most-focus-sickness', 'mostFocusSickness')->name('api.home.mostFocusSickness');
});

Route::controller(MasterApiController::class)->group(function () {
    Route::get('expert/{expert_en_name}', 'show')->name('api.master.show');
    Route::get('banner', 'banner')->name('api.master.banner');
    Route::get('conditions', 'conditions')->name('api.master.conditions');
    Route::get('experts', 'experts')->name('api.master.experts');
    Route::get('new-articles', 'newArticles')->name('api.master.new.articles');
});

Route::controller(ThirdPartyFeedController::class)->group(function () {
    Route::get('yahoo_rss/health', 'yahoo')->name('api.yahoo_rss');
    Route::get('line_articles/health', 'lineArticles')->name('api.line_articles');
    Route::get('mixerbox_articles/health', 'mixerboxArticles')->name('api.mixerbox_articles');
});

Route::controller(MemberApiController::class)->group(function () {
    Route::get('member', 'decrypt')->name('api.member.decrypt');
});
