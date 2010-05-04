<?php
/*
Plugin Name: Open Graph Protocol Tools
Plugin URI: http://lab.artlung.com/open-graph-protocol-tools/
Description: Tools for Open Graph Protocol
Version: 1.1
Author: Joe Crawford
Author URI: http://joecrawford.com
License: GPL2
*/
// http://opengraphprotocol.org/

/*  Copyright 2010  Joe Crawford  (email : joe@artlung.com)

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
	'fb:admins' => '',
	'fb:appid' => '',
);

define('OGPT_DEFAULT_TYPE', 'blog');
define('OGPT_ARTICLE_TYPE', 'article');
define('OGPT_SETTINGS_KEY_FB_APPID', 'opengraphprotocoltools-fb:appid');
define('OGPT_SETTINGS_KEY_FB_ADMINS', 'opengraphprotocoltools-fb:admins');

$opengraphprotocoltools_keys = array(
	OGPT_SETTINGS_KEY_FB_APPID => 'A Facebook Platform application ID that administers this site.',
	OGPT_SETTINGS_KEY_FB_ADMINS => 'A comma-separated list of Facebook user IDs that administers this site. You can find your user id by visiting <a href="http://apps.facebook.com/what-is-my-user-id/" target="_blank">http://apps.facebook.com/what-is-my-user-id/</a>',
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


function load_opengraphprotocoltools_settings() {
	global $ogpt_settings;
	$ogpt_settings['fb:appid']  = get_option(OGPT_SETTINGS_KEY_FB_APPID);
	$ogpt_settings['fb:admins'] = get_option(OGPT_SETTINGS_KEY_FB_ADMINS);
}

function opengraphprotocoltools_plugin_path() {
	return get_option('siteurl') .'/wp-content/plugins/' . basename(dirname(__FILE__));
}

function opengraphprotocoltools_image_url_default() {
	// default image associated is in the plugin directory named "default.png"
	return opengraphprotocoltools_plugin_path() . '/default.png';
}

function opengraphprotocoltools_image_url() {
	global $post;

	$args = array(
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'post_parent' => $post->ID,
	);

	if( $images = get_children( $args ) ) {
		foreach( $images as $image ) {
			return array_shift(wp_get_attachment_image_src( $image->ID, 'medium' ));
		}
	}
	// if no images, return the default
	return opengraphprotocoltools_image_url_default();
}

function opengraphprotocoltools_set_data() {
	global $wp_query;
	load_opengraphprotocoltools_settings();
	$data = array();
	if (is_home()) :
		$data['og:title'] = get_bloginfo('name');
		$data['og:type'] = OGPT_DEFAULT_TYPE;
		$data['og:image'] = opengraphprotocoltools_image_url(); 
		$data['og:url'] = get_bloginfo('url');
		$data['og:site_name'] = get_bloginfo('name');
		$data['og:description'] = get_bloginfo('description');
	elseif (is_single() || is_page()):
		$data['og:title'] = get_the_title();
		$data['og:type'] = is_single() ? OGPT_ARTICLE_TYPE : OGPT_DEFAULT_TYPE; 
		$data['og:image'] = opengraphprotocoltools_image_url();
		$data['og:url'] = get_permalink();
		$data['og:site_name'] = get_bloginfo('name');
	else:
		$data['og:title'] = get_bloginfo('name');
		$data['og:type'] = OGPT_DEFAULT_TYPE;
		$data['og:image'] = opengraphprotocoltools_image_url(); 
		$data['og:url'] = get_bloginfo('url');
		$data['og:site_name'] = get_bloginfo('name');
		$data['og:description'] = get_bloginfo('description');
	endif;
	
	global $ogpt_settings;
	
	foreach($ogpt_settings as $key => $value) {
		if ($value!='') {
			$data[$key] = $value;
		}
	}
	return $data;
}

function opengraphprotocoltools_add_head() {
	$data = opengraphprotocoltools_set_data();
	echo get_opengraphprotocoltools_headers($data);
}

function get_opengraphprotocoltools_headers($data) {
	if (!count($data)) {
		return;
	}
	$out = array();
	$out[] = "\n<!-- BEGIN: Open Graph Protocol Tools: http://opengraphprotocol.org/ for more info -->";
	foreach ($data as $property => $content) {
		if ($content != '') {
			$out[] = get_opengraphprotocoltools_tag($property, $content);
		} else {
			$out[] = "<!--{$property} value was blank-->";
		}
	}
	$out[] = "<!-- End: Open Graph Protocol Tools-->\n";
	return implode("\n", $out);
}

function get_opengraphprotocoltools_tag($property, $content) {
	return "<meta property=\"{$property}\" content=\"".htmlentities($content)."\" />";
}

function the_opengraphprotocoltools_like_code() {
	echo get_opengraphprotocoltools_like_code();
}

function get_opengraphprotocoltools_like_code() {
	$data = opengraphprotocoltools_set_data();
	$url = rawurlencode($data['og:url']);
	$out .= "<!--begin facebook like code--><div align=\"center\" style=\"text-align: center;padding: 10px;\" class=\"opengraphprotocoltools-div\"><iframe src=\"http://www.facebook.com/plugins/like.php?href={$url}&amp;layout=standard&amp;show_faces=true&amp;width=450&amp;action=like&amp;colorscheme=light\" scrolling=\"no\" frameborder=\"0\" allowTransparency=\"true\" style=\"border:none; overflow:hidden; width:450px; height:80px\"></iframe></div><!--end facebook like code-->";
	return $out;
}

add_action('wp_head', 'opengraphprotocoltools_add_head');
add_action('admin_menu', 'opengraphprotocoltools_plugin_menu');


?>
