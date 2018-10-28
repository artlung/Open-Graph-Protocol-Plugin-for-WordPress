=== Plugin Name ===  
Contributors: artlung,pathawks  
Donate link: http://joecrawford.com/plugin-donation  
Tags: metadata, opengraphprotocol, facebook  
Requires at least: 2.9  
Tested up to: 5.1
Stable tag: 1.8 
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html  

Adds Open Graph Protocol meta to the individual pages and posts of your WordPress Install. 

== Description ==

Adds [Open Graph Protocol](http://opengraphprotocol.org/) meta tags to individual content pages of your WordPress install. Works in coordination with "Like" buttons and "share" inside Facebook and other consumers of OGP metadata.

== Installation ==

1. Upload `open-graph-protocol-tools/` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. To add the "Like" module, use this code:

<pre>&lt;?php
/**
 * Include this code on your theme for single blog posts (for example, in your single.php file)
 * or on your blogs main page to include a Facebook &quot;Like&quot; iframe
 */
if (function_exists(&#x27;the_opengraphprotocoltools_like_code&#x27;)):
   the_opengraphprotocoltools_like_code();
else:
	echo &quot;&lt;!-- opengraphprotocoltools is not activated --&gt;&quot;;
endif;
?&gt;</pre>

It would be best if in your theme you also added an attribute in the `<html>` tag, like this: `<html prefix="og: http://ogp.me/ns#">`


== Frequently Asked Questions ==

= Is there a FAQ? =

I've gotten some bug reports via <a href="https://github.com/artlung/Open-Graph-Protocol-Plugin-for-WordPress">GitHub</a>, you can report bugs or make comments there.

= For more information about Open Graph Protocol =

Visit [Open Graph Protocol](http://opengraphprotocol.org/).

== Screenshots ==

None.

== Changelog ==

= 1 =
* First version.

= 1.2 =
* Changed instructions, fixes encoding errors for non-English content.

= 1.3 =
* Improve issue with home vs front page

= 1.6 =
* Improvements by pathawks to accommodate more types
* Address double encoding issue with wptexturize modified values

= 1.7 =

* Misc improvements I never enumerated and can't remember. Whoops.

= 1.8 =

* WordPress 5 compatibility check!
