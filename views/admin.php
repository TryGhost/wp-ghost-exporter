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
 * @copyright 2013 Ghost Foundation
 */
?>
<div class="wrap" id="ghost">
	<div id="icon-ghost" class="icon32"><br></div>
	<h2 class="title"><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<p>First off, we're absolutely super-excited you decided to move your WordPress blog to Ghost. We hope you'll find it an amazing experience to work with it. However, since it literally launched a few days into the future to when I'm writing these lines, I'd like you to be aware of a few things before you take the plunge and pull the plug on the WordPress world.</p>
	<h3>Things to keep in mind</h3>
	<ol>
		<li>Ghost does not, and will not deal with comments. We are going to be using <a href="http://disqus.com/">Disqus</a> to handle comments, so if you don't have one already, create an account, and migrate your existing comments over there from WordPress. (There's a plugin / functionality for this in their documentation)</li>
		<li>Ghost has very rudimentary media handling at the moment, and currently grabbing images from WordPress and plucking them into Ghost is tedious. For that reason it might be a good idea to sign up for a free <a href="http://cloudinary.com/">Cloudinary</a> account, download their plugin, connect the plugin to the account (all of these are super easy), and then under the Media option in the admin menu, tick all the images, and from the Bulk Actions drop down select Upload to Cloudinary and click Apply. (Hint: if you open Screen options and choose to view more than 20 items per page, this process will take less time)</li>
		<li>Currently there's no automated way of dealing with files other than images. Cloudinary will not handle your pdfs.</li>
		<li>Shortcodes are not implemented, so those will be plain text. There will be attempts at handling easy ones (like [code][/code]), but expect those to break.</li>
		<li>Custom post types and pages are ignored entirely for now.</li>
		<li>Any metadata attached to blog posts are lost. Remember, Ghost is just a blogging platform.</li>
		<li>It will however keep tags and with the posts. Categories need to be converted to tags first! See the WordPress documentation on how to convert categories to tags.</li>

	</ol>
	<h3>Let's export data!</h3>
	<ol>
		<li>Once you've moved media and comments into the cloud, it's time to press the blue "Download Ghost file" button. You will receive a <code>.json</code> file.</li>
		<li>Next up spin up your Ghost installation, create a user, or sign in, if you already have an account, navigate to http://yourblogadress/ghost/debug/, and import the posts from the file.</li>
		<li>You may or may not be logged out at the end of this procedure, depends on the ammount of posts you're moving over. More posts tend not to log you out.</li>

	</ol>
	<p class="accent">If all went well, you now have all your content in Ghost complete with images.</p>
	<p>(however, comments are simply kept in a safe place for now)</p>
	<p>We hope you enjoy your discovery and experience of Ghost. If something is not right, don't hesitate to get in touch with us through <a href="http://ghost.org">the Ghost HQ</a></p>

	<form id="wp-2-ghost" method="get">
		<input type="hidden" name="ghostexport" value="true">
		<?php submit_button( __('Download Ghost File') ); ?>

	</form>

</div>
