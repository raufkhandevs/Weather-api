<?php

namespace App\Traits;

use Closure;
use Illuminate\Support\Facades\Cache;

trait CacheAble
{
    /**
     * Get a value from the cache or execute the given closure and store the result in the cache.
     *
     * @param string $key
     * @param Closure $closure
     * @param int $minutes
     * @return mixed
     */
    public static function remember(string $key, Closure $closure, int $minutes = 10): mixed
    {
        return Cache::remember($key, $minutes, $closure);
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     * @return bool
     */
    public static function forget(string $key): bool
    {
        return Cache::forget($key);
    }
}
