<?php

namespace App\Http\Controllers;

use App\Services\StoreWeatherTemperature;
use App\Services\DisplayResultService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

/**
 * Class HomeController.
 */
class HomeController
{
    /**
     * @var StoreWeatherTemperature $storeWeatherTemperature
     */
    protected $storeWeatherTemperature;

    /**
     * @var DisplayResultService $displayResultService
     */
    protected $displayResultService;

    /**
     * HomeController constructor.
     * 
     * @param StoreWeatherTemperature $storeWeatherTemperature
     * @param DisplayResultService $displayResultService
     **/
    public function __construct(StoreWeatherTemperature $storeWeatherTemperature, DisplayResultService $displayResultService)
    {
        $this->storeWeatherTemperature = $storeWeatherTemperature;
        $this->displayResultService = $displayResultService;
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
        
        $result = [];
        $query  = $request->input('query') ?? null;
        $reset  = $request->query('reset') ? true : false;

        $display = $this->displayResultService->getDisplayResult($query, $reset);
        
        return view('weather.index')
                    ->withTemperatureResult($display)
                    ->withQuery($query);
    }

    /**
     * @return JsonResponse
     */
    public function store(Request $request) : JsonResponse
    {
        $data = $request->all();

        $store = $this->storeWeatherTemperature->store($data);

        return response()->json($store);
    }
}
