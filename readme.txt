=== Ghost ===
Contributors: JohnONolan, ErisDS, javorszky
Donate link: http://ghost.org/
Tags: export, ghost
Requires at least: 3.5
Tested up to: 3.8
Stable tag: 0.3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Export your WordPress data to Ghost.

== Description ==

The Ghost WordPress plugin allows you to export your WordPress data in a format that can be imported quickly and easily by [Ghost](http://tryghost.org).

== Installation ==

1. Use the Add New Plugin in the WordPress admin area
1. Activate the plugin through the 'Plugins' menu in WordPress
1. You can find the settings and documentation under Tools -> Export to Ghost

== Frequently Asked Questions ==

= How do I use this? =

Install it, then go to Tools -> Export to Ghost.

== Changelog ==

= 0.3.0 =
* Fixes a bug where future posts would crash the plugin, and you weren't able to export
* Future posts are exported as drafts. Their published_at retains the future publish datetime though
* Updated version to 0.3.0 for semver reasons
* Left out Bond version :(

= 0.0.6 =
* Fixes a duplicate tag issue.

= 0.0.5 =
* Removed the export of Categories so that duplicate slugs don't happen if there is a tag and a category with the same name.

= 0.0.4 =
* There was a schema change in 0.3.2 or somewhere near that. Basically the way tags work changed, thus the exporter had to be reworked.

= 0.0.3 =
* Fixed a typo I left in the previous version causing everything to not work... Sorry...

= 0.0.2 =
* Fixed a path issue that would sometimes result in empty .json files. Hat tip to Ian Wootten for the fix!

= 0.0.1 =
* Initial implementation
