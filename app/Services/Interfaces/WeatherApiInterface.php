<?php

namespace App\Services\Interfaces;

interface WeatherApiInterface {

	/**
     * Get Temperature by query
     * 
     * @param String $query
     * @return Array $array
     **/ 
	public function getTemperatureByQuery(String $query) : Array;

	/**
     * Get response format from query
     * 
     * @param Array $response
     * @return Array $apiResponse
     **/ 
	public function getResponseFormat(Array $apiResponse) : Array;
}