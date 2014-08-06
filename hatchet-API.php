<?php

/**
 * Class for the Hatchet API
 * https://api.hatchet.is/apidocs/#!/playlists/findPlaylist
 */

class Hatchet_API {
    
    var $username;
    var $password;
    var $access_token;
    
    function __construct() {
        $this->username = hatchet_get_option('api_username');
        $this->password = hatchet_get_option('api_password');

    }

    /*
     * Get the token that allows us to use the API
     */
    
    function get_access_token(){
        
        if (!$this->access_token){
            
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
            if ($api_error = self::get_response_error($response)) return $api_error;

            $body = wp_remote_retrieve_body( $response );
            $body = json_decode($body);
            
            $this->access_token = $body->access_token;
            
        }
        
        return $this->access_token;
        

        
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
        if ($api_error = self::get_response_error($response)) return $api_error;
        
        $body = wp_remote_retrieve_body( $response );
        $body = json_decode($body);
        
        return $body->refresh_token;
    }
    
    /*
     * Get a single playlist
     */
    
   function get_playlist($playlist_id){
        $url = 'http://api.hatchet.is/v1/playlists/' . $playlist_id;
        
        $request_args = array();
        
        $response = wp_remote_get( $url, $request_args );
        if ($api_error = self::get_response_error($response)) return $api_error;
        
        $body = wp_remote_retrieve_body( $response );
        $body = json_decode($body);
        
        $playlists = $body->playlists;
        
        if (!isset($playlists[0])) return $response;
        
        $playlist = $playlists[0];
        
        return $playlist;
    }
    
    /*
     * Get entries for a playlist
     */
    
    function get_playlist_entries($playlist_id){
        $url = 'http://api.hatchet.is/v1/playlists/' . $playlist_id . '/entries';
        
        $request_args = array();
        
        $response = wp_remote_get( $url, $request_args );
        if ($api_error = self::get_response_error($response)) return $api_error;
        
        $body = wp_remote_retrieve_body( $response );
        $body = json_decode($body);

        return $body->playlistEntries;
    }
    
    /*
     * Adds an entry in a playlist,
     * Giving a track ID
     * Or track strings : artist + track + album (album is optional)
     */
    
    function add_playlist_entry($playlist_id, $track_id = null, $artist = null, $track = null, $album = null){
        
        $accesstoken = self::get_access_token();
        if (is_wp_error($accesstoken)) return $accesstoken;
        
        $playlist_entry = new Hatchet_Playlist_Entry($playlist_id, $track_id, $artist, $track, $album);
        
        print_r($playlist_entry);
        die("add_playlist_entry");
        
        $query_args = array(
            'accesstoken'                   => $accesstoken,
            'main.PlaylistEntryPostStruct'  => $playlist_entry
        );
        
        $url = 'http://api.hatchet.is/v1/playlists/' . $playlist_id . '/playlistEntries';
        $url = add_query_arg( $query_args, $url );
        
        
        $request_args = array();
        
        $response = wp_remote_post( $url, $request_args );
        if ($api_error = self::get_response_error($response)) return $api_error;
        
        $body = wp_remote_retrieve_body( $response );
        $body = json_decode($body);
        

        
        print_r("add_playlist_entry<br/>");
        print_r($body);
        die();
        
        //MUST RETURN WP_ERROR OR... ENTRY ID ?

    }
    
    /*
     * Delete a single entry in a playlist
     */
    
    function delete_playlist_entry($playlist_id,$entry_id){
        $accesstoken = self::get_access_token();
        if (is_wp_error($accesstoken)) return $accesstoken;

        $query_args = array(
            'accesstoken'   => $accesstoken,
            'playlist-id'   => $playlist_id,
            'entry-id'   => $entry_id,
        );
        
        $url = 'http://api.hatchet.is/v1/playlists/' . $playlist_id . '/playlistEntries/' . $entry_id;
        $url = add_query_arg( $query_args, $url );

        $request_args = array(
            'method'    => 'DELETE'
        );
        
        $response = wp_remote_request( $url, $request_args );
        if ($api_error = self::get_response_error($response)) return $api_error;
        
        //$body = wp_remote_retrieve_body( $response );
        //$body = json_decode($body);

        return true;
    }
    
    /*
     * Remove all entries from a playlist (if, for example, whe need to update all the tracks)
     */
    
    function delete_all_playlist_entries($playlist_id){
        
        $accesstoken = self::get_access_token();
        if (is_wp_error($accesstoken)) return $accesstoken;
        
        $entries = self::get_playlist_entries($playlist_id);
        if (is_wp_error($entries)) return $entries;
        
        $errors = 0;
        
        foreach ((array)$entries as $entry){
            if ( is_wp_error( self::delete_playlist_entry($playlist_id,$entry->id) ) ){
                $errors++;
            }
        }
        
        if ($errors){
            return new WP_Error( 'delete_all_playlist_entries', 
                    sprintf( _n( '1 track has not been deleted', '%s tracks have not been deleted', $errors, 'hatchet' ), $errors )
            );
        }
        
        return true;
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
        if ($api_error = self::get_response_error($response)) return $api_error;
        
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
        if ($api_error = self::get_response_error($response)) return $api_error;
        
        $body = wp_remote_retrieve_body( $response );
        $body = json_decode($body);
        

        
        print_r("add_playlist<br/>");
        print_r($body);
        die();
        
        //MUST RETURN HATCHET ID OR WP ERROR
        
    }
    
    function delete_playlist($playlist_id){
        $accesstoken = self::get_access_token();
        if (is_wp_error($accesstoken)) return $accesstoken;
        
        $query_args = array(
            'accesstoken'   => $accesstoken,
            'playlist-id'   => $playlist_id
        );
        
        $url = 'http://api.hatchet.is/v1/playlists/' . $playlist_id;
        $url = add_query_arg( $query_args, $url );
        
        $request_args = array(
            'method'    => 'DELETE'
        );
        
        $response = wp_remote_request( $url, $request_args );
        if ($api_error = self::get_response_error($response)) return $api_error;
        
        $body = wp_remote_retrieve_body( $response );
        $body = json_decode($body);
        

        
        print_r("delete_playlist<br/>");
        print_r($body);
        die();
        
        //MUST RETURN TRUE OR WP ERROR
        
    }
    
    /*
     * Converts the error response from the API
     * in a WP error, with the code 'api_error'.
     */
    
    function get_response_error($response){
        
        //WP error while trying to get the response
        if (is_wp_error($response)) return $response;
        
        //convert API error in WP error
        
        //TO FIX Hatchet.is should unify its errors. 
        //See with @muesli : 
        //currently it returns different types of objects.
        
        $body = wp_remote_retrieve_body( $response );
        $body = json_decode($body);

        if ( ( is_object($body) ) && (property_exists($body,'error')) ){

            if (property_exists($body,'error_description')){
                
                return new WP_Error( 'api_error', $body->error_description );
                
            }else{
                
                return new WP_Error( 'api_error', $body->error->description );
                
            }
            
            
        }
        
        return false;
    }
    
}

class Hatchet_Playlist_Entry{
    var $albumString;
    var $artistString;
    var $playlist;
    var $track;
    var $trackString;
    
    function __construct($playlist_id, $track_id = null, $artist = null, $track = null, $album = null){
        
        $this->playlist = $playlist_id;

        if (isset($track_id)){ // track_id has priority over track strings
            
            $this->track = $track_id;
            
        } elseif ( isset($artist) && isset($track) ) {
            $this->artistString = $artist;
            $this->trackString = $track;
            if (isset($album)) $this->albumString = $album;
        }
            
        
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
