<?php

namespace App\Services;

interface ServiceInterface {

	/**
     * Get Temperature by query
     * 
     * @param String $query
     * @return Array $array
     **/ 
	public function getTemperatureByQuery(String $query) : Array;

	/**
     * Store data for temperature reading
     * 
     * @param Array $request
     * @return Array $array
     **/ 
	public function store(Array $request) : Array;
}