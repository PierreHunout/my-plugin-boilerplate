<?php

/**
 * @package WpPluginBoilerplate
 */

namespace WpPluginBoilerplate;

class Tools {

	/**
     * Get data from JSON
     * 
     * @since 1.0.0
     * 
     * @param $url string
     * @param $object string
     * @return object
     */
    public static function get_data($url, $object)
    {
        $data	= self::get_json($url);

		if(empty($data)){
			return;
		}

        $result	= $data->$object;

		if(empty($result)){
			return;
		}

		return $result;
    }

    /**
     * Get JSON from Habit
     * 
     * @since 1.0.0
     * 
     * @param $url string
     * @return object
     */
    public static function get_json($url)
    {
		if(empty($url)){
			return;
		}
        $json	= file_get_contents($url);

		if(empty($json)){
			return;
		}

        $object	= json_decode($json);

        return $object;
    }
}