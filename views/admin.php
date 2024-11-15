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
	<div id="icon-ghost"></div>
	
	<h2 class="title"><?php echo esc_html( get_admin_page_title() ); ?></h2>
	
	<p>Hey there! We’re excited to help you migrate WordPress content over to Ghost, and this plugin is designed to help with that process by exporting your WP content into a set of files which Ghost should be able to import cleanly.</p>
	
	<h3>Things to keep in mind</h3>
	
	<ol>
		<li>Categories will be included & converted to tags. Your existing tags will also be migrated.</li>
		<li>No custom fields, meta, shortcodes, post types, custom taxonomies, comments, or binary files will be migrated. Just regular <strong>posts</strong>, <strong>pages</strong>, <strong>tags</strong> and <strong>images</strong>.</li>
	</ol>
	
	<h3>Steps to follow</h3>
	
	<ol>
		<li>Click the "Download Ghost file" button. You will receive an import file for Ghost.</li>
		<li>Log into your Ghost site, and head to Settings and scroll to the “Import/Export” section in admin and use the “Universal import” button import the file.</li>
		<li>Verify that everything is working as expected, and make any manual adjustments.</li>
	</ol>

	<hr>

	<p>Download JSON and Images as a zip file</p>

	<?php
		if (class_exists('ZipArchive')) {
			echo '<form id="wp-2-ghost" method="get">';
				echo '<input type="hidden" name="ghostexport" value="true">';
				submit_button( __( 'Download Ghost File' ) );
			echo '</form>';
		} else {
			echo '<form id="wp-2-ghost" method="get">';	
				submit_button( __( 'Download Ghost File' ), 'primary', 'submit', true, array( 'disabled' => 'disabled' ) );
				echo '<p class="description" style="font-style: italic; margin-top: -1.5em;">Cannot create ZIP files. Please enable <code>ZipArchive</code></p>';
			echo '</form>';
		}
	?>

	<p>Struggling with the zip file? Download the <code>.json</code> instead.<br>Find out how to move your images in the <a href="https://ghost.org/docs/migration/wordpress/?utm_source=wp-ghost-plugin" target="_blank">WordPress migration guide</a>.</p>

	<form id="wp-2-ghost-json" method="get">
		<input type="hidden" name="ghostjsonexport" value="true">
		<?php submit_button( __( 'Download JSON' ), 'secondary' ); ?>
	</form>

	<hr/>

	<div id="ghost-diagnostics">
		<?php
			// Set diagnostic variables
			$gMaxExecutionTime = ini_get('max_execution_time');
			$gMemoryLimit = ini_get('memory_limit');
			$ghostMigrator = new ghost();
			$zipArchiveInstalled = (class_exists('ZipArchive')) ? 'Yes' : 'No';
			$wp_upload_dir = wp_upload_dir();
			$wp_upload_basedir = $wp_upload_dir['basedir'];
		?>

		<h4>Ghost Migrator <?php echo $ghostMigrator->getghostmigratorversion(); ?> - Diagnostics</h4>
		<p>PHP version: <?php echo phpversion(); ?></p>
		<p>PHP ZipArchive Installed: <?php echo $zipArchiveInstalled; ?></p>
		<p>Memory Limit: <?php echo $gMemoryLimit; ?></p>
		<p>Max Execution Time: <?php echo $gMaxExecutionTime; ?></p>
		<p>Media file size: <?php echo size_format(recurse_dirsize($wp_upload_basedir)); ?></p>
	</div>
</div>
