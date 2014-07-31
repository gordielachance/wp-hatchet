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

function hatchet_extract_url($hatchet_url){

    //remove arguments
    $hatchet_url = strtok($hatchet_url, '?');

    $matches = array();
    $pattern = "#^https?://([a-z0-9-]+\.)*hatchet\.is(/.*)?$#";

    preg_match($pattern,$hatchet_url, $matches);

    if (!isset($matches[2])) return false;

    $suffix = $matches[2];
    return $suffix;

}


/*
 * Sanitize an hatchet ID
 */

function hatchet_sanitize_id($id){
    //TO FIX : there can be underscore too !
    //if (!ctype_alnum($id)) return false;
    return $id;
}

/*
 * Allowed characters for url parts (user/artist/album/track/ids...)
 */
function hatchet_get_pattern($type){
    
    //TO FIX don't work well yet.  Not working for this : Don’t+Try+So+Hardtoto
    $slug_pattern = '([a-zA-Z0-9_+’]*)';
    
    switch ($type){
        case 'username':
            $pattern = "(?:/people/)".$slug_pattern;
        break;
        case 'playlist':
            $pattern = "(?:/people/)".$slug_pattern."(?:/playlists/)".$slug_pattern;
        break;
        case 'artist':
            $pattern = "(?:/music/)".$slug_pattern;
        break;
        case 'album':
            $pattern = "(?:/music/)".$slug_pattern."(?:/)".$slug_pattern;
        break;
        case 'track':
            $pattern = "(?:/music/)".$slug_pattern."(?:/)".$slug_pattern."(?:/)".$slug_pattern;
        break;
    }
    
    $pattern = "@^".$pattern."(?:/)?.*$@";
    
    return $pattern;
    
}

function hatchet_get_artist_patern(){
    
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

function hatchet_extract_url_playlist($url){
    $path = hatchet_extract_url($url);
    $matches = array();
    $pattern = hatchet_get_pattern('playlist');
    preg_match($pattern,$path, $matches);

    if((!isset($matches[1])) || (!isset($matches[2]))) return false;
    
    $result = array(
        'username' => $matches[1],
        'hatchet_id' => $matches[2],
    );

    return $result; 
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

function hatchet_extract_url_user($url){
    $path = hatchet_extract_url($url);
    $matches = array();
    $pattern = hatchet_get_pattern('username');
    preg_match($pattern,$path, $matches);
    if(isset($matches[1])) return $matches[1];
}

/**
 * Get the hatchet link to a track
 * @param type $artist
 * @param type $title
 * @param type $album
 * @return string
 */

function hatchet_get_track_link($artist,$title,$album = '_'){
    
    $album = urlencode($album);
    $title = urlencode($title);
    $hatchet_url = hatchet_get_album_link($artist,$album) . $title;
    return apply_filters('hatchet_get_track_link',$hatchet_url,$artist,$title,$album);
}

function hatchet_extract_url_track($url){
    $path = hatchet_extract_url($url);


    
    $matches = array();
    $pattern = hatchet_get_pattern('track');

    preg_match($pattern,$path, $matches);
    
    if((!isset($matches[1])) || (!isset($matches[2])) || (!isset($matches[3])) ) return false;
    
    $result = array(
        'artist' => $matches[1],
        'album' => $matches[2],
        'track' => $matches[3],
    );

    return $result; 
}

/**
 * Get the hatchet link to an album
 * @param type $artist
 * @param type $album
 * @return string
 */

function hatchet_get_album_link($artist,$album){
    $hatchet_url = hatchet_get_artist_link($artist) . $album .'/';
    return apply_filters('hatchet_get_album_link',$hatchet_url,$artist,$album);
}

function hatchet_extract_url_album($url){
    $path = hatchet_extract_url($url);
    $matches = array();
    $pattern = hatchet_get_pattern('album');
    preg_match($pattern,$path, $matches);

    if((!isset($matches[1])) || (!isset($matches[2]))) return false;
    
    $result = array(
        'artist' => $matches[1],
        'album' => $matches[2],
    );

    return $result; 
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
