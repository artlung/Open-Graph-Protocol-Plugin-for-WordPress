<?php
/*
Plugin Name: Open Graph Protocol Tools
Plugin URI: http://lab.artlung.com/open-graph-protocol-tools/
Description: Tools for Open Graph Protocol
Version: 1
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

define('OGPT_DEFAULT_TYPE', 'blog');

function opengraphprotocoltools_plugin_path() {
	return get_option('siteurl') .'/wp-content/plugins/' . basename(dirname(__FILE__));
}

function opengraphprotocoltools_image_url_default() {
	return opengraphprotocoltools_plugin_path() . '/default.png';
}

function opengraphprotocoltools_image_url() {
	return opengraphprotocoltools_image_url_default();
}


function opengraphprotocoltools_add_head() {
		// REQUIRED
		// title ~ title of web page
		// type ~ blog
		// image ~ ?
		// url ~ url of post
		global $wp_query;
		$data = array();
		if (is_home()) :
			$data['title'] = get_bloginfo('name');
			$data['type'] = OGPT_DEFAULT_TYPE;
			$data['image'] = opengraphprotocoltools_image_url(); 
			$data['url'] = get_bloginfo('url');
			$data['site_name'] = get_bloginfo('name');
			$data['description'] = '';
		elseif (is_single() || is_page()):
			$data['title'] = get_the_title();
			$data['type'] = OGPT_DEFAULT_TYPE;
			$data['image'] = opengraphprotocoltools_image_url();
			$data['url'] = get_permalink();
			$data['site_name'] = get_bloginfo('name');
			$data['description'] = '';
		endif;
		echo get_opengraphprotocoltools_headers($data);
}

function get_opengraphprotocoltools_headers($data) {
	if (!count($data)) {
		return;
	}
	$out = array();
	$out[] = "\n<!-- BEGIN: Open Graph Protocol Tools: http://opengraphprotocol.org/ for more info -->";
	foreach ($data as $property => $content) {
		$out[] = get_opengraphprotocoltools_tag($property, $content);
	}
	$out[] = "<!-- End: Open Graph Protocol Tools-->\n";
	return implode("\n", $out);
}

function get_opengraphprotocoltools_tag($property, $content) {
	return "<meta property=\"og:{$property}\" content=\"".htmlentities($content)."\" />";
}


add_action('wp_head', 'opengraphprotocoltools_add_head');

?>