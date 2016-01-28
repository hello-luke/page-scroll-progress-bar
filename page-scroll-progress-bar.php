<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * Plugin Name:       Page Scroll Progress Bar
 * Description:       Displays a vertical progress bar which shows user location on a page. 
 * Version:           1.0.0
 * Author:            Łukasz Kowalski
 * Author URI:        http://helloluke.eu/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Run the plugin
 * @since 1.0.0
 */
function pspb_run() {

	if (is_admin()){

		require( plugin_dir_path( __FILE__ ) . 'admin/class-page-scroll-progress-bar-admin.php' );
		add_action( 'plugins_loaded', array( 'Page_Scroll_Progress_Bar_Admin', 'get_instance' ) );

	}

	if ( ! is_admin() ){

		require( plugin_dir_path( __FILE__ ) . 'public/class-page-scroll-progress-bar.php' );
		add_action( 'plugins_loaded', array( 'Page_Scroll_Progress_Bar', 'get_instance' ) );

	}

}

pspb_run();