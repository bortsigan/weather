<?php

namespace App\Services;

use App\Services\WeatherTemperatureService;
use App\Services\TemperatureCachingService;
use Illuminate\Support\Facades\Cache;

/**
 * Class DisplayResultService.
 */
class DisplayResultService 
{
	const EMPTY_RESPONSE = [];

	/**
     * @var WeatherTemperatureService $weatherTemperatureService
     */
    protected $weatherTemperatureService;

    /**
     * @var TemperatureCachingService $temperatureCachingService
     */
    protected $temperatureCachingService;

	/**
     * DisplayResultService constructor.
     * 
     * @param WeatherTemperatureService $weatherTemperatureService
     * @param TemperatureCachingService $temperatureCachingService
     **/
	public function __construct(WeatherTemperatureService $weatherTemperatureService, TemperatureCachingService $temperatureCachingService) 
	{

		$this->weatherTemperatureService = $weatherTemperatureService;
		$this->temperatureCachingService = $temperatureCachingService;
	}

	/**
	 * Get display result
	 * 
	 * @param Array $arrayValues
	 * @param String $query
	 * @param Bool $reset
	 * 
	 * @return Array
	 **/
	public function getDisplayResult(String $query = null, Bool $reset = false) : Array
	{
		$cacheKey = env('LAST_STORED_CACHE_KEY');
		$cachedData = $this->temperatureCachingService->getCachedByKey($cacheKey);

		if ($reset) {
			$this->temperatureCachingService->forget($cacheKey); # delete cache on reset to empty display
			return self::EMPTY_RESPONSE;
		}

		if (!$query && !$cachedData) {
			return self::EMPTY_RESPONSE;
		}

		if (!$query && $cachedData) {
			return $cachedData;
		}

		if ($query) {
			return $this->weatherTemperatureService->getTemperatureByQuery($query);
		}
	}
}
