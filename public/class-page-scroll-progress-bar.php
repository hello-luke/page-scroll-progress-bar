<?php

/**
 * Define the main plugin class
 * @since 1.0.0
 * @package Reading_Progress_Scroll_Bar
 */

// Don't allow this file to be accessed directly.
if ( ! defined( 'WPINC' ) ) die;


class Page_Scroll_Progress_Bar{

	/**
	 * Allow only one instance of this class.
	 * @since    1.0.0
	 */
	protected static $instance = null;

	public static function get_instance() {
		
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}

	/**
	 * Initialize the class
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->hooks();

	}

	/**
	 * Add hook
	 * @since    1.0.0
	 */
	public function hooks(){
		add_action( 'wp_enqueue_scripts', array(&$this, 'rbsp_is_allowed'));
	}

	/**
	 * Load necessary scripts
	 * @since    1.0.0
	 */
	public function load_scripts() {
		 
		// CSS
		wp_enqueue_style( 'pspb-styles', plugin_dir_url( __FILE__ ) . 'assets/css/progress.css' );

		// JS
		if ( ! wp_script_is( 'jquery' )) wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'isMobile', plugin_dir_url( __FILE__ ) . 'assets/js/isMobile.min.js', array( 'jquery' ), false, true);
		wp_enqueue_script( 'pspb-js', plugin_dir_url( __FILE__ ) . 'assets/js/progress.js', array( 'jquery' ), false, true);

		// WP LOCALIZE SCRIPT
		$pspb_opts = get_option( 'pspb_options' );
    	
		//check if user is logged in and add it to our array
		$pspb_opts['is_user_logged_in'] = is_user_logged_in();
		wp_localize_script( 'pspb-js', 'pspb_val', $pspb_opts );

	}

	/**
	 * Enable scipts on desired pages
	 * @since    1.0.0
	 */
	public function rbsp_is_allowed(){
		global $post;
		$opts = get_option( 'pspb_options' );
		$hide_on_home = $opts['pspb_hide_on_homepage'];
		$excluded_post_types = $opts['pspb_exclude_post_types'];
		$excluded_post_ids = $opts['pspb_exclude_by_id'];

		// Enable/disable plugin on homepage
		if (isset($hide_on_home) && 1 == $hide_on_home){

				if( is_home() || is_front_page() ){
					return false;
				}
				
		}

		// Enable/disable plugin on certain Post Types
		if ( ! empty($excluded_post_types) ){

			foreach ($excluded_post_types as $excluded_post_type) {
				if (get_post_type() === $excluded_post_type && is_archive()) {
					return false;
				}
			}
			
			
			if( is_singular( $excluded_post_types ) ) {
				// do not render the scroll bar 
				return false;
			}
				
		}

		// Enable/disable plugin on post ID's
		if ( ! empty($excluded_post_ids) ){

			if( in_array( $post->ID , $excluded_post_ids ) ) {
					return false;
			}
			
		}

		// load the scripts!
		$this->load_scripts();

	}

}