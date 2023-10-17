<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Mews\Captcha\Facades\Captcha;

class IndexController extends Controller
{
    /**
     * Get captcha
     *
     * @param Request $request
     * @param Captcha $captcha
     * @param string  $config
     * @return mixed
     */
    public function captcha(Request $request, string $config = 'default')
    {
        $request->validate(['ability' => 'required']);

        $key = getCurrentCaptchaKey($request->ability);

        $cacheValue = Cache::get($key);

        if (!$cacheValue || $cacheValue <= 3) {
            return false;
        }

        return Captcha::create($config, true);
    }
}