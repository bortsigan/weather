<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Weather;
use App\Services\ServiceInterface;
use Illuminate\Support\Facades\Cache;

/**
 * Class WeatherTemperature.
 */
class WeatherTemperatureService implements ServiceInterface 
{
    const NUM_OF_DECIMAL = 2;

    const CACHE_MINUTES = 3;

    /**
     * WeatherTemperature constructor.
     *
     * @param Weather $model
     */
    public function __construct(Weather $model)
    {
        $this->model = $model;
    }

    /**
     * Get Temperature by query
     * 
     * @param String $query
     * @return Array $array
     **/ 
    public function getTemperatureByQuery($query = null) : Array
    {
        $response = [
            'error'             => true,
            'message'           => null,
            'current_temp'      => null
        ];

        if (!$query) { return $response; }

        $query = ucfirst($query);

        $openWeatherApi = $this->api1($query);
        $weatherApi = $this->api2($query);

        if ($openWeatherApi['error'] || $weatherApi['error']) {
            $response['message'] = "Place not found";
            return $response;
        }

        $totalTemp = ($openWeatherApi['temperature'] + $weatherApi['temperature']) / 2;

        $response = [
            'error'         => false,
            'message'       => null,
            'current_temp'  => round($totalTemp, 2) . "°C",
            'region'        => $weatherApi['name'],
            'city'          => $weatherApi['city'],
            'country'       => $weatherApi['country'],
            'cloud'         => $weatherApi['cloud'],
            'img'           => $weatherApi['img']
        ];

        return $response;
    }

    /**
     * Store data for temperature reading
     * 
     * @param Array $request
     * @return Array $array
     **/ 
    public function store(Array $request) : Array 
    {
        $response = ['error' => true, 'message' => 'Something went wrong, please try again.'];

        DB::beginTransaction();
        try {
            $this->model->create($request);
            DB::commit();

            $response['error'] = false;
            $response['message'] = "Successfully saved.";

            $request['current_temp'] = $request['temperature_c'];
            $request['is_last_saved'] = true;

            Cache::forget('lastTemperatureRead');
            Cache::put('lastTemperatureRead', $request, now()->addMinutes(self::CACHE_MINUTES)); # cache will last with set cache minutes

        } catch(\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());

            $response['message'] = $e->getMessage();
        } 

        return $response;
    }

    /**
     * Get Temperature by open weather map API
     * 
     * @param String $city
     * @return Array $array
     **/ 
    private function api1(String $city = null) : Array
    {
        $totalTemp = 0;
        $response = [
            'error'         => true,
            'temperature'   => null,
            'message'       => 'Place not found',
        ];

        $apiCall = Http::get('https://api.openweathermap.org/data/2.5/find', [
            'appid'     => env('OPEN_WEATHER_API_KEY'),
            'q'         => $city,
            'units'     => 'metric'
        ]);

        $temperatures = json_decode($apiCall->body());

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

    /**
     * Get Temperature by weather map API
     * 
     * @param String $city
     * @return Array $array
     **/ 
    private function api2(String $city = null) : Array
    {
        $response = [
            'error'         => true, 
            'temperature'   => null,
            'message'       => 'Place not found',
        ];

        $apiCall = Http::get('https://api.weatherapi.com/v1/current.json', [
            'key'   => env('WEATHER_API_KEY'),
            'q'     => $city,
            'aqi'   => 'no'
        ]);

        $temperatures = json_decode($apiCall->body());

        if (isset($temperatures->error)) {
            return $response;
        }

        $response = [
            'error'         => false,
            'message'       => null,
            'temperature'   => $temperatures->current->temp_c,
            'name'          => $temperatures->location->name,
            'city'          => $temperatures->location->region,
            'country'       => $temperatures->location->country,
            'cloud'         => $temperatures->current->condition->text,
            'img'           => $temperatures->current->condition->icon
        ];

        return $response;
    }
}