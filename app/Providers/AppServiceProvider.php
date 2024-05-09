<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($data, $meta = []) {
            $response = [
                'status' => '00000',
                'message' => 'success',
                'data' => $data,
            ];
            if (!empty($meta)) {
                $response['meta'] = $meta;
            }
            return Response::json($response, 200, [], JSON_UNESCAPED_UNICODE);
        });

        Response::macro('xml', function ($viewPage, $data) {
            return response()->view($viewPage, $data)->header('Content-Type', 'text/xml');
        });

        Paginator::useBootstrap();
    }
}
