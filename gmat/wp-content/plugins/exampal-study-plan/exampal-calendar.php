<?php
/*
Plugin Name: Exampal Plan Calendar
Plugin URI: exampal.com
Description: Custom Plugin.
Text Domain: exampal
Domain Path: /lang
Author: Exampal
Author URI: http://exampal.com
Version: 1.0
*/

// Make sure that no info is exposed if file is called directly
if ( !function_exists( 'add_action' ) ) {
    echo "This page cannot be called directly.";
    exit;
}

// Define some useful constants that can be used by functions
if ( ! defined( 'WP_CONTENT_URL' ) ) {
    if ( ! defined( 'WP_SITEURL' ) ) define( 'WP_SITEURL', get_option("siteurl") );
    define( 'WP_CONTENT_URL', WP_SITEURL . '/wp-content' );
}
if ( ! defined( 'WP_SITEURL' ) ) define( 'WP_SITEURL', get_option("siteurl") );
if ( ! defined( 'WP_CONTENT_DIR' ) ) define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) ) define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) ) define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

if ( basename(dirname(__FILE__)) == 'plugins' )
    define("EXAMPAL_FOLDER",'');
else define("EXAMPAL_FOLDER" , basename(dirname(__FILE__)));
define("EXAMPAL_PATH", WP_PLUGIN_URL . "/" . EXAMPAL_FOLDER);
define("EXAMPAL_DIR", WP_PLUGIN_DIR . "/" . EXAMPAL_FOLDER);


require_once(EXAMPAL_DIR.'/includes/exampal-functions.php');
require_once(EXAMPAL_DIR.'/includes/exampal-mailchimp.php');
require_once(EXAMPAL_DIR.'/includes/exampal-options.php');
require_once(EXAMPAL_DIR.'/includes/exampal-shortcodes.php');
require_once(EXAMPAL_DIR.'/includes/exampal-calendar.php');
require_once(EXAMPAL_DIR.'/includes/exampal-export-csv.php');
require_once(EXAMPAL_DIR.'/includes/exampal-export-google.php');




//Load styles and scripts
function exampal_enqueues() {
    // Styles
    if (!wp_style_is( 'jquery-ui')) {
        wp_enqueue_style('jquery-ui', EXAMPAL_PATH . '/assets/css/jquery-ui.css');
    }
    if (!wp_style_is( 'bootstrap')) {
        wp_enqueue_style('bootstrap', EXAMPAL_PATH . '/assets/css/bootstrap.min_.css');
    }
    if (!wp_style_is( 'bootstrap-select')) {
        wp_enqueue_style('bootstrap-select', EXAMPAL_PATH . '/assets/css/bootstrap-select.min_.css');
    }
    wp_enqueue_style('exampal-css', EXAMPAL_PATH . '/assets/css/exampal-style.css');
   

    

    /* Scripts */
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-datepicker');

    if (!wp_script_is( 'bootstrap')) {
        wp_enqueue_script( 'bootstrap', EXAMPAL_PATH . '/assets/js/bootstrap.min_.js');
    }
    if (!wp_script_is( 'bootstrap-select')) {
        wp_enqueue_script( 'bootstrap-select', EXAMPAL_PATH . '/assets/js/bootstrap-select.min_.js');
    }

    if (!wp_script_is( 'jquery-validate')) {
        wp_enqueue_script( 'jquery-validate', EXAMPAL_PATH . '/assets/js/jquery.validate.min.js');
    }

    wp_register_script('exampal-js', EXAMPAL_PATH . '/assets/js/exampal-main.js');

    wp_localize_script( 'exampal-js', 'exampalGlobal', 	array( 	'ajaxurl' => admin_url( 'admin-ajax.php' ),                                                                     'ajax_nonce_csv' => wp_create_nonce('exampal-export-csv-nonce')));
    wp_enqueue_script('exampal-js');



}

add_action('wp_enqueue_scripts', 'exampal_enqueues', 100);




// Load styles and scripts to admin options page
function exampal_admin_scripts_styles( $hook ) {
    $screen = get_current_screen();

    if (isset($screen->id) && ($screen->id == 'toplevel_page_exampal-plan-calendar-settings')) {
        wp_enqueue_script('exampal-admin-js', EXAMPAL_PATH . '/assets/js/exampal-admin.js');
    }

}
add_action( 'admin_enqueue_scripts', 'exampal_admin_scripts_styles' );

// Create page for plan calendar on activation
register_activation_hook( __FILE__, 'exampal_create_plan_page_on_activation' );

