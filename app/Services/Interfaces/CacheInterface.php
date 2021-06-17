<?php

namespace App\Services\Interfaces;

/**
 * interface CacheInterface
 **/
interface CacheInterface 
{
	/**
	 * Get stored cache by key
	 * 
	 * @param String $key
	 * @return Array 
	 **/
	public function getCachedByKey(String $key) : Array;

	/**
	 * Store in cache 
	 * 
	 * @param Array $arrayValues
	 * @param String $key
	 * 
	 * @return Void
	 **/
	public function store(Array $arrayValues, String $key) : Void;

	/**
	 * Remove all the cache inside a key 
	 * 
	 * @param String $key
	 * 
	 * @return Void
	 **/
	public function forget(String $key) : Void;
}