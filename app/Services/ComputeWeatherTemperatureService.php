<?php

namespace App\Services;

/**
 * Class ComputeWeatherTemperatureService.
 */
class ComputeWeatherTemperatureService 
{
	
	const NUM_OF_DECIMAL = 2;
	const ZERO_VALUE = 0;

	public function __construct() {}

	/**
	 * Get average of temperature of any provided temperature under {temperature} key
	 *
	 * @param Array $params
	 * @return Float  
	 **/
	public function getAverageOfTemperature(Array $params) : Float
	{
		$size = sizeof($params);
		$tempTotal = self::ZERO_VALUE;

		foreach($params as $val) {
			if (isset($val['temperature']) && is_numeric($val['temperature'])) {
				$tempTotal += (float) $val['temperature'];
			}
		}

		return ($tempTotal <= self::ZERO_VALUE || $size !== self::ZERO_VALUE) ? round(($tempTotal / $size), self::NUM_OF_DECIMAL) : self::ZERO_VALUE;
	}
}