<?php

/**
 * Class for the Hatchet API
 * https://api.hatchet.is/apidocs/#!/playlists/findPlaylist
 */

class Hatchet_API {
    
    var $username;
    var $password;
    
    function __construct() {
        $this->username = hatchet_get_option('api_login');
        $this->password = hatchet_get_option('api_password');
    }

    /*
     * Get the token that allows us to use the API
     */
    
    function get_access_token(){

        $refresh_token = self::get_refresh_token();
        
        if (is_wp_error($refresh_token)) return $refresh_token;

        $url = 'https://auth.hatchet.is/v1/tokens/refresh/bearer';

        $query_args = array(
            'refresh_token'     => $refresh_token,
            'grant_type'        => 'refresh_token'
        );
        
        $url = add_query_arg( $query_args, $url );
        
        $request_args = array();
        
        $response = wp_remote_post( $url, $request_args );
        if (is_wp_error($response)) return false;
        
        $body = wp_remote_retrieve_body( $response );
        $body = json_decode($body);

        if (!$body->access_token){
            return new WP_Error( 'accesstoken', __( "Error while trying to get the Hatchet access token", "hatchet" ) );
        }
        
        return $body->access_token;
        
    }
    
    /*
     * Get the refresh token (needed for get_access_token())
     */
    
    function get_refresh_token(){
        
        $url = 'https://auth.hatchet.is/v1/authentication/password';

        $query_args = array(
            'username'   => $this->username,
            'password'   => $this->password,
            'grant_type' => 'password'
        );
        
        $url = add_query_arg( $query_args, $url );
        
        $request_args = array();
        
        $response = wp_remote_post( $url, $request_args );
        if (is_wp_error($response)) return $response;
        
        $body = wp_remote_retrieve_body( $response );
        $body = json_decode($body);

        if (!$body->refresh_token){
            return new WP_Error( 'refreshtoken', __( "Error while trying to get the Hatchet refresh token", "hatchet" ) );
        }
        
        return $body->refresh_token;
    }
    
   function get_playlist($hatchet_id){
        $url = 'http://api.hatchet.is/v1/playlists/' . $hatchet_id;
        
        $request_args = array();
        
        $response = wp_remote_get( $url, $request_args );
        if (is_wp_error($response)) return $response;
        
        $body = wp_remote_retrieve_body( $response );
        $body = json_decode($body);
        
        $playlists = $body->playlists;
        
        if (!isset($playlists[0])) return $response;
        
        $playlist = $playlists[0];
        
        return $playlist;
    }
    
    function add_playlist_entry($playlist_id,$track_id){
        
        $accesstoken = self::get_access_token();
        if (is_wp_error($accesstoken)) return $accesstoken;
        
        $playlist_entry = new Hatchet_Playlist_Entry($playlist_id,$track_id);
        
        $query_args = array(
            'accesstoken'                   => $accesstoken,
            'main.PlaylistEntryPostStruct'  => $playlist_entry
        );
        
        $url = 'http://api.hatchet.is/v1/playlists/' . $playlist_id . '/playlistEntries';
        $url = add_query_arg( $query_args, $url );
        
        
        $request_args = array();
        
        $response = wp_remote_post( $url, $request_args );
        
        if (is_wp_error($response)) return $response;
        
        $body = wp_remote_retrieve_body( $response );
        $body = json_decode($body);
        
        print_r("add_playlist_entry<br/>");
        print_r($body);
        die();
        
        //MUST RETURN WP_ERROR OR... ENTRY ID ?

    }

    
    
    function update_playlist($hatchet_id,$playlist){
        
        $accesstoken = self::get_access_token();
        if (is_wp_error($accesstoken)) return $accesstoken;
        
        $query_args = array(
            'accesstoken'   => $accesstoken,
            'playlist-id'   => $hatchet_id
        );
        
        $url = 'http://api.hatchet.is/v1/playlists/' . $hatchet_id;
        $url = add_query_arg( $query_args, $url );
        
        $request_args = array(
            'method'    => 'PUT'
        );
        
        $response = wp_remote_request( $url, $request_args );
        if (is_wp_error($response)) return $response;
        
        $body = wp_remote_retrieve_body( $response );
        $body = json_decode($body);
        
        print_r("update_playlist<br/>");
        print_r($body);
        die();
        //MUST RETURN TRUE OR WP ERROR
    }
    
    function add_playlist($playlist){

        $accesstoken = self::get_access_token();
        if (is_wp_error($accesstoken)) return $accesstoken;
        
        $query_args = array(
            'accesstoken'               => $accesstoken,
            'main.PlaylistPostStruct'   => json_encode($playlist)
        );
        
        $url = 'http://api.hatchet.is/v1/playlists/';
        $url = add_query_arg( $query_args, $url );
        
        
        $request_args = array();
        
        $response = wp_remote_post( $url, $request_args );
        
        if (is_wp_error($response)) return $response;
        
        $body = wp_remote_retrieve_body( $response );
        $body = json_decode($body);
        
        print_r("add_playlist<br/>");
        print_r($body);
        die();
        
        //MUST RETURN HATCHET ID OR WP ERROR
        
    }
    
    function delete_playlist($hatchet_id){
        $accesstoken = self::get_access_token();
        if (is_wp_error($accesstoken)) return $accesstoken;
        
        $query_args = array(
            'accesstoken'   => $accesstoken,
            'playlist-id'   => $hatchet_id
        );
        
        $url = 'http://api.hatchet.is/v1/playlists/' . $hatchet_id;
        $url = add_query_arg( $query_args, $url );
        
        $request_args = array(
            'method'    => 'DELETE'
        );
        
        $response = wp_remote_request( $url, $request_args );
        if (is_wp_error($response)) return $response;
        
        $body = wp_remote_retrieve_body( $response );
        $body = json_decode($body);
        
        print_r("delete_playlist<br/>");
        print_r($body);
        die();
        
        //MUST RETURN TRUE OR WP ERROR
        
    }
    
}

class Hatchet_Playlist_Entry{
    var $playlist;
    var $track;
    function __construct($playlist_id,$track_id){
        $this->playlist = $playlist_id;
        $this->track = $track_id;
    }
}

class Hatchet_Playlist{
    var $id;
    var $title;
    var $created;
    var $currentrevision;
    var $playlistEntries = array();
    var $user;
    var $isFull;
    
}

new Hatchet_API();
