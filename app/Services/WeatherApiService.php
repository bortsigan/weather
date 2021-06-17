<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Services\Interfaces\WeatherApiInterface;

/**
 * Class WeatherTemperature.
 */
class WeatherApiService implements WeatherApiInterface {
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
            $apiResponse = Http::get('https://api.weatherapi.com/v1/current.json', [
                'key'   => env('WEATHER_API_KEY'),
                'q'     => $query,
                'aqi'   => 'no'
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
        $response = ['error' => true, 'temperature' => null, 'message' => 'Place not found'];

        $temperatures = json_decode($apiResponse['apiResponse']->body());

        if (isset($temperatures->error)) {
            return $response;
        }

        $response = [
            'error'         => false,
            'message'       => null,
            'temperature'   => $temperatures->current->temp_c ? $temperatures->current->temp_c : 0,
            'region'        => $temperatures->location->name,
            'city'          => $temperatures->location->region,
            'country'       => $temperatures->location->country,
            'cloud'         => $temperatures->current->condition->text,
            'img'           => $temperatures->current->condition->icon
        ];

        return $response;
    }
}