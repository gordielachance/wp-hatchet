<?php

class Hatchet_Widget_Playlist extends Hatchet_Widget{

    var $style_default = array(
        'width'=>550, 
        'height'=>430,
    );
        
    var $options_default = array(
        'hatchet_id'=>null,
        'username'=>null
    );
    
    function extract_url_info($url){
        $args = hatchet_extract_url_playlist($url);
        return $args;
    }
    
    function get_html(){

        $url = hatchet_get_playlist_link($this->options['hatchet_id'], $this->options['username']);
        if (!$url) return false;
        
        return $url;

        $url_args['embed'] = 'true';
        $url = add_query_arg($url_args,$url);
        
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
