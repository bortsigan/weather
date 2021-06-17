<?php

namespace App\Services;

use App\Services\Interfaces\CacheInterface;
use Illuminate\Support\Facades\Cache;

/**
 * class TemperatureCachingService
 **/
class TemperatureCachingService implements CacheInterface 
{
	const CACHE_MINUTES = 3;

	public function __construct() {}

	/**
	 * Get stored cache by key
	 * 	
	 * @param String $key
	 * @return Array 
	 **/
	public function getCachedByKey(String $key) : Array
	{
		return Cache::has($key) ? Cache::get($key) : [];
	}

	/**
	 * Store in cache for the latest saved
	 * 
	 * @param Array $arrayValues
	 * @param String $key
	 * 
	 * @return Void
	 **/
	public function store(Array $arrayValues, String $key) : Void 
	{
		if (!Cache::has($key)) {

			Cache::add($key, $arrayValues, now()->addMinutes(self::CACHE_MINUTES));

		} else {

			$this->forget($key);
			Cache::put($key, $arrayValues, now()->addMinutes(self::CACHE_MINUTES));

		}
	}

	/**
	 * Remove all assigned cache in the key
	 * 
	 * @param Array $request
	 * 
	 * @return Void
	 **/
	public function forget(String $key) : Void
	{
		if (Cache::has($key)) { 
			Cache::forget($key);
		}
	}
}