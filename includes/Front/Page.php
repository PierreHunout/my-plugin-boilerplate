<?php
/**
 * This file is responsible for handling the page functionality in the WordPress plugin.
 * It includes methods to run the page functionality and can be extended in the future.
 * 
 * @package WpPluginBoilerplate
 * @since 1.0.0
 * @version 1.0.0
 * @author Pierre Hunout <https://pierrehunout.com/>
 */

namespace WpPluginBoilerplate\Front;

/**
 * This check prevents direct access to the plugin file,
 * ensuring that it can only be accessed through WordPress.
 * 
 * @since 1.0.0
 */
if (!defined('WPINC')) {
    die;
}

class Page
{
    /**
     * Class Runner for the page functionality.
     * 
     * This function is currently empty and can be extended in the future
     * to include page-related features or hooks.
     * 
     * @return void
     */
    public function run()
    {

    }
}
