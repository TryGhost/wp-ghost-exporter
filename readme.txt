=== Ghost ===
Contributors: JohnONolan, ErisDS, javorszky, nathanjeffery, pauladamdavis
Donate link: https://ghost.org/docs/concepts/contributing/#donations
Tags: ghost, export, migrate, blogging, publishing
Requires at least: 4.2.0
Tested up to: 6.7
Stable tag: 1.6.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Export all your WordPress data to Ghost in a couple of clicks!

== Description ==

### Ghost Migrator: The easy way to migrate data to Ghost

The official Ghost plugin allows you to export your WordPress data in a JSON format that can be imported quickly and easily by the [Ghost](https://ghost.org) publishing platform.

#### Features Overview

The Ghost Migrator plugin will export as much blog and publication data as it can into a clean set of exported files.

- Posts, pages, tags and authors are all automatically exported and recreated for Ghost
- Tags will be migrated, but not categories. If needed you can [convert your categories to tags](https://wordpress.org/plugins/wpcat2tag-importer/) before exporting.
- Ghost does not have built-in comments, but it does integrate with [many comment platforms](https://ghost.org/integrations/?tag=community) if you want to migrate your comments there.
- No custom fields, meta, shortcodes, post types, taxonomies or binary files will be migrated. Just regular **posts**, **pages**, **tags** and **images**
- Passwords are not migrated - after importing to Ghost, each user may perform a password reset to gain access to their Ghost account


#### Docs & Support
You can find [docs](https://ghost.org/docs/), [FAQ](https://ghost.org/faq/) and more detailed information about Ghost on [ghost.org](https://ghost.org). If you're unable to find the answer to your question in our FAQ or in any of the documentation, try searching the [Ghost support forum](https://forum.ghost.org) - if you still don't find the answer you need, post a new topic!

### Bug reports

Bug reports for the Ghost Migrator plugin are welcome over on our [GitHub repository](https://github.com/tryghost/wp-ghost-exporter/). Please note that GitHub is not a support forum, and that issues that aren’t properly qualified as bugs will be closed.

### Further Reading

For more information about Ghost and help getting started with the platform, check out:

* The [Ghost](https://ghost.org) official homepage
* The [Ghost Support & FAQ](https://ghost.org/docs/)
* The [Ghost Forum & Community](https://forum.ghost.org)
* Follow Ghost on [Twitter](https://twitter.com/ghost)

== Installation ==

1. Use the Add New Plugin in the WordPress admin area
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Access the exporter functionality under `Tools -> Export to Ghost`

== Frequently Asked Questions ==

= How do I use this? =

Install it, then go to Tools -> Export to Ghost.

== Screenshots ==

1. The plugin has a single settings screen to export all your data

== Changelog ==

= 1.6.0 =

* Include categories
* Set all users to Contributor role once imported to Ghost
* User info now has fallbacks

= 1.5.0 =

* Delete prior exports and change how new exports are generated - Credit to [Joshua Chan](https://patchstack.com/database/researcher/7d630755-51bd-4c8b-8d77-a700aea26d1d)
* Test & ensure compatibility with WorePress 6.5

= 1.4.0 =

* Remove HTML from post titles
* Use example email if none exists for a user
* Test & ensure compatibility with WorePress 6.4 

= 1.3.0 =

* Added support for feature image alt & captions (thanks [@unitof](https://github.com/TryGhost/wp-ghost-exporter/commit/9ed5c4b6551b4e89577fb030665f74e4d124a484))
* Encode post titles (thanks [@unitof](https://github.com/TryGhost/wp-ghost-exporter/commit/9ed5c4b6551b4e89577fb030665f74e4d124a484))
* Disabled ZIP download button if ZipArchive is not installed
* Test & ensure compatibility with WorePress 6.3

= 1.2.1 =

* Use slug as user name if no display_name exists

= 1.2.0 =

* Add #wordpress tag to all posts & pages
* Test & ensure compatibility with WorePress 6.1
* Test & ensure compatibility with PHP 8.1

= 1.1.1 =

* Fix date format for compatibility with PHP 7

= 1.1.0 =

* Test & ensure compatibility with WorePress 6.0
* Test & ensure compatibility with Ghost 5.0
* Include `meta_title` and `meta_description` values where available
* Use the `user_login` value for user slugs to match the value used in URLs
* Remove the `html` and `language` values for posts, as these are not used by Ghost when importing content
* Include media library size in diagnostics 
* Various bug fixes & code improvements

= 1.0.5 =

* Only export users with the capability to create posts - PaulAdamDavis
* Include user roles in JSON export - PaulAdamDavis
* Use ISO 8601 dates for better Ghost compatibility - PaulAdamDavis
* Update plugin icon & screenshot - PaulAdamDavis

= 1.0.4 =

* Limit plugin to admin users only. Props to PaulAdamDavis.

= 1.0.3 =

* Updated branding. Props to PaulAdamDavis.

= 1.0.2 =

* UI cleanup and fix for absolute image URLs in post content. Props to PaulAdamDavis.

= 1.0.1 =

* Added check/notice for ZipArchive.
* Added check/notice for minimum PHP version.
* Added diagnostics section.

= 1.0.0 =

* Updated export format to support Ghost 2.x & 3.x
* Updated slug to use user_nicename.
* Upload/image URLs are now exported as relative paths compatible with Ghost.
* The download archive now includes all images in a Ghost compatible directory structure.
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

# Copyright & License

Copyright (c) 2013-2025 Ghost Foundation - Released under the [GNU General Public License](LICENSE).
