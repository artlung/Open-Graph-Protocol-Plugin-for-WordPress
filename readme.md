=== Plugin Name ===
Contributors: artlung,pathawks
Donate link: 
Tags: metadata, opengraphprotocol, facebook
Requires at least: 2.9
Tested up to: 3.3.1
Stable tag: 1.3

This is a plugin to add Open Graph Protocol Data to your WordPress Install, plus adds the capability to add Facebook "Like" module.

== Description ==

This is a tool to add [Open Graph Protocol](http://opengraphprotocol.org/) Data to the individual content pages of your WordPress Install.


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

I've gotten some bug reports via <a href="https://github.com/artlung/Open-Graph-Protocol-Plugin-for-WordPress">GitHub</a>, you can report bugs or make comments there.



== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot

== Changelog ==

= 1 =
* First version.

= 1.2 =
* Changed instructions, fixes encoding errors for non-English content.

= 1.3 =
* Improve issue with home vs front page