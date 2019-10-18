<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Ghost
 * @author    Ghost Foundation
 * @license   GPL-2.0+
 * @link      http://ghost.org
 * @copyright 2014 Ghost Foundation
 */
?>
<div class="wrap" id="ghost">
	<div id="icon-ghost" class="icon32"><br></div>
	<h2 class="title"><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<p>Hey there! We’re excited to help you migrate WordPress content over to Ghost, and this plugin is designed to help with that process by exporting your WP content into a set of files which Ghost should be able to import cleanly.</p>
	<h3>Things to keep in mind</h3>
	<ol>
		<li>Tags will be migrated, but not categories. If needed, you can <a href="https://wordpress.org/plugins/wpcat2tag-importer/" target="_blank">convert your categories to tags</a> before exporting.</li>
		<li>Ghost does not have built-in comments, but it does integrate with <a href="https://ghost.org/integrations/community/" target="_blank">many comment platforms</a> if you want to migrate your comments there.</li>
		<li>No custom fields, meta, shortcodes, post types, taxonomies or binary files will be migrated. Just regular <strong>posts</strong>, <strong>pages</strong>, <strong>tags</strong> and <strong>images</strong>.</li>
	</ol>
	<h3>Steps to follow</h3>
	<ol>
		<li>Click the "Download Ghost file" button. You will receive a <code>.json</code> file.</li>
		<li>Log into your Ghost site, and head to the “Labs” section in admin and import the file.</li>
		<li>Verify that everything is working as expected, and make any manual adjustments</li>
	</ol>

	<form id="wp-2-ghost" method="get">
		<input type="hidden" name="ghostexport" value="true">
		<?php submit_button( __( 'Download Ghost File' ) ); ?>
	</form>

	<form id="wp-2-ghost" method="get">
		<input type="hidden" name="ghostarchiveexport" value="true">
		<?php submit_button( __( 'Download Ghost Image Archive' ) ); ?>
	</form>

</div>
