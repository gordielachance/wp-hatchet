<?php
/*
Plugin Name: Hatchet.is
Description: Enables shortcodes to embed playlists, albums and single tracks from Hatchet.is, and other functions to communicate with the Hatchet.is API.
Version: 0.1.0
Author: G.Breant
Author URI: http://radios.pencil2d.org
Plugin URI: https://wordpress.org/plugins/wp-hatchet
License: GPL2
*/

class Hatchet {

    /** Version ***************************************************************/

    /**
    * @public string plugin version
    */
    public $version = '0.1.0';

    /**
    * @public string plugin DB version
    */
    public $db_version = '010';

    /** Paths *****************************************************************/

    public $file = '';

    /**
     * @public string Basename of the plugin directory
     */
    public $basename = '';

    /**
     * @public string Absolute path to the plugin directory
     */
    public $plugin_dir = '';

    private static $instance;

    var $meta_key_db_version = 'hatchet-db';
    var $meta_key_options = 'hatchet-options';

    public static function instance() {
            if ( ! isset( self::$instance ) ) {
                    self::$instance = new Hatchet;
                    self::$instance->setup_globals();
                    self::$instance->includes();
                    self::$instance->setup_actions();
            }
            return self::$instance;
    }

    /**
     * A dummy constructor to prevent it from being loaded more than once.
     *
     * @since bbPress (r2464)
     * @see bbPress::instance()
     * @see bbpress();
     */
    private function __construct() { /* Do nothing here */ }

    function setup_globals() {

            /** Paths *************************************************************/
            $this->file       = __FILE__;
            $this->basename   = plugin_basename( $this->file );
            $this->plugin_dir = plugin_dir_path( $this->file );
            $this->plugin_url = plugin_dir_url ( $this->file );

            //options
            $this->options_default = array(
                'api_username'   => null,
                'api_password'   => null

            );

        $options = get_option( $this->meta_key_options, $this->options_default );
        $this->options = apply_filters('hatchet_options',$options);
        
    }

    function includes(){

        require( $this->plugin_dir . 'hatchet-templates.php' );
        require( $this->plugin_dir . 'hatchet-widget.php' );
        require( $this->plugin_dir . 'hatchet-API.php' );

        //admin
        if(is_admin()){
            require($this->plugin_dir . 'hatchet-admin-options.php');
        }
        
    }

    function setup_actions(){    

        add_action( 'plugins_loaded', array($this, 'upgrade'));//install and upgrade
        add_shortcode( 'hatchet', array($this, 'shortcode_widget' ));
        
    }
        
    function upgrade(){
        global $wpdb;
        
        
        $current_version = get_option($this->meta_key_db_version);

        if ( $current_version==$this->db_version ) return;

        //install
        if(!$current_version){
            //handle SQL
            //require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            //dbDelta($sql);
            //add_option($option_name,$this->get_default_settings()); // add settings
        }

        //upgrade DB version
        update_option($this->meta_key_db_version, $this->db_version );//upgrade DB version
    }
    
    function shortcode_widget( $args, $item_url = null) {

        // Attributes
        extract( shortcode_atts(
                array(
                ), $args )
        );

        $item = new Hatchet_Widget($args, $item_url);
        return $item->get_html();
    }
    
}

/**
 * The main function responsible for returning the one true bbPress Instance
 * to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 *
 * @return The one true Instance
 */

function hatchet() {
	return Hatchet::instance();
}

hatchet();


?>