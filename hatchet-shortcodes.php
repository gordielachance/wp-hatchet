<?php

class Hatchet_Shortcodes{
    
    function __construct() {

        add_shortcode( 'hatchet-playlist', array($this, 'shortcode_playlist' ));
        add_shortcode( 'hatchet-track', array($this, 'shortcode_track' ));
        add_shortcode( 'hatchet-album', array($this, 'shortcode_album' ));

    }
    
    function shortcode_playlist( $args, $item_url = null) {

        // Attributes
        extract( shortcode_atts(
                array(
                ), $args )
        );

        $item = new Hatchet_Widget_Playlist($args, $item_url);
        return $item->get_html();
    }
        
    function shortcode_track( $args, $item_url = null) {

        // Attributes
        extract( shortcode_atts(
                array(
                ), $args )
        );

        $item = new Hatchet_Widget_Track($args, $item_url);
        return $item->get_html();
    }
        
    function shortcode_album( $args, $item_url = null) {

        // Attributes
        extract( shortcode_atts(
                array(
                ), $args )
        );

        $item = new Hatchet_Widget_Album($args, $item_url);
        return $item->get_html();
    }
    
}

new Hatchet_Shortcodes();

