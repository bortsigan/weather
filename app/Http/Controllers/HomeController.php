<?php

namespace App\Http\Controllers;

use App\Services\WeatherTemperatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

/**
 * Class HomeController.
 */
class HomeController
{
    /**
     * @var WeatherTemperatureService $weatherTemperatureService
     */
    protected $weatherTemperatureService;

    /**
     * HomeController constructor.
     * 
     * @param WeatherTemperatureService $weatherTemperatureService
     **/
    public function __construct(WeatherTemperatureService $weatherTemperatureService)
    {
        $this->weatherTemperatureService = $weatherTemperatureService;
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request) : View
    {
        /**
         * Guidelines about the search for City's temperature
         * 1. It will not display result if one of the two API references could not find the place or has error
         * 2. It will only cache once it is saved
         * 3. Last saved in cache will only display for a limited time
         * 4. When reset is clicked, cache will be removed and search will be empty
         **/
        
        $city   = null;
        $reset  = $request->query('reset') ?? null;
        $cache  = Cache::get('lastTemperatureRead');

        if ($reset) { 
            Cache::forget('lastTemperatureRead');
            $cache  = null;
            $result = [];
        }

        $city = $request->input('city') ? $request->input('city') : ($cache ? $cache['city'] : null);
        $result = $this->weatherTemperatureService->getTemperatureByQuery($city);

        if (Cache::has('lastTemperatureRead') && !$reset && !$request->input('city')) {
            $city   = $cache['city'];
            $result = $cache;
            $result['error'] = false;
        }

        return view('weather.index')
                    ->withTemperatureResult($result)
                    ->withRequestCity($request->input('city') ?? null);
    }

    /**
     * @return JsonResponse
     */
    public function store(Request $request) : JsonResponse
    {
        $data = $request->all();

        $store = $this->weatherTemperatureService->store($data);

        return response()->json($store);
    }
}
