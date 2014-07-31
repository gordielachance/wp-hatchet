<?php

/**
 * Admin Options Page
 */

class Hatchet_Admin_Options{

    /**
     * Start up
     */
    public function __construct(){
        
        add_action( 'admin_menu', array( $this, 'register_options_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
        //add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
    }

    function enqueue_scripts_styles($hook){
        if ($hook!='settings_page_hatchet-options') return;
        wp_enqueue_script('hatchet-options', hatchet()->plugin_url.'_inc/js/settings.js', array('jquery'),hatchet()->version);
        
    }

    /**
     * Add options page
     */
    public function register_options_page()
    {
        // This page will be under "Settings"
        add_options_page(
                __('Hatchet.is','hatchet'),
                __('Hatchet.is','hatchet'),
                'manage_options',
                'hatchet-options',
                array( $this, 'options_page' )
        );
    }

    /**
     * Options page callback
     */
    public function options_page(){
        // Set class property
        
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php _e('Hatchet Options','hatchet');?></h2>  
            
            
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'hatchet_option_group' );   
                do_settings_sections( 'hatchet-settings-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'hatchet_option_group', // Option group
            hatchet()->meta_key_options, // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'settings_general', // ID
            __('General Options','hatchet'), // Title
            array( $this, 'section_general_desc' ), // Callback
            'hatchet-settings-admin' // Page
        );  
        
        add_settings_section(
            'settings_hatchet', // ID
            __('Hatchet API','hatchet'), // Title
            array( $this, 'hatchet_desc' ), // Callback
            'hatchet-settings-admin' // Page
        );
        
        add_settings_field(
            'api_login', 
            __('Hatchet Login','hatchet'), 
            array( $this, 'api_login_callback' ), 
            'hatchet-settings-admin', 
            'settings_hatchet'
        );
        
        add_settings_field(
            'api_password', 
            __('Hatchet Password','hatchet'), 
            array( $this, 'api_password_callback' ), 
            'hatchet-settings-admin', 
            'settings_hatchet'
        );
        
        add_settings_section(
            'settings_system', // ID
            __('System Options','hatchet'), // Title
            array( $this, 'section_system_desc' ), // Callback
            'hatchet-settings-admin' // Page
        );

        add_settings_field(
            'reset_options', 
            __('Reset Options','hatchet'), 
            array( $this, 'reset_options_callback' ), 
            'hatchet-settings-admin', 
            'settings_system'
        );
        
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ){

        $new_input = array();

        if( isset( $input['reset_options'] ) ){
            
            $new_input = hatchet()->options_default;
            
        }else{ //sanitize values
            
            if( isset( $input['hatchet_username'] ) ){
                $new_input['hatchet_username'] = $input['hatchet_username'];
            }
            
            if( isset( $input['api_password'] ) ){
                $new_input['api_password'] = $input['api_password'];
            }

        }

        //remove default values
        foreach($input as $slug => $value){
            $default = hatchet_get_option_default($slug);
            if ($value == $default) unset ($input[$slug]);
        }

        $new_input = array_filter($new_input);

        return $new_input;
       
    }

    /** 
     * Print the Section text
     */
    public function section_general_desc(){
    }

    public function hatchet_desc(){
        printf(__('If you want to use the Hatchet API, you need an %1$s account.'),'<a href="http://hatchet.is" target="_blank">Hatchet.is</a>');
    }
    
    public function api_login_callback(){
        $option = hatchet_get_option('hatchet_username');
        printf(
            '<input type="text" name="%1$s[hatchet_username]" value="%2$s"/>',
            hatchet()->meta_key_options,
            $option
        );
    }
    
    public function api_password_callback(){
        $option = hatchet_get_option('api_password');
        printf(
            '<input type="password" name="%1$s[api_password]" value="%2$s"/>',
            hatchet()->meta_key_options,
            $option
        );
    }
    
    
    public function section_system_desc(){
    }

    
    public function reset_options_callback(){
        printf(
            '<input type="checkbox" name="%1$s[reset_options]" value="on"/> %2$s',
            hatchet()->meta_key_options,
            __("Reset options to their default values.","ari")
        );
    }
    
}

new Hatchet_Admin_Options();