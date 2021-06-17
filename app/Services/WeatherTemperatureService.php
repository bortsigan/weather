<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Services\OpenWeatherApiService;
use App\Services\WeatherApiService;
use App\Services\ComputeWeatherTemperatureService;
use App\Services\Interfaces\WeatherApiInterface;

/**
 * Class WeatherTemperature.
 */
class WeatherTemperatureService implements WeatherApiInterface
{
    const NUM_OF_DECIMAL = 2;
    const CACHE_MINUTES = 3;

    /** @var $weatherApiService **/
    protected $weatherApiService;

    /** @var $openWeatherApiService **/
    protected $openWeatherApiService;

    /** @var $computeWeatherTemperatureService **/
    protected $computeWeatherTemperatureService;
    
    /**
     * WeatherTemperature constructor.
     *
     * @param WeatherApiService $openWeatherApiService
     * @param OpenWeatherApiService $openWeatherApiService
     * @param ComputeWeatherTemperatureService $computeWeatherTemperatureService
     * 
     */
    public function __construct(WeatherApiService $weatherApiService, OpenWeatherApiService $openWeatherApiService, ComputeWeatherTemperatureService $computeWeatherTemperatureService)
    {
        $this->weatherApiService = $weatherApiService;
        $this->openWeatherApiService = $openWeatherApiService;
        $this->computeWeatherTemperatureService = $computeWeatherTemperatureService;
    }

    /**
     * Get temperature by query
     * 
     * @param String $query
     * 
     * @return Array $array
     **/ 
    public function getTemperatureByQuery($query = null) : Array
    {
        $response = [ 'error' => true, 'message' => null, 'temperature_c' => null];

        if (!$query) { return $response; }

        $query = ucfirst($query);

        $weatherApiResponse     = $this->weatherApiService->getTemperatureByQuery($query);
        $openWeatherApiResponse = $this->openWeatherApiService->getTemperatureByQuery($query);

        if ($weatherApiResponse['error'] || $openWeatherApiResponse['error']) {
            $response['message'] = "Something went wrong, please try again later";
            return $response;
        }

        $apiArrayResponse = [
            'weatherApiResponse' => $weatherApiResponse,
            'openWeatherApiResponse' => $openWeatherApiResponse
        ];

        $apiResponse = $this->getResponseFormat($apiArrayResponse);

        return $apiResponse;
    }

    /**
     * Get response format from query
     * 
     * @param Array $response
     * @return Array $apiResponse
     **/ 
    public function getResponseFormat(Array $apiResponse) : Array
    {
        $response = [];

        $weatherApiResponseDecode = $this->weatherApiService->getResponseFormat($apiResponse['weatherApiResponse']);
        $openWeatherApiResponseDecode = $this->openWeatherApiService->getResponseFormat($apiResponse['openWeatherApiResponse']);

        $format[]['temperature'] = (float) $weatherApiResponseDecode['temperature'];
        $format[]['temperature'] = (float) $openWeatherApiResponseDecode['temperature'];

        $computedAverageTemperature = $this->computeWeatherTemperatureService->getAverageOfTemperature($format);

        $response = [
            'error'         => false,
            'temperature_c' => $computedAverageTemperature . "°C"
        ];

        $response = array_merge($response, $weatherApiResponseDecode);

        return $response;
    }
}
