<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class Captcha
{
    public string $key;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $ability): Response
    {
        $this->key = $this->getCurrentKey($request, $ability);
        $attamps = $this->tickCache();
        
        // 需要验证码
        if ($attamps > 3) {
            $request->validate(['captcha' => 'captcha_api:'. ($request->key ?? '') . ',math']);
        }
        $response = $next($request);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            $this->clearCache();
        }

        return $response;
    }

    /**
     * Get current captcha key
     *
     * @param Request $request
     * @param string $ability
     * @return string
     */
    protected function getCurrentKey(Request $request, string $ability): string
    {
        return 'captcha'.$ability.$request->ip();
    }

    /**
     * Increment cache value
     *
     * @return int
     */
    protected function tickCache(): int
    {
        $value = Cache::get($this->key) ?? 0;

        Cache::put($this->key, $value + 1);

        return $value;
    }

    /**
     * Clear cache
     *
     * @return void
     */
    protected function clearCache()
    {
        Cache::forget($this->key);
    }
}
