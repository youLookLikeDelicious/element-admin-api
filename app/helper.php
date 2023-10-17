<?php

if (!function_exists('convertChildren')) {
    /**
     * 将一维数组转为树形结构
     *
     * @param \Illuminate\Database\Eloquent\Collection<\App\Models\Menu> $list
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function convertChildren(\Illuminate\Database\Eloquent\Collection $list) {
        $list = $list->keyBy('id');
        $result = $list->slice(0);

        foreach ($list as $value) {
            $parent = $list[$value->parent_id] ?? null;
            if ($parent) {
                if (! $parent->relationLoaded('children')) {
                    $parent->setRelation('children', collect([$value]));
                } else {
                    $parent->setRelation('children', $parent->children->concat([$value]));
                }
                // dump($parent->getRelation('children'));
                unset($result[$value->id]);
            }
        }

        return $result->values();
    }
}

if (!function_exists('getLocationOfIp')) {
    /**
     * 获取ip的地理位置
     *
     * @param string $ip
     * @return string
     */
    function getLocationOfIp($ip = '') {
        $location = \GeoIP::getLocation($ip ?: request()->ip())->toArray();

        return ($location['state_name'] ?? '') . ($location['city'] ?? '');
    }
}

if (!function_exists('getCurrentCaptchaKey')) {
    /**
     * 获取当前验证码的键值
     *
     * @param string $ability 功能
     * @return string
     */
    function getCurrentCaptchaKey(string $ability = '')
    {
        $request = request();
        return 'captcha'.$ability.$request->ip();
    }
}

if (! function_exists('secondsToStr')) {
    /**
     * 将秒转为字符串
     *
     * @param int $seconds
     * @param string $format
     * @return string
     */
    function secondsToStr($seconds, string $format = 'H小时i分s秒') {
        if (! $seconds) {
            return '';
        }

        // 获取天数
        $days = (int) ($seconds / 86400);
        $remainSeconds = $seconds % 86400;
        $duration = gmdate($format, $remainSeconds);

        $duration = preg_replace('/00[^\d]+/', '', $duration);
        $duration = trim($duration, '0');

        if ($days) {
            $duration = $days.'天'.$duration;
        }
        return $duration;
    }
}