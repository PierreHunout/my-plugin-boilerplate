<?php
/**
 * This file is responsible for handling the tools functionality in the WordPress plugin.
 * It includes methods to fetch data from a JSON URL and retrieve specific objects from the JSON response
 * and can be extended in the future.
 * 
 * @package WpPluginBoilerplate
 * @since 1.0.0
 * @version 1.0.0
 * @author Pierre Hunout <https://pierrehunout.com/>
 */

namespace WpPluginBoilerplate;

/**
 * This check prevents direct access to the plugin file,
 * ensuring that it can only be accessed through WordPress.
 * 
 * @since 1.0.0
 */
if (!defined('WPINC')) {
	die;
}

class Tools {

    /**
     * Get data from JSON
     * @since 1.0.0
     * @param string $url The URL to fetch the JSON data from.
     * @param string $object The object key to retrieve from the JSON data.
     * @return mixed|null Returns the data from the specified object key in the JSON response, or null if the URL or object is empty,
     *                    or if the data is not found.
     */
    public static function get_data($url, $object)
    {
        if(empty($url) || empty($object)){
            return;
        }
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
     * Fetches JSON data from a given URL.
     * 
     * @param string $url The URL to fetch the JSON data from.
     * @return object|null Returns the decoded JSON object, or null if the URL is empty or the JSON data is not found.
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