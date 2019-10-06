=== Ghost ===
Contributors: JohnONolan, ErisDS, javorszky, nathanjeffery
Donate link: http://ghost.org/
Tags: export, ghost
Requires at least: 3.5
Tested up to: 5.2.3
Stable tag: 1.0.0
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

= 1.0.0 =

* Updated export format to support Ghost 2.x & 3.x
* Updated slug to use user_nicename.
* Added user profile image, updated name to use display_name, and updated the post feature image field. Props to badrihippo.

= 0.5.6 =

* Added permission check for downloading the exported data. Only users allowed to do that can download it. Props tomhallam.

= 0.5.5 =
* Added 'the_content' filter around $post->content. Helps with markdown conversion and display issues

= 0.5.4 =
* new line tweaks: h1 and h2 will be prepended by new lines now. Previously they weren't, and that caused problems with opening a new tag for the headers.
* set h1 and h2 to use atx method (# and ## instead of underlining with === and ---)
* tweaked code block: multiple lines of code blocks are exported wrapped with ticks instead of being indented by one tab character. Code spans not changed.
* shout out to sorbolene on #ghost for taking the time to nag me about this :) Thanks!

= 0.5.3 =
* implemented HTML to Markdown (thanks @yauh!). Ref: https://github.com/nickcernis/html2markdown/
* code lint: plugin now adheres to WordPress-Extra coding style
* added featured image to the export

= 0.5.2 =
* Restricting fields to fit within Ghost's db schema (concatenating some of the fields):
* title and slug are both capped at 150 characters
* status is capped at 150 characters (although you'd only every use about 15)
* language is capped at 6 (although most are 5, like "en_GB" or 2)

= 0.5.1 =
* Housekeeping in the repository.

= 0.5.0 =
* Added support for multi-user capabilities, it now exports users, and connects posts with the users
* Now exports pages
* Exports user meta
* Moved the json file save directory to the uploads folder. Added check whether the folder is writable or not.

= 0.4.3 =
* Added support for `private` and `pending` WordPress post statuses (which were unhandled, and thus broke import). Ref: [WP Query: Status Parameters](http://codex.wordpress.org/Class_Reference/WP_Query#Status_Parameters)

= 0.4.2 =
* Removed published_at date from draft posts

= 0.4.1 =
* Bumped version to current stable Ghost version
* Added Export link to plugin row display for easier access
* Changed date format in exported JSON. Previously it was microtime, which caused troubles on 32 bit PHP installs, as the maximum integer was less than current time, so exports were either in scientific notation or string. Currently using RFC2822 format (date("r") for the devs), which works with the importer

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
