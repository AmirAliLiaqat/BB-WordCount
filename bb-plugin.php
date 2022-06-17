<?php
/***
 * Plugin Name: BB Plugin
 * Plugin URI: https://www.bytebunch.com/plugins
 * Author: ByteBunch
 * Author URI: https://www.bytebunch.com
 * Description: My first testing plugin for practice.
 * Version: 1.0.0
 * Licence: GPL v2 or Later
 */

if(!defined('ABSPATH')) {
    die();
}

if(!defined('PLUGIN_PATH')) {
    define('PLUGIN_PATH', plugin_dir_path( __FILE__ ));
}

if(!defined('PLUGIN_URL')) {
    define('PLUGIN_URL', plugin_dir_url( __FILE__ ));
}

if(!defined('PLUGIN')) {
    define('PLUGIN', plugin_basename( __FILE__ ));
}

class BBPlugin {

    /******************************* Adding all actions and filters ************************************/
    function __construct() {
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue') );
    }

    /******************************* Enqueue all styles and scripts ************************************/
    function enqueue() {
        wp_enqueue_style( 'bb-style', PLUGIN_URL . 'assets/css/style.css' );
        wp_enqueue_script( 'bb-script', PLUGIN_URL . 'assets/js/script.js' );
    }

}
$bbPlugin = new BBPlugin();