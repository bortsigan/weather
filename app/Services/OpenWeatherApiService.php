<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Services\Interfaces\WeatherApiInterface;

/**
 * Class OpenWeatherTemperatureService.
 */
class OpenWeatherApiService implements WeatherApiInterface
{
    const TEMPERATURE_UNIT = 'metric';
    const NUM_OF_DECIMAL = 2;

    public function __construct() {}

    /**
     * Get Temperature by query
     * 
     * @param String $query
     * @return Array $array
     **/ 
    public function getTemperatureByQuery(String $query) : Array
    {
        $response = ['error' => false, 'apiResponse' => null];

        try {
            $apiResponse = Http::get('https://api.openweathermap.org/data/2.5/find', [
                'appid'     => env('OPEN_WEATHER_API_KEY'),
                'q'         => $query,
                'units'     => self::TEMPERATURE_UNIT
            ]);

            $response['apiResponse'] = $apiResponse;

        } catch(\Exception $e) {
            \Log::error($e->getMessage());

            $response['error'] = true;
        }

        return $response;
    }

    /**
     * Get response format from query
     * 
     * @param Array $apiResponse
     * @return Array $array
     **/ 
    public function getResponseFormat(Array $apiResponse) : Array
    {
        $totalTemp = 0;
        $response = ['error' => true, 'temperature' => null, 'message' => 'Place not found'];

        $temperatures = json_decode($apiResponse['apiResponse']->body());

        if ((isset($temperatures->cod) && $temperatures->cod == 400) || $temperatures->count == 0) {
            return $response;
        }

        for ($i = 0; $i < $temperatures->count; $i++) {
            $totalTemp += $temperatures->list[$i]->main->temp;
        }

        $totalTemp      = $totalTemp / $temperatures->count;
        $temperature    = round($totalTemp, self::NUM_OF_DECIMAL);

        $response = ['error' => false, 'message' => null, 'temperature' => $temperature];

        return $response;
    }
}