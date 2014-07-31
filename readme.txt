=== Hatchet.is ===
Contributors: grosbouff
Tags: Hatchet, Tomahawk, Hatchet API, shortcode, hatchet.is, music, Spotify, Grooveshark, Soundcloud, Ex.fm, Subsonic
Requires at least: 3.5
Tested up to: 4
Stable tag: trunk

Enables shortcodes to embed playlists, albums and single tracks from Hatchet.is, and other functions to communicate with the Hatchet.is API.

== Description ==

Enables shortcodes to embed playlists, albums and single tracks from Hatchet.is, and other functions to communicate with the Hatchet.is API.

= Embed a playlist =

Use this code : [hatchet-playlist]HATCHET_PLAYLIST_URL[/hatchet-playlist] where HATCHET_PLAYLIST_URL is the url of your playlist (eg.: https://hatchet.is/people/muesli/playlists/5095d5f87fb4401672fa194c_53d895852484f201a20117d0).
You can also specify several arguments :

* width (the embed width) - default is 550
* height (the embed height) - default is 430

Like this : [hatchet-playlist width="550" height="430"]HATCHET_PLAYLIST_URL[/hatchet-playlist]

= Embed an album =

Use this code : 
[hatchet-album artist="ALBUM ARTIST" album="ALBUM TITLE"][/hatchet-album]

Or use:
[hatchet-album]HATCHET_ALBUM_URL[/hatchet-album]where HATCHET_ALBUM_URL is the url of your album (eg.: https://hatchet.is/music/Queen/Greatest+Hits).

You can also specify several arguments :

 
* width (the embed width) - default is 550
* height (the embed height) - default is 430
* Like this : [hatchet-album width="550" height="430"]HATCHET_ALBUM_URL[/hatchet-album] 

= Embed a single track =


Use this code : 

[hatchet-track artist="TRACK ARTIST" track="TRACK TITLE"][/hatchet-track]

Or use:

[hatchet-track]HATCHET_TRACK_URL[/hatchet-track]where HATCHET_TRACK_URL is the url of your track (eg.: https://hatchet.is/music/Queen/_/Don't+Try+So+Hard).

You can also specify several arguments :

* width (the embed width) - default is 200
* height (the embed height) - default is 200
* autoplay (true|false) - default is true
* disabled resolvers - default is false

Like this : [hatchet-album width="200" height="200" autoplay=true disabled_resolvers="Soundcloud,Officialfm"]HATCHET_ALBUM_URL[/hatchet-album]

= Contributors =
[Contributors are listed
here](https://github.com/gordielachance/wp-hatchet/contributors)
= Notes =

For feature request and bug reports, [please use the
forums](http://wordpress.org/support/plugin/wp-hatchet#postform).

If you are a plugin developer, [we would like to hear from
you](https://github.com/gordielachance/wp-hatchet). Any contribution would be
very welcome.
 
== Installation ==

Upload the plugin to your blog and Activate it.

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

= 0.1 =
* First release

== Upgrade Notice ==

== Localization ==