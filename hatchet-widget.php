<?php

class Hatchet_Widget{

    var $style_default = array();
    var $options_default = array(
        'type'          => null,
        'username'      => null,
        'hatchet_id'    => null,
        'artist'        => null,
        'album'         => null,
        'track'         => null,
        'url'           => null
    );
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
        
        return hatchet_parse_url($url);
        
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

        $options = $this->sanitize_options($options);

        return $options;
    }
    
    function get_type($args){
        
        if ( isset($args['username']) && isset($args['hatchet_id']) ){ //playlist
            $type = 'playlist';
        }

        if ( isset($args['artist'])){
            $type = 'artist';
        }

        if ( ( isset($args['artist'])) && ( isset($args['album'])) ){
            $type = 'album';
        }

        if ( ( isset($args['artist'])) && ( isset($args['album'])) && ( isset($args['track']))){
            $type = 'track';
        }

        return $type;
            
    }
    
    function sanitize_style($style){
        
        if ((isset($style['width']))&&(!is_numeric($style['width']))) unset($style['width']);
        if ((isset($style['height']))&&(!is_numeric($style['height']))) unset($style['height']);
        
        $new_style = $style;

        return $new_style;
    }
    
    function sanitize_options($options){
        $new_options = $options;
        
        if ( (isset($new_options['artist'])) && (isset($new_options['track'])) && (!isset($new_options['album'])) ){
            $new_options['album'] = '_';
        }
        
        $new_options['type'] = self::get_type($new_options);
        
        return $new_options;
    }
    
    function get_html(){
        switch ($this->options['type']){
            case 'playlist':
                $html = self::playlist_widget($this->options['username'],$this->options['hatchet_id']);
            break;
            case 'artist':
                $html = self::artist_widget($this->options['artist']);
            break;
            case 'album':
                $html = self::album_widget($this->options['artist'],$this->options['album']);
            break;
            case 'track':
                $html = self::track_widget($this->options['artist'],$this->options['album'],$this->options['track']);
            break;
        }

        return $html;
    }
    
    function html(){
        echo $this->get_html();
    }
    
    function playlist_widget($username,$hatchet_id){
        /*
        ob_start();
        ?>
        <iframe src="<?php echo $url;?>" width="<?php echo $this->style['width'];?>" height="<?php echo $this->style['height'];?>" scrolling="no" frameborder="0" allowtransparency="true" ></iframe>
        <?php
        $iframe = ob_get_contents();
        ob_end_clean();
        return $iframe;
        */
        $output[] = "PLAYLIST WIDGET";
        $output[] = $username;
        $output[] = $hatchet_id;
        
        return implode(',',$output);
    }
    
    function artist_widget($artist){
        $output[] = "ARTIST WIDGET";
        $output[] = $artist;
        
        return implode(' , ',$output);
    }
    
    function album_widget($artist,$album){
        $output[] = "ALBUM WIDGET";
        $output[] = $artist;
        $output[] = $album;
        
        return implode(' , ',$output);
    }
    
    function track_widget($artist,$album,$track){
        $output[] = "TRACK WIDGET";
        $output[] = $artist;
        $output[] = $album;
        $output[] = $track;
        
        return implode(' , ',$output);
    }
    

}

?>
