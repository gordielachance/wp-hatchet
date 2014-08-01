<?php

class Hatchet_Shortcodes{
    
    function __construct() {

        add_shortcode( 'hatchet', array($this, 'shortcode_widget' ));

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

new Hatchet_Shortcodes();

