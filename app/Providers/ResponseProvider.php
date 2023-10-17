<?php

namespace App\Providers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // 成功的response
        Response::macro('success', function ($data = '', $message = 'success', $headers = [], $options = 0, $meta = []): JsonResponse {
            $resData = [
                'status'  => 'success',
                'message' => $message,
                'data'    => $data
            ];

            if ($meta) {
                $resData['meta'] = $meta;
            }
            return new JsonResponse($resData, 200, $headers, $options);
        });

        // 失败的response
        Response::macro('error', function ($message = 'error', $code = 400, $headers = [], $options = 0, $data = ''): JsonResponse {
            return new JsonResponse([
                'status'  => 'error',
                'message' => $message,
                'data'    => $data
            ], $code, $headers, $options);
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}