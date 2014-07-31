<?php

class Hatchet_Widget{

    var $style_default = array();
    var $options_default = array();
    var $styles = array();
    var $options = array();
    
    function __construct($args = array(), $item_url = false){
        
        //hatchet.is url
        if ($item_url){
            $url_args = self::populate_url($item_url);
            $args = wp_parse_args($args,$url_args);
            $args['url'] = $item_url;
        }

        $this->options = $this->populate_options($args);
        $this->styles = $this->populate_style($args);

    }
    
    function populate_url($url){
        
        $url_args = array();
        
        if ($args = $this->extract_url_info($url)) $url_args = $args;
        
        return $url_args;
        
    }
    
    function populate_style($args){
        
        $style = array();
        
        //clean array
        foreach((array)$this->style_default as $slug=>$default){
            if (isset($args[$slug])) $style[$slug] = $args[$slug];
        }
        
        $style = wp_parse_args($style,(array)$this->style_default);
        return $this->sanitize_style($style);
        
    }
    
    function populate_options($args){
        
        $options = array();
        
        //clean array
        foreach((array)$this->options_default as $slug=>$default){
            if (isset($args[$slug])) $options[$slug] = $args[$slug];
        }
        
        $options = wp_parse_args($options,(array)$this->options_default);
        return $this->sanitize_options($options);
    }
    
    function sanitize_style($style){
        
        if ((isset($style['width']))&&(!is_numeric($style['width']))) unset($style['width']);
        if ((isset($style['height']))&&(!is_numeric($style['height']))) unset($style['height']);
        
        $new_style = $style;

        return $new_style;
    }
    
    function sanitize_options($options){
        $new_options = $options;
        return $new_options;
    }
    
    function html(){
        echo $this->get_html();
    }
    

}

?>
