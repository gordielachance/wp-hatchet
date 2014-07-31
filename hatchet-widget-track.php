<?php

class Hatchet_Widget_Track extends Hatchet_Widget{
    
    var $style_default = array(
        'width'=>200, 
        'height'=>200,
        'autoplay'=>false,
        'disabled_resolvers'=>null
    );
        
    var $options_default = array(
        'hatchet_id'=>null,
        'artist'=>null,
        'album'=>null,
        'track'=>null
    );

    function extract_url_info($url){
        
        $args = hatchet_extract_url_track($url);
        return $args;
    }

    
    function get_html(){
        
        $url = hatchet_get_track_link($this->options['artist'],$this->options['track'],$this->options['album']);
        if (!$url) return false;
        
        return $url;
        
        //TO FIX
        
        $url = 'http://toma.hk/embed.php';
        
        $url_args['artist'] = $this->options['artist'];
        $url_args['title'] = $this->options['title'];
        $url_args['autoplay'] = ($this->options['autoplay']) ? 'true' : 'false';
        
        $disabled_resolvers_str = implode(',',(array)$this->options['disabled_resolvers']);
        
        if ($disabled_resolvers_str)
            $url_args['disabledResolvers'] = $disabled_resolvers_str;
        
        $url = add_query_arg($url_args,$url);

        ob_start();
        ?>
        <iframe src="<?php echo $url;?>" width="<?php echo $this->style['width'];?>" height="<?php echo $this->style['height'];?>" scrolling="no" frameborder="0" allowtransparency="true" ></iframe>
        <?php
        $iframe = ob_get_contents();
        ob_end_clean();
        return $iframe;
    }
}

?>