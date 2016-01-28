<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Page Scroll Progres Bar
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) die;

class Page_Scroll_Progress_Bar_Admin{

	/**
     * Options Page title/menu title
     * @var string
     */
	protected $admin_page_title;
	protected $admin_menu_title;

	/* Allow only one instance of this class. */
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

		$this->admin_page_title ='Page Scroll Progress Bar Settings';
		$this->admin_menu_title ='Scroll Bar';

		// Initialize our hooks
		$this->hooks();

	}

	/**
	 * Initiate our hooks
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'admin_init', array( $this, 'set_default_values' ) );
		add_action( 'admin_menu', array( $this, 'plugin_add_options'));
		add_action( 'admin_init', array( $this, 'page_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_pspb_scripts' ) );	

	}

	/**
	 * Set plugin default values
	 * @since    1.0.0
	 */
	public function set_default_values(){

		$pspb_defaults = array(
			'pspb_position'=>'top',
			'pspb_height'=> 20,
			'pspb_offset'=> 0,
			'pspb_rail_color'=> '#2c3e50',
			'pspb_fill_color'=> '#e74c3c',
			'pspb_hide_on_homepage' => 0,
			'pspb_exclude_post_types' => null,
			'pspb_exclude_by_id' => null,
			'pspb_hide_on_mobile' => 0,
			'pspb_reverse' => 0,
			'pspb_transition' => 'pspb_easing',
		);

		add_option( 'pspb_options', $pspb_defaults );

	}

	/**
	 * Add Options Page
	 * @since    1.0.0
	 */
	public function plugin_add_options() {

		add_submenu_page( 'options-general.php', $this->admin_page_title, $this->admin_menu_title, 'manage_options', 'pspb-options', array(&$this, 'render_options_page'));
		
	}

	/**
	 * Options Page content
	 * @since  1.0.0
	 */
	public function render_options_page() {

		$this->options = get_option( 'pspb_options' );
		?>

		<div class="wrap pspb-options-wrap">
			<h2><span class="dashicons dashicons-admin-settings"></span> <?php echo esc_html( get_admin_page_title() ); ?></h2>            
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'pspb_option_group' );   
				do_settings_sections( 'pspb_settings' );
				submit_button(); 
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Add options
	 * @since  1.0.0
	 */
	public function page_init() {

		register_setting(
			'pspb_option_group', // Option group
			'pspb_options', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'pspb_general_settings', // ID
			'Enter your settings below:', // Title
			array( $this, 'print_section_info' ), // Callback
			'pspb_settings' // Page
		); 

		// Add Scroll Bar position field
		add_settings_field(
			'pspb_position', // ID
			'Scroll Bar Position', // Title 
			array( $this, 'pspb_position_callback' ), // Callback
			'pspb_settings', // Page
			'pspb_general_settings' // Section    
		);

		// Add Scroll Bar height field
		add_settings_field(
			'pspb_height', // ID
			'Scroll Bar Height', // Title 
			array( $this, 'pspb_height_callback' ), // Callback
			'pspb_settings', // Page
			'pspb_general_settings' // Section            
		);

		// Add Scroll Bar offset field
		add_settings_field(
			'pspb_offset', // ID
			'Scroll Bar Offset', // Title 
			array( $this, 'pspb_offset_callback' ), // Callback
			'pspb_settings', // Page
			'pspb_general_settings' // Section          
		);

		// Add Scroll Bar rail color field
		add_settings_field(
			'pspb_rail_color', // ID
			'Choose rail color', // Title 
			array( $this, 'pspb_rail_color_callback' ), // Callback
			'pspb_settings', // Page
			'pspb_general_settings' // Section           
		);

		// Add Scroll Bar fill color field
		add_settings_field(
			'pspb_fill_color', // ID
			'Choose fill color', // Title 
			array( $this, 'pspb_fill_color_callback' ), // Callback
			'pspb_settings', // Page
			'pspb_general_settings' // Section     
		);

		// Add option to hide Scroll Bar on Homepage
		add_settings_field(
			'pspb_hide_on_homepage', // ID
			'Hide Scroll Bar on Homepage?', // Title 
			array( $this, 'pspb_hide_on_homepage_callback' ), // Callback
			'pspb_settings', // Page
			'pspb_general_settings' // Section     
		); 

		// Add exclude on Post Types field
		add_settings_field(
			'pspb_exclude_post_types', // ID
			'Exclude on:', // Title 
			array( $this, 'pspb_exclude_post_types_callback' ), // Callback
			'pspb_settings', // Page
			'pspb_general_settings' // Section 
		);

		// Add exclude by ID field
		add_settings_field(
			'pspb_exclude_by_id', // ID
			'Exclude by ID\'s:', // Title 
			array( $this, 'pspb_exclude_by_id_callback' ), // Callback
			'pspb_settings', // Page
			'pspb_general_settings' // Section  
		);

		// Add option to hide Scroll Bar on mobile devices
		add_settings_field(
			'pspb_hide_on_mobile', // ID
			'Hide Scroll Bar on mobile devices?', // Title 
			array( $this, 'pspb_hide_on_mobile_callback' ), // Callback
			'pspb_settings', // Page
			'pspb_general_settings' // Section   
		);
 
		// Add option to reverse Scroll Bar
		add_settings_field(
			'pspb_reverse', // ID
			'Reverse Scroll Bar?', // Title 
			array( $this, 'pspb_reverse_callback' ), // Callback
			'pspb_settings', // Page
			'pspb_general_settings' // Section         
		);

		// Add Scroll Bar effect field
		add_settings_field(
			'pspb_transition', // ID
			'Select Scroll Bar transition', // Title 
			array( $this, 'pspb_transition_callback' ), // Callback
			'pspb_settings', // Page
			'pspb_general_settings' // Section    
		);

	}

	/**
     * Sanitize each setting field as needed
     * @since  1.0.0
     * @param array $input Contains all settings fields as array keys
     */
	public function sanitize( $input ){

		// rgba and hex color pattern
		$rgbaPattern = '/\A^rgba\(([0]*[0-9]{1,2}|[1][0-9]{2}|[2][0-4][0-9]|[2][5][0-5])\s*,\s*([0]*[0-9]{1,2}|[1][0-9]{2}|[2][0-4][0-9]|[2][5][0-5])\s*,\s*([0]*[0-9]{1,2}|[1][0-9]{2}|[2][0-4][0-9]|[2][5][0-5])\s*,\s*([0-9]*\.?[0-9]+)\)$\z/im';
		$hexPattern  = '|^#([A-Fa-f0-9]{3}){1,2}$|';

		//create empty array of new values
		$new_input = array();

		// sanitize Progress Bar position
		if( isset( $input['pspb_position'] ) )
			$new_input['pspb_position'] = sanitize_text_field( $input['pspb_position'] );

		// sanitize Progress Bar height
		if( isset( $input['pspb_height'] ) )
			$new_input['pspb_height'] = absint( $input['pspb_height'] );

		// sanitize Progress Bar offset
		if( isset( $input['pspb_offset'] ) )
			$new_input['pspb_offset'] = absint( $input['pspb_offset'] );

		// sanitize Progress Bar rail color
		if( isset( $input['pspb_rail_color'] ) ){

			if(preg_match($rgbaPattern, $input['pspb_rail_color']) || preg_match($hexPattern, $input['pspb_rail_color'] )) {
				$new_input['pspb_rail_color'] = $input['pspb_rail_color'];
			} else {
				$new_input['pspb_rail_color'] = '#2c3e50';
			}

		}

		// sanitize Progress Bar fill color
		if( isset( $input['pspb_fill_color'] ) ){

			if(preg_match($rgbaPattern, $input['pspb_fill_color']) || preg_match($hexPattern, $input['pspb_fill_color'] )) {
				$new_input['pspb_fill_color'] = $input['pspb_fill_color'];
			} else {
				$new_input['pspb_fill_color'] = '#e74c3c';
			}

		}

		// sanitize Progress Bar show/hide on homepage option
		if( isset( $input['pspb_hide_on_homepage'] ) )
			$new_input['pspb_hide_on_homepage'] = absint( $input['pspb_hide_on_homepage'] );
		else
			$new_input['pspb_hide_on_homepage'] = 0;

		// sanitize Progress Bar excluded Post Types
		if( isset( $input['pspb_exclude_post_types'] ) ){
			$new_input['pspb_exclude_post_types'] = $input['pspb_exclude_post_types'];
		} else {
			$new_input['pspb_exclude_post_types'] = null;
		}

		// sanitize Progress Bar excluded Post IDs
		if( isset( $input['pspb_exclude_by_id'] ) ){
			$new_input['pspb_exclude_by_id'] = $input['pspb_exclude_by_id'];
		} else {
			$new_input['pspb_exclude_by_id'] = null;
		}

		// sanitize Progress Bar show/hide on mobile devices
		if( isset( $input['pspb_hide_on_mobile'] ) )
			$new_input['pspb_hide_on_mobile'] = absint( $input['pspb_hide_on_mobile'] );
		else
			$new_input['pspb_hide_on_mobile'] = 0;

		// sanitize reverse Scroll Bar checkbox field
		if( isset( $input['pspb_reverse'] ) )
			$new_input['pspb_reverse'] = absint( $input['pspb_reverse'] );
		else
			$new_input['pspb_reverse'] = 0;

		// sanitize Progress Bar transition
		if( isset( $input['pspb_transition'] ) )
			$new_input['pspb_transition'] = sanitize_text_field( $input['pspb_transition'] );

		return $new_input;

	}

	/** 
     * Print the Section text
     * @since  1.0.0
     */
	public function print_section_info() {
		// leave empty
	}

	// Progress Bar position callback
	public function pspb_position_callback() { ?>

		<select id="pspb_position" name='pspb_options[pspb_position]'>
			<option value='top' <?php selected( $this->options['pspb_position'], 'top' ); ?>>Top</option>
			<option value='bottom' <?php selected( $this->options['pspb_position'], 'bottom' ); ?>>Bottom</option>
		</select>

	<?php }

	// Progress Bar height callback
	public function pspb_height_callback() {

		printf(
			'<input type="text" id="rpbs_height" name="pspb_options[pspb_height]" value="%s" /> px',
			isset( $this->options['pspb_height'] ) ? esc_attr( $this->options['pspb_height']) : ''
		);

	}

	// Progress Bar offset callback
	public function pspb_offset_callback()
	{
		printf(
			'<input type="text" id="rpbs_offset" name="pspb_options[pspb_offset]" value="%s" /> px',
			isset( $this->options['pspb_offset'] ) ? esc_attr( $this->options['pspb_offset']) : ''
		);
	}

	// Progress Bar rail color callback
	public function pspb_rail_color_callback() {

		printf(
			'<input type="text" class="rpsb-color-picker rail-color-picker" id="rail_color" name="pspb_options[pspb_rail_color]" data-alpha="true" value="%s" />',
			isset( $this->options['pspb_rail_color'] ) ?  $this->options['pspb_rail_color'] : ''
		);

	}

	// Progress Bar fill color callback
	public function pspb_fill_color_callback() {

		printf(
			'<input type="text" class="rpsb-color-picker fill-color-picker" id="fill_color" name="pspb_options[pspb_fill_color]" data-alpha="true" value="%s" />',
			isset( $this->options['pspb_fill_color'] ) ?  $this->options['pspb_fill_color'] : ''
		);

	}

	// Progress Bar show/hide on hompage callback
	public function pspb_hide_on_homepage_callback() {

		printf(
			'<input type="checkbox" id="hide_on_homepage" name="pspb_options[pspb_hide_on_homepage]" %s value="%s" />',
			checked( $this->options['pspb_hide_on_homepage'], 1, false ),
			isset( $this->options['pspb_hide_on_homepage'] ) ? esc_attr( $this->options['pspb_hide_on_homepage']) : ''
		);

	}

	// Progress Bar excluded Post Types input callback
	public function pspb_exclude_post_types_callback() { ?>

		<select multiple id="include_post_types" name='pspb_options[include_post_types][]'>

			<?php

				$args = array(
					'public' => true,
				);

				$output = 'objects';

				$post_types = get_post_types( $args, $output );

				foreach ( $post_types  as $post_type ) {

					echo '<option value="' . $post_type->name . '">' . $post_type->name . '</option>';

				}

			?>

		</select>

	<?php }

	// Progress Bar posts excluded by ID callback
	public function pspb_exclude_by_id_callback() { ?>

		<select multiple id="exclude_by_id" name='pspb_options[exclude_by_id][]'>

			<?php

				$args = array(
					'public' => true,
				);

				$output = 'objects';

				$post_types = get_post_types( $args, $output );

				//remove attachment post type from array because we don't need it here
				unset( $post_types[ 'attachment' ] );

				foreach ( $post_types  as $post_type ) {

					echo '<optgroup label="' . $post_type->name . '">';

					$args = array (
						'post_type'              => $post_type->name,
						'post_status'            => 'publish',
						'posts_per_page'         => '-1',
						'order'                  => 'DESC',
						'orderby'                => 'date',
					);

					// The Query
					$query = new WP_Query( $args );

					if ( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$query->the_post();

							echo '<option value="' . get_the_ID() . '">' . get_the_title() . ' (ID: '.get_the_ID().')</option>';

						}
					}

					wp_reset_postdata();

					echo '</optgroup>';

				}

			?>

		</select>

	<?php }

	// Progress Bar show/hide on mobile checkbox callback
	public function pspb_hide_on_mobile_callback() {

		printf(
			'<input type="checkbox" id="hide_on_mobile" name="pspb_options[pspb_hide_on_mobile]" %s value="%s" />',
			checked( $this->options['pspb_hide_on_mobile'], 1, false ),
			isset( $this->options['pspb_hide_on_mobile'] ) ? esc_attr( $this->options['pspb_hide_on_mobile']) : ''
		);

	}

	// Progress Bar reverse checkbox callback
	public function pspb_reverse_callback() {

		printf(
			'<input type="checkbox" id="pspb_reverse" name="pspb_options[pspb_reverse]" %s value="%s" />',
			checked( $this->options['pspb_reverse'], 1, false ),
			isset( $this->options['pspb_reverse'] ) ? esc_attr( $this->options['pspb_reverse']) : ''
		);

	}

	// Progress Bar transition callback
	public function pspb_transition_callback() { ?>

		<select id="pspb_transition" name='pspb_options[pspb_transition]'>
			<option value='pspb-easing' <?php selected( $this->options['pspb_transition'], 'pspb-easing' ); ?>>Easing</option>
			<option value='pspb-linear' <?php selected( $this->options['pspb_transition'], 'pspb-linear' ); ?>>Linear</option>
			<option value='pspb-ease-in' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in' ); ?>>Ease In</option>
			<option value='pspb-ease-in-out' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-out' ); ?>>Ease In Out</option>
			<option value='pspb-ease-in-quad' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-quad' ); ?>>Ease In Quad</option>
			<option value='pspb-ease-in-cubic' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-cubic' ); ?>>Ease In Cubic</option>
			<option value='pspb-ease-in-quart' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-quart' ); ?>>Ease In Quart</option>
			<option value='pspb-ease-in-quint' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-quint' ); ?>>Ease In Quint</option>
			<option value='pspb-ease-in-sine' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-sine' ); ?>>Ease In Sine</option>
			<option value='pspb-ease-in-expo' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-expo' ); ?>>Ease In Expo</option>
			<option value='pspb-ease-in-circ' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-circ' ); ?>>Ease In Circ</option>
			<option value='pspb-ease-in-back' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-back' ); ?>>Ease In Back</option>
			<option value='pspb-ease-out-quad' <?php selected( $this->options['pspb_transition'], 'pspb-ease-out-quad' ); ?>>Ease Out Quad</option>
			<option value='pspb-ease-out-cubic' <?php selected( $this->options['pspb_transition'], 'pspb-ease-out-cubic' ); ?>>Ease Out Cubic</option>
			<option value='pspb-ease-out-quart' <?php selected( $this->options['pspb_transition'], 'pspb-ease-out-quart' ); ?>>Ease Out Quart</option>
			<option value='pspb-ease-in-quint' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-quint' ); ?>>Ease In Quint</option>
			<option value='pspb-ease-out-sine' <?php selected( $this->options['pspb_transition'], 'pspb-ease-out-sine' ); ?>>Ease Out Sine</option>
			<option value='pspb-ease-out-expo' <?php selected( $this->options['pspb_transition'], 'pspb-ease-out-expo' ); ?>>Ease Out Expo</option>
			<option value='pspb-ease-out-circ' <?php selected( $this->options['pspb_transition'], 'pspb-ease-out-circ' ); ?>>Ease Out Circ</option>
			<option value='pspb-ease-out-back' <?php selected( $this->options['pspb_transition'], 'pspb-ease-out-back' ); ?>>Ease Out Back</option>
			<option value='pspb-ease-in-out-quad' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-out-quad' ); ?>>Ease In Out Quad</option>
			<option value='pspb-ease-in-out-cubic' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-out-cubic' ); ?>>Ease In Out Cubic</option>
			<option value='pspb-ease-in-out-quart' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-out-quart' ); ?>>Ease In Out Quart</option>
			<option value='pspb-ease-in-out-quint' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-out-quint' ); ?>>Ease In Out Quint</option>
			<option value='pspb-ease-in-out-sine' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-out-sine' ); ?>>Ease In Out Sine</option>
			<option value='pspb-ease-in-out-expo' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-out-expo' ); ?>>Ease In Out Expo</option>
			<option value='pspb-ease-in-out-circ' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-out-circ' ); ?>>Ease In Out Circ</option>
			<option value='pspb-ease-in-out-back' <?php selected( $this->options['pspb_transition'], 'pspb-ease-in-out-back' ); ?>>Ease In Out Back</option>
		</select>

	<?php }

	// load necessary styles
	public function load_pspb_scripts(){

		// CSS
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'pspb-select2-styles', plugin_dir_url( __FILE__ ) . 'vendor/select2/select2.css' );
		wp_enqueue_style( 'pspb-admin-styles', plugin_dir_url( __FILE__ ) . 'assets/css/page-scroll-progress-bar-admin.css' );

		// JS
		wp_enqueue_script( 'wp-color-picker-alpha', plugin_dir_url( __FILE__ ) . 'vendor/wp-color-picker-alpha/wp-color-picker-alpha.js', array( 'jquery', 'wp-color-picker' ), '1.2', true );
		wp_enqueue_script( 'pspb-select2-js', plugin_dir_url( __FILE__ ) . 'vendor/select2/select2.min.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'pspb-custom-js', plugin_dir_url( __FILE__ ) . 'assets/js/pspb-script-admin.js', array( 'jquery', 'wp-color-picker', 'wp-color-picker-alpha' ), false, true );

		// LOCALIZE SCRIPT
		$myopts = get_option( 'pspb_options' );
		wp_localize_script( 'pspb-custom-js', 'pspb_params', $myopts );

	}

}