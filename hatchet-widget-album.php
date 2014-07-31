<?php

class Hatchet_Widget_Album extends Hatchet_Widget{
    
    var $style_default = array(
        'width'=>550, 
        'height'=>430,
        'artist'=>null,
        'title'=>null
    );
        
    var $options_default = array(
        'artist'=>false,
        'album'=>false
    );

    
    function extract_url_info($url){
        $args = hatchet_extract_url_album($url);
        return $args;
    }
    
    function get_html(){

        
        $url = hatchet_get_album_link($this->options['artist'],$this->options['album']);
        if (!$url) return false;

        return $url;
        
        //TO FIX
        
        /*
        
        ob_start();
        ?>
        <iframe src="<?php echo $url;?>" width="<?php echo $this->style['width'];?>" height="<?php echo $this->style['height'];?>" scrolling="no" frameborder="0" allowtransparency="true" ></iframe>
        <?php
        
        $iframe = ob_get_contents();
        ob_end_clean();
        return $iframe;
         */
    }
}

?>
