=== DX Sources ===
Contributors: xavortm, devrix
Tags: post, dashboard, content, admin
Requires at least: 3.0.1
Tested up to: 4.7
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add the sources for your articles in the end of a post and link them through #dxs-<number> ID for fast reader's navigation.

== Description ==

DX Sources is a plugin giving you easy way of adding sources to your posts. Be it news or technical article sometimes specifying sources is required and DX Sources provides with a tool just after your post editor where you can add dynamically new items without refreshing the page.

The list of sources will be displayed under your article's content with numerical representation before the name like [0], [1] e.t.c. Those numbers can be used for linking any text in your article to the source requried by adding link with url `#dxs-<the source number>`. For example the 4th source would be linked with `#dxs-4` and so on.

### For theme developers:

The plugin doesn't include any stylings or scripts to the front-end (theme), so no extra requests. All the stylings are up to you. The markup uses `<ul>`, `strong` and `em`, so by default it should be styled properly. Still if you need anything more every single element has clean class names you can use. You can also use the unique incremented IDs for specific target if required.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/dx-sources` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Access the plugin through the single post view.

== Screenshots ==

1. Example view for the plugin with basic default WordPress theme installed. This is what you get from simply activating and using the plugin. Any extra styling is up to you.
2. Adding and deleting items happens without refresing the dashboard. Only the "Name" field is required and if not filled you would recieve notification that it should be filled.

== Changelog ==

= 1.1.0 - 12/08/2016 =
* Added external link icon plus opening external links in a new tab
* Bump compatibility with WordPress to 4.7 from 4.5

= 1.0.0 - 4/14/2016 =
* Initial version of DX Sources
