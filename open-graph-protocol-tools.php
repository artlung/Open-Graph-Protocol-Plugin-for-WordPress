<?php
/*
Plugin Name: Open Graph Protocol Tools
Plugin URI: http://lab.artlung.com/open-graph-protocol-tools/
Description: Tools for Open Graph Protocol
Version: 1.5
Author: Joe Crawford
Author URI: http://joecrawford.com
License: GPL2
*/
// http://opengraphprotocol.org/

/*  Copyright 2010-12  Joe Crawford  (email : joe@artlung.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
// opengraphprotocol.default.png

/**
 * These are initially blank, to have a like button, at least one of these must be set
 */
$ogpt_settings = array(
	'http://ogp.me/ns/fb#admins' => '',
	'http://ogp.me/ns/fb#app_id' => '',
);

define('OGPT_DEFAULT_TYPE', 'blog');
define('OGPT_ARTICLE_TYPE', 'article');
define('OGPT_SETTINGS_KEY_FB_APP_ID', 'opengraphprotocoltools-fb:app_id');
define('OGPT_SETTINGS_KEY_FB_ADMINS', 'opengraphprotocoltools-fb:admins');
define('OGPT_SETTINGS_KEY_TWITTER_SITE', 'opengraphprotocoltools-twitter:site');

$opengraphprotocoltools_keys = array(
	OGPT_SETTINGS_KEY_FB_APP_ID => 'A Facebook Platform application (fb:app_id, formerly fb:appid) ID that administers this site.',
	OGPT_SETTINGS_KEY_FB_ADMINS => 'A comma-separated list of Facebook user IDs that administers this site. You can find your user id by visiting <a href="http://apps.facebook.com/what-is-my-user-id/" target="_blank">http://apps.facebook.com/what-is-my-user-id/</a>',
	OGPT_SETTINGS_KEY_TWITTER_SITE => 'The Twitter @username of the entire site, if there is one.',
);


function opengraphprotocoltools_plugin_menu() {
	add_submenu_page('options-general.php', 'Open Graph Protocol', 'Open Graph Protocol', 'manage_options', 'opengraphprotocoltools', 'opengraphprotocoltools_plugin_options');

}

function opengraphprotocoltools_plugin_options() {

	global $opengraphprotocoltools_keys;
	echo '<div class="wrap">';
	echo '<form method="post" action="options.php">';
	echo '<input type="hidden" name="action" value="update" />';
	echo '<input type="hidden" name="page_options" value="'.implode(',',array_keys($opengraphprotocoltools_keys)).'" />';
	echo wp_nonce_field('update-options');
	echo '<p>To include the Facebook "like" code on your page, you must first include values for one of the below. Your Facebook User ID is a number. You may specify multiple user IDs if you like.</p>';
	echo '<table class="form-table">';
	foreach ($opengraphprotocoltools_keys as $key => $desc) {
		echo '<tr valign="top">';
		echo '<th scope="row">';
		echo array_pop(explode('-',$key));
		echo '</th>';
		echo '<td><input type="text" name="';
		echo $key;
		echo '" value="';
		echo get_option($key);
		echo '" size="30"/><br />';
		echo $desc; 
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
	echo '<p class="submit">';
	echo '<input type="submit" class="button-primary" value="Save Changes" />';
	echo '</p>';
	echo '</form>';
	echo '<textarea rows="11" cols="80">';
	echo htmlentities(file_get_contents(dirname(__FILE__) . '/sample_code.txt'));
	echo '</textarea>';
	echo '</div>';
}

function opengraphprotocoltools_user_contactmethods($user_contactmethods){
  $user_contactmethods['twitter'] = 'Twitter Username';
  return $user_contactmethods;
}

function get_opengraphprotocoltools_author_twitter(){
	global $post;
	return '@'.trim(get_the_author_meta('twitter',$post->post_author),'@ ');
}

function load_opengraphprotocoltools_settings() {
	global $ogpt_settings;
	$ogpt_settings['http://ogp.me/ns/fb#app_id'] = get_option(OGPT_SETTINGS_KEY_FB_APP_ID);
	$ogpt_settings['http://ogp.me/ns/fb#admins'] = get_option(OGPT_SETTINGS_KEY_FB_ADMINS);
	$ogpt_settings['http://ogp.me/ns#site_name'] = get_bloginfo('name');
	$ogpt_settings['http://ogp.me/ns#locale']    = get_locale();
	$ogpt_settings['http://ogp.me/ns#type']      = OGPT_DEFAULT_TYPE;
	$ogpt_settings['twitter:card']               = 'summary';
	$ogpt_settings['twitter:site']               = '@'.trim(get_option(OGPT_SETTINGS_KEY_TWITTER_SITE),'@ ');
}

function opengraphprotocoltools_image_url_default() {
	// default image associated is in the plugin directory named "default.png"
	return plugins_url( 'default.png' , __FILE__ );
}

function opengraphprotocoltools_image() {
	global $post;
	$meta_tags = array();

	$args = array(
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'post_parent' => $post->ID,
	);

	if( $images = get_children( $args ) ) {
		foreach( $images as $image ) {
			$opengraphprotocoltools_image = wp_get_attachment_image_src( $image->ID, 'medium' );
			$image_tags = array();
			$image_tags['url']    = $opengraphprotocoltools_image[0];
			$image_tags['width']  = $opengraphprotocoltools_image[1];
			$image_tags['height'] = $opengraphprotocoltools_image[2];
			$meta_tags['http://ogp.me/ns#image'] = array($image_tags);
			$meta_tags['twitter:card'] = 'photo';
			return $meta_tags;
		}
	}
	// if no images, return the default
	$meta_tags['http://ogp.me/ns#image'] = opengraphprotocoltools_image_url_default();
	return $meta_tags;
}

function opengraphprotocoltools_audio() {
	global $post;
	$meta_tags = array();

	$args = array(
		'post_type' => 'attachment',
		'post_mime_type' => 'audio/mpeg',
		'post_parent' => $post->ID,
	);

	if( $audios = get_children( $args ) ) {
		foreach( $audios as $audio ) {
			$opengraphprotocoltools_audio_url = wp_get_attachment_url( $audio->ID );
			$meta_tags['http://ogp.me/ns#audio'] = $opengraphprotocoltools_audio_url;
			$meta_tags['twitter:player:stream'] = array( 'url' => $opengraphprotocoltools_audio_url);
			$meta_tags['twitter:player:stream']['content_type'] = $audio->post_mime_type;

//			$meta_tags['twitter:card'] = 'player';
//			We haven't yet provided enough data for Twitter to display a player.
//			We need to also provide an iframe player, which must work over HTTPS
//			We also need to provide a fallback image that is the same size as the iframe player.

			return $meta_tags;
		}
	}
	return $meta_tags;
}

function opengraphprotocoltools_video() {
	global $post;
	$meta_tags = array();
	$meta_tags = opengraphprotocoltools_embed_youtube($post->ID);
	return $meta_tags;
}

function opengraphprotocoltools_embed_youtube($post_id) {
	$post_array = get_post($post_id);
	$markup = $post_array->post_content;
	$markup = apply_filters('the_content',$markup);
	$meta_tags = array();

	// Checks for a standard YouTube embed
	preg_match('#<object[^>]+>.+?http://www.youtube.com/[ve]/([A-Za-z0-9\-_]+).+?</object>#s', $markup, $matches);
	
	// More comprehensive search for YouTube embed, redundant but necessary until more testing is completed
	if(!isset($matches[1])) {
		preg_match('#http://www.youtube.com/[ve]/([A-Za-z0-9\-_]+)#s', $markup, $matches);
	}
	
	// Checks for YouTube iframe
	if(!isset($matches[1])) {
		preg_match('#http://www.youtube.com/embed/([A-Za-z0-9\-_]+)#s', $markup, $matches);
	}
	
	if ($matches[1]) {
		$meta_tags['http://ogp.me/ns#image'] = array();
		$meta_tags['http://ogp.me/ns#video'] = array();
		$meta_tags['twitter:player']         = array();
		$meta_tags['http://ogp.me/ns#image']['url']        = 'http://img.youtube.com/vi/' . $matches[1] . '/0.jpg';
		$meta_tags['http://ogp.me/ns#image']['width']      = '480';
		$meta_tags['http://ogp.me/ns#image']['height']     = '360';
		$meta_tags['http://ogp.me/ns#video']['url']        = 'http://www.youtube.com/embed/'.$matches[1];
		$meta_tags['http://ogp.me/ns#video']['secure_url'] = 'https://www.youtube.com/embed/'.$matches[1];
		$meta_tags['http://ogp.me/ns#video']['type']       = 'text/html';
		$meta_tags['twitter:card']                         = 'player';
		$meta_tags['twitter:player']['url']                = 'https://www.youtube.com/embed/'.$matches[1];
		$meta_tags['twitter:player']['width']              = '480';
		$meta_tags['twitter:player']['height']             = '360';
	}
	
	return $meta_tags;
}

function opengraphprotocoltools_set_data() {
	global $post;
	global $wp_query;
	global $ogpt_settings;
	load_opengraphprotocoltools_settings();
	$meta_tags = $ogpt_settings;
	if (is_front_page() || is_home()) :
		$meta_tags['http://ogp.me/ns#title'] = get_bloginfo('name');
		$meta_tags['http://ogp.me/ns#url'] = get_bloginfo('url');
		$meta_tags['http://ogp.me/ns#description'] = get_bloginfo('description');
	elseif ( is_author() && isset( $post->post_author ) ):
		$meta_tags['http://ogp.me/ns#type'] = 'profile';
		if ( is_multi_author() )
			$meta_tags['http://ogp.me/ns/profile#username'] = get_the_author_meta( 'login', $post->post_author );
		$meta_tags['http://ogp.me/ns/profile#first_name'] = get_the_author_meta( 'first_name', $post->post_author );
		$meta_tags['http://ogp.me/ns/profile#last_name'] = get_the_author_meta( 'last_name', $post->post_author );
	elseif (is_single() || is_page()):
		$post_type = get_post_type();
		if ( post_type_supports( $post_type, 'title' ) )
			$meta_tags['http://ogp.me/ns#title'] = get_the_title();
		$meta_tags['http://ogp.me/ns#type'] = is_single() ? OGPT_ARTICLE_TYPE : OGPT_DEFAULT_TYPE;
		$meta_tags['http://ogp.me/ns#url'] = apply_filters( 'rel_canonical', get_permalink() );
		$meta_tags['http://ogp.me/ns/article#published_time'] = get_the_date('c');
		$meta_tags['http://ogp.me/ns/article#modified_time'] = get_the_modified_date('c');
		if ( post_type_supports( $post_type, 'author' ) && isset( $post->post_author ) )
			$meta_tags['http://ogp.me/ns/article#author'] = get_author_posts_url( $post->post_author );
		$meta_tags['twitter:creator'] = get_opengraphprotocoltools_author_twitter();
		
		$meta_tags = array_merge($meta_tags,opengraphprotocoltools_image());
		$meta_tags = array_merge($meta_tags,opengraphprotocoltools_audio());
		$meta_tags = array_merge($meta_tags,opengraphprotocoltools_video());
		
	else:
		$meta_tags['http://ogp.me/ns#title'] = get_bloginfo('name');
		$meta_tags['http://ogp.me/ns#url'] = get_bloginfo('url');
		$meta_tags['http://ogp.me/ns#description'] = get_bloginfo('description');
		$meta_tags['twitter:creator'] = get_opengraphprotocoltools_author_twitter();
	endif;

	ksort($meta_tags); // For easier debugging

	return $meta_tags;
}

function opengraphprotocoltools_add_head() {
	global $post;
	$meta_tags = opengraphprotocoltools_set_data();
	$meta_tags = apply_filters( 'fb_meta_tags', $meta_tags, $post );
	echo get_opengraphprotocoltools_headers($meta_tags);
}

function get_opengraphprotocoltools_headers($meta_tags) {
	if (!count($meta_tags)) {
		return;
	}
	$out = array();
	$out[] = "\n<!-- BEGIN: Open Graph Protocol Tools: http://opengraphprotocol.org/ for more info -->";

	if ( $meta_tags['twitter:site'] = '@' )
		$meta_tags['twitter:site'] = '';
	if ( $meta_tags['twitter:creator'] = '@' )
		$meta_tags['twitter:creator'] = '';

	foreach ($meta_tags as $property => $content) {
		$out[] = get_opengraphprotocoltools_tag($property, $content);
	}
	$out[] = "<!-- End: Open Graph Protocol Tools-->\n";
	foreach ($out as $key => $value) { 
	  if (empty($value)) { 
	    unset($out[$key]); 
	  } 
	}
	return implode("\n", $out);
}

function get_opengraphprotocoltools_tag($property, $content) {
	if ( empty( $property ) || empty( $content ) )
		return;

	// array of property values or structured property
	if ( is_array( $content ) ) {
		$meta_tags = array();
		foreach( $content as $structured_property => $content_value ) {
			// handle numeric keys from regular arrays
			// account for the special structured property of url which is equivalent to the root tag and sets up the structure
			if ( ! is_string( $structured_property ) || $structured_property === 'url' )
				$meta_tags[] = get_opengraphprotocoltools_tag( $property, $content_value );
			else
				$meta_tags[] = get_opengraphprotocoltools_tag( $property . ':' . $structured_property, $content_value );
		}
		return implode("\n",$meta_tags);
	}
	else {
		if ( strstr( $property, 'twitter:' ) )
			return "<meta name=\"{$property}\" content=\"".htmlentities($content, ENT_QUOTES, 'UTF-8')."\" />";
		return "<meta property=\"{$property}\" content=\"".htmlentities($content, ENT_QUOTES, 'UTF-8')."\" />";
	}
}

function the_opengraphprotocoltools_like_code() {
	echo get_opengraphprotocoltools_like_code();
}

function get_opengraphprotocoltools_like_code() {
	$meta_tags = opengraphprotocoltools_set_data();
	$url = rawurlencode($meta_tags['http://ogp.me/ns#url']);
	$out .= "<!--begin facebook like code--><div align=\"center\" style=\"text-align: center;padding: 10px;\" class=\"opengraphprotocoltools-div\"><iframe src=\"http://www.facebook.com/plugins/like.php?href={$url}&amp;layout=standard&amp;show_faces=true&amp;width=450&amp;action=like&amp;colorscheme=light\" scrolling=\"no\" frameborder=\"0\" allowTransparency=\"true\" style=\"border:none; overflow:hidden; width:450px; height:80px\"></iframe></div><!--end facebook like code-->";
	return $out;
}

add_filter('user_contactmethods', 'opengraphprotocoltools_user_contactmethods');
add_action('wp_head', 'opengraphprotocoltools_add_head');
add_action('admin_menu', 'opengraphprotocoltools_plugin_menu');