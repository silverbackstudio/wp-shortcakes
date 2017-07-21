<?php
/**
 *
 *
 * @package svbk-shortcakes
 * @version 1.6
 */

/**
Plugin Name: Silverback Shortcakes
Plugin URI:
Description: Shortcode UI Helpers
Author: Silverback Studio
Version: 1.6
Author URI: http://www.silverbackstudio.it/
Text Domain: svbk-shortcakes
 */

/**
 *
 * Init function
 */
function svbk_shortcakes_init() {
	load_plugin_textdomain( 'svbk-shortcakes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'plugins_loaded', 'svbk_shortcakes_init' );
