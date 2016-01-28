<?php

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// delete plugin options from database
$option_name = 'pspb_options';
 
delete_option( $option_name );