<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Weather;
use App\Services\TemperatureCachingService;

/**
 * Class StoreWeatherTemperature.
 */
class StoreWeatherTemperature 
{
    const ERROR_FALSE = false;

	/** @var $model **/
	protected $model;

    /** @var $temperatureCachingService **/
    protected $temperatureCachingService;

	/**
     * WeatherTemperature constructor.
     *
     * @param Weather $model
     * @param TemperatureCachingService $temperatureCachingService 
     */
	public function __construct(Weather $model, TemperatureCachingService $temperatureCachingService) 
	{
		$this->model = $model;
        $this->temperatureCachingService = $temperatureCachingService;
	}

	/**
     * Store data for temperature reading
     * 
     * @param Array $request
     * @return Array $array
     **/
	public function store(Array $request) : Array 
	{
		$response = ['error' => true, 'message' => null];

        DB::beginTransaction();

        try {
            $this->model->create($request);

            $request['is_cache']    = true;
            $request['error']       = self::ERROR_FALSE;
            $request['message']     = null;

            
            $response['error']      = self::ERROR_FALSE;
            $response['message']    = "Successfully saved.";

            $this->temperatureCachingService->forget(env('LAST_STORED_CACHE_KEY')); # delete before storing new to the cache
            $this->temperatureCachingService->store($request, env('LAST_STORED_CACHE_KEY')); # store a new cache after deleting the old data

            DB::commit();

        } catch(\Exception $e) {
            DB::rollBack();

            \Log::error($e->getMessage());

            $response['message'] = "Something went wrong in saving, please try again.";
        } 

        return $response;
	}
}