=== Plugin Name ===
Contributors: artlung
Donate link: 
Tags: metadata, opengraphprotocol
Requires at least: 2.9
Tested up to: 2.9.2
Stable tag: 1.0

This is a plugin to add Open Graph Protocol Data to your WordPress Install, plus adds the capability to add Facebook "Like" module.

== Description ==

This is a tool to add [Open Graph Protocol](http://opengraphprotocol.org/) Data to your WordPress Install.

This is very new at this point.

The following data is a description of the metadata that at this point is just a placeholder:

*   "Contributors" is a comma separated list of wp.org/wp-plugins.org usernames
*   "Tags" is a comma separated list of tags that apply to the plugin
*   "Requires at least" is the lowest version that the plugin will work on
*   "Tested up to" is the highest version that you've *successfully used to test the plugin*. Note that it might work on
higher versions... this is just the highest one you've verified.
*   Stable tag should indicate the Subversion "tag" of the latest stable version, or "trunk," if you use `/trunk/` for
stable.

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

It would be best if in your theme you also added an attribute in the &lt;html&gt; tag, like this: &lt;html prefix="og: http://ogp.me/ns#"&gt;


== Frequently Asked Questions ==

= Is there a FAQ? =

Not yet.

= What about foo bar? =

No, not that either.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot

== Changelog ==

= 1 =
* First version.