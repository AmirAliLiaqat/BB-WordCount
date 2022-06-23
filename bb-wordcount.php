<?php
/***
 * Plugin Name: BB Word Count
 * Plugin URI: https://amir.bytebunch.com/plugins
 * Author: Byte Ki Duniya
 * Author URI: https://amir.bytebunch.com
 * Description: That plugin which is used to count the word and characters of the post. And also tell us the time which is required to read this post.
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

class BBWordCount {

    /******************************* Adding all actions and filters ************************************/
    function __construct() {
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue') );
        add_action( 'admin_init', array($this, 'settings') );
        add_action( 'admin_menu', array($this, 'menu_page') );
        add_filter( 'the_content', array($this, 'ifWrap') );
    }

    /******************************* Enqueue all styles and scripts ************************************/
    function enqueue() {
        wp_enqueue_style( 'bb-style', PLUGIN_URL . 'assets/css/style.css' );
        wp_enqueue_script( 'bb-script', PLUGIN_URL . 'assets/js/script.js' );
    }

    /******************************* Adding new settings, sections and fields ************************************/
    function settings() {
        add_settings_section( 'wc_first_section', null, null, 'word_count_settings' );

        add_settings_field( 'wc_location', 'Display Location', array($this, 'location_html'), 'word_count_settings', 'wc_first_section' );
        register_setting( 'wordCount', 'wc_location', array('sanitize_callback' => array($this, 'sanitize_location'), 'default' => '0') );

        add_settings_field( 'wc_headline', 'Headline Text', array($this, 'headline_html'), 'word_count_settings', 'wc_first_section' );
        register_setting( 'wordCount', 'wc_headline', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Statistics') );

        add_settings_field( 'wc_wordcount', 'Word count', array($this, 'wordcount_html'), 'word_count_settings', 'wc_first_section' );
        register_setting( 'wordCount', 'wc_wordcount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1') );

        add_settings_field( 'wc_charactercount', 'Character count', array($this, 'charactercount_html'), 'word_count_settings', 'wc_first_section' );
        register_setting( 'wordCount', 'wc_charactercount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1') );

        add_settings_field( 'wc_readtime', 'Read time', array($this, 'readtime_html'), 'word_count_settings', 'wc_first_section' );
        register_setting( 'wordCount', 'wc_readtime', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1') );
    }
    
    /******************************* Callback function for wc_location field ************************************/
    function location_html() { ?>
        <select name="wc_location">
            <option value="0" <?php selected( get_option('wc_location'), '0' ) ?>>Beginning of post</option>
            <option value="1" <?php selected( get_option('wc_location'), '1' ) ?>>End of post</option>
        </select>
    <?php }

    /******************************* Callback function for wc_location sanitize_callback ************************************/
    function sanitize_location($input) {
        if($input != '0' AND $input != '1') {
            add_settings_error( 'wc_location', 'wc_location_error', 'Display location must be either beginning or end...' );
            return get_option('wc_location');
        }
        return $input;
    }

    /******************************* Callback function for wc_headline field ************************************/
    function headline_html() { ?>
        <input type="text" name="wc_headline" value="<?php echo esc_attr(get_option('wc_headline')); ?>">
    <?php }

    /******************************* Callback function for wc_wordcount field ************************************/
    function wordcount_html() { ?>
        <input type="checkbox" name="wc_wordcount" value="1" <?php checked(get_option('wc_wordcount'), '1'); ?>>
    <?php }

    /******************************* Callback function for wc_charactercount field ************************************/
    function charactercount_html() { ?>
        <input type="checkbox" name="wc_charactercount" value="1" <?php checked(get_option('wc_charactercount'), '1'); ?>>
    <?php }

    /******************************* Callback function for wc_readtime field ************************************/
    function readtime_html() { ?>
        <input type="checkbox" name="wc_readtime" value="1" <?php checked(get_option('wc_readtime'), '1'); ?>>
    <?php }

    /******************************* Adding new options pages ************************************/
    function menu_page() {
        add_options_page( 'Word Count Settings', 'Word Count', 'manage_options', 'word_count_settings', array($this, 'menu_page_html') );
    }

    /******************************* Callback function for menu page ************************************/
    function menu_page_html() { ?>
        <h1 style="background: #000; color: #fff; text-align:center; margin:20px; padding:20px;">Word Count Settings</h1>
        <div class="wrap" style="background:#fff; margin:20px; padding:20px; font-style:italic;">
            <form action="options.php" method="post">
                <?php
                    settings_fields( 'wordCount' );
                    do_settings_sections( 'word_count_settings' );
                    submit_button();
                ?>
            </form>
        </div><!--wrap-->
    <?php }

    /******************************* Creating new function for showing Statistics in posts ************************************/
    function ifWrap($content) {
        if( is_main_query() AND is_single() AND 
        (
            get_option('wc_wordcount', '1') OR 
            get_option('wc_charactercount', '1') OR 
            get_option('wc_readtime', '1')
        ) ) {
            return $this->create_html($content);
        }
        return $content;
    }

    /******************************* Creating new function for Statistics html ************************************/
    function create_html($content) {
        $html = '<h3>' .get_option('wc_headline', 'Post Statistics'). '</h3><p>';

        // get word count once because both wordcount and read time will need it.
        if(get_option('wc_wordcount', '1') OR get_option('wc_readtime', '1')) {
            $wordcount = str_word_count(strip_tags($content));
        }

        if(get_option('wc_wordcount', '1')) {
            $html .= 'This post has ' . $wordcount . ' words.</br>';
        }

        if(get_option('wc_charactercount', '1')) {
            $html .= 'This post has ' . strlen(strip_tags($content)) . ' characters.</br>';
        }

        if(get_option('wc_readtime', '1')) {
            $html .= 'This post will take about ' . round($wordcount/255) . ' minute(s) to read.</br>';
        }

        $html .= '</p>';

        if(get_option('wc_location', '0') == '0') {
            return $html . $content;
        }
        return $content . $html;
    }

}
$bbWordCount = new BBWordCount();