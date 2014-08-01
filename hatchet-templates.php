<?php

function hatchet_get_option($name){
    if (!isset(hatchet()->options[$name])) return;
    return hatchet()->options[$name];
}

function hatchet_get_option_default($name){
    if (!isset(hatchet()->options_default[$name])) return;
    return hatchet()->options_default[$name];
}

/*
 * Returns the attributes of an hatchet.is URL
 */

function hatchet_parse_url($url){
    
    $args = array();
    
    $url = parse_url($url);
    if ( (!isset($url['host'])) || (!isset($url['path'])) )  return $args;
    if ($url['host'] != 'hatchet.is') return $args;
    
    $bits = explode('/',$url['path']);
    
    switch ($bits[1]){
        
        case 'people':
            if ( ( isset($bits[2]) && isset($bits[3]) && isset($bits[4]) ) && ( $bits[3] == 'playlists') ){ //playlist
                $args['username'] = $bits[2];
                $args['hatchet_id'] = $bits[4];
            }
        break;
        
        case 'music':
            
            if ( isset($bits[2]) ){ //artist
                $args['artist'] = $bits[2];
            }
            
            if ( isset($bits[3]) ){ //album
                $args['album'] = $bits[3];
            }
            
            if ( isset($bits[4]) ){ //track
                $args['track'] = $bits[4];
            }
            
        break;
        
        
    }
    
    foreach ((array)$args as $slug=>$value){
        $args[$slug] = urldecode($value);
    }
    
    
    return apply_filters('hatchet_parse_url',$args,$url);

}


/*
 * Sanitize an hatchet ID
 */

function hatchet_sanitize_id($id){
    //TO FIX : there can be underscore too !
    //if (!ctype_alnum($id)) return false;
    return $id;
}


/**
 * Get the hatchet link to a playlist
 * @param type $artist
 * @param type $title
 * @param type $album
 * @return string
 */

function hatchet_get_playlist_link($id, $user){
    $hatchet_url = hatchet_get_user_link($user) .'playlists/' . $id;
    return apply_filters('hatchet_get_playlist_link',$hatchet_url,$id, $user);
}

/**
 * Get the hatchet link to a playlist
 * @param type $artist
 * @param type $title
 * @param type $album
 * @return string
 */

function hatchet_get_user_link($user){
    $hatchet_url = sprintf('http://hatchet.is/people/%1$s/',$user);
    return apply_filters('hatchet_get_user_link',$hatchet_url,$user);
}


/**
 * Get the hatchet link to a track
 * @param type $artist
 * @param type $title
 * @param type $album
 * @return string
 */

function hatchet_get_track_link($artist,$title,$album = '_'){
    $title = urlencode($title);
    $hatchet_url = hatchet_get_album_link($artist,$album) . $title;
    return apply_filters('hatchet_get_track_link',$hatchet_url,$artist,$title,$album);
}


/**
 * Get the hatchet link to an album
 * @param type $artist
 * @param type $album
 * @return string
 */

function hatchet_get_album_link($artist,$album){
    $album = urlencode($album);
    $hatchet_url = hatchet_get_artist_link($artist) . $album .'/';
    return apply_filters('hatchet_get_album_link',$hatchet_url,$artist,$album);
}


/**
 * Get the hatchet link to an artist
 * @param type $artist
 * @return string
 */

function hatchet_get_artist_link($artist){
    $artist = urlencode($artist);
    $hatchet_url = sprintf('http://hatchet.is/music/%1$s/',$artist);
    return apply_filters('hatchet_get_artist_link',$hatchet_url,$artist);
}
