<?php
/**
 * WordPress to Ghost exporter
 *
 * @package   Ghost
 * @author	  Ghost Foundation
 * @license   GPL-2.0+
 * @link	  http://ghost.org
 * @copyright 2014 Ghost Foundation
 */


/**
 * Plugin class.
 *
 * @package Ghost
 * @author  Ghost Foundation
 */
class Ghost {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   0.0.1
	 *
	 * @var	 string
	 */
	protected $version = '1.4.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since	0.0.1
	 *
	 * @var	  string
	 */
	protected $plugin_slug = 'ghost';

	/**
	 * Instance of this class.
	 *
	 * @since	0.0.1
	 *
	 * @var	  object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since	0.0.1
	 *
	 * @var	  string
	 */
	protected $plugin_screen_hook_suffix = null;

	protected $garray = null;
	protected $instead_of_1 = 0;
	protected $date_format = 'Y-m-d\TH:i:sP';
	protected $ghost_image_base = 'content/images/wordpress';
	protected $json_file_name = 'wp_ghost_export.json';
	protected $zip_file_name = 'wp_ghost_export.zip';

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since	 0.0.1
	 */
	private function __construct() {
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Init deleting if old exports
		add_action( 'init', array( $this, 'deleteLocalExportFiles' ) );

		if ( isset( $_GET['ghostexport'] ) ) {
			add_action( 'init', array( $this, 'download_file' ) );
		}

		if ( isset( $_GET['ghostjsonexport'] ) ) {
			add_action( 'init', array( $this, 'download_json' ) );
		}

		// Adds the Export link
		add_filter( 'plugin_action_links_ghost/ghost.php', array($this, 'plugin_action_links'), 10, 4 );

		add_filter( 'intermediate_image_sizes', array( $this, 'add_full_size_image' ) );
	}

	/**
	 * Adds the Export link to the Ghost links
	 * @param  array 		$actions 		the actions already there (Deactivate / Edit)
	 * @param  string 		$file			the name of the plugin file
	 * @param  array 		$data			all the info about the plugin
	 * @param  string 		$context 		mustuse|dropins|active|inactive
	 * @return array		  				the new actions
	 */
	function plugin_action_links( $actions, $file, $data, $context ) {
		$actions['export'] = '<a href="' . admin_url( 'tools.php?page=ghost' ) . '">Export</a>';
		return $actions;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since	 1.0.0
	 *
	 * @return	object						A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since	1.0.0
	 *
	 * @param	boolean		$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is
	 *							   		disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		// TODO: Define activation functionality here
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since	1.0.0
	 *
	 * @param	boolean		$network_wide	True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is
	 *							   		disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		// TODO: Define deactivation functionality here
	}
	
	/**
	 * Deleted locally saved export files, which prior versions saved in the uploads directory.
	 * 
	 * @since 1.5.0
	 */
	function deleteLocalExportFiles() {
		// Get the files in the directory, ignoring the . and ..
		$upload_dir = wp_upload_dir();
		$gfiledir = $upload_dir['basedir'] . '/ghost-exports';

		// Return if directory does not exist
		if ( ! is_dir($gfiledir) ) {
			return;
		}

		$filesInDir = scandir($gfiledir);
		$slicesFilesInDir = array_slice($filesInDir);

		// Delete each file
		foreach ($slicesFilesInDir as $file) {
			unlink($gfiledir . '/' . $file);
		}

		// And delete the directory
		rmdir($gfiledir);
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since	1.0.0
	 */
	public function load_plugin_textdomain() {
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'ghost', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since	 1.0.0
	 *
	 * @return	null						Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
		}
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since	 1.0.0
	 *
	 * @return	null						Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), false, $this->version );
		}
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since	1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since	1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), $this->version );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since	1.0.0
	 */
	public function add_plugin_admin_menu() {
		$this->plugin_screen_hook_suffix = add_management_page(
			__( 'Export to Ghost', $this->plugin_slug ),
			__( 'Export to Ghost', $this->plugin_slug ),
			'read',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since	1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Populates the meta bit of the json file
	 * @return void 						modifies it in place
	 */
	private function populate_meta() {
		$this->garray['meta'] = array(
			'exported_on' 	=> date( $this->date_format, time() ),
			'version'		=> '2.31.0',
		);
	}

	/**
	 * Sets arrays
	 * @return void 						modifies in place
	 */
	private function prepare_garray_structure() {
		$this->garray['data'] = array();
		$this->garray['data']['posts'] = array();
		$this->garray['data']['tags'] = array();
		$this->garray['data']['posts_tags'] = array();
		$this->garray['data']['posts_authors'] = array();
		$this->garray['data']['users'] = array();
	}

	/**
	 * Deals with populating the tags onto the export object
	 * @return void 						modifies in place
	 */
	private function populate_tags() {
		$all_tags = get_tags();

		if ( ! empty( $all_tags ) ) {
			foreach ( $all_tags as $tag ) {
				$this->garray['data']['tags'][] = array(
					'id' => intval( $tag->term_id ),
					'name' => $tag->name,
					'slug' => $tag->slug,
					'description' => $tag->description,
				);
			}
		}

		$this->garray['data']['tags'][] = array(
			'id' => 999999999999,
			'name' => '#wordpress',
			'slug' => 'hash-wordpress'
		);

		// cleanup
		unset( $all_tags );
		unset( $tag );
	}

	/**
	 * Populates the posts on the export object
	 * @return void 						modifies in place
	 */
	private function populate_posts() {
		$posts_args = array(
			'post_type'			=> array( 'post', 'page' ),
			'posts_per_page'	=> -1,
			'order'				=> 'ASC',
			'orderby'			=> 'date',
		);
		$posts = new WP_Query( $posts_args );
		$slug_number = 0;
		$_post_tags = array();

		if ( $posts->have_posts() ) {
			while ( $posts->have_posts() ) {
				global $post;
				$posts->the_post();

				$tags = get_the_tags();
				if ( ! empty( $tags ) ) {
					foreach ( $tags as $tag ) {
						$_post_tags[] = array(
							'tag_id' => intval( $tag->term_id ),
							'post_id' => intval( $post->ID )
						);
					}
				}

				// Push #wordpress tag to post
				$_post_tags[] = array(
					'tag_id' => 999999999999,
					'post_id' => intval( $post->ID )
				);

				$status = $this->map_status( $post->post_status );

				$image_id = get_post_thumbnail_id( $post->ID );
				if ( $image_id !== '' ) {
					$image = wp_get_attachment_image_src( $image_id, 'full' );
					$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
					$image_caption = wp_get_attachment_caption( $image_id );
				}

				// Get the post content, with filters applied, as if it were used in a template file
				$post_content = apply_filters( 'the_content', $post->post_content );

				// Change the absolute image URLs to be relative, with the directory structure
				$corrected_post_content = str_replace( get_site_url() .'/wp-content/uploads', '/' . $this->ghost_image_base, $post_content );

				// Post meta. Here as separate variables to enable future modification based on output from common plugins.
				// This works with Yoast.
				$post_meta = get_post_meta( $post->ID );
				$post_meta_title 		= ( isset( $post_meta['_page_title'] ) ) ? $post_meta['_page_title'][0] : null;
				$post_meta_description	= ( isset( $post_meta['_meta_description'] ) ) ? $post_meta['_meta_description'][0] : null;

				$this->garray['data']['posts'][] = array(
					'id'				=> intval( $post->ID ),
					'title'				=> substr( ( empty( wp_strip_all_tags( $post->post_title, true ) ) ) ? '(untitled)' : html_entity_decode( wp_strip_all_tags( $post->post_title, true ) ), 0, 150 ),
					'slug'				=> substr( ( empty( $post->post_name ) ) ? 'temp-slug-' . $slug_number : $post->post_name, 0, 150 ),
					'mobiledoc' 		=> '{"version":"0.3.1","atoms":[],"cards":[["html",{"html":"'.str_replace(
						array(
							'\n',
							'\\/',
						),
						array(
							'\\n',
							'/',
						),
						json_encode($corrected_post_content) ) .'"}]],"markups":[],"sections":[[10,0],[1,"p",[]]]}',
					'feature_image'		=> ( $image_id !== 0 && $image ) ? $image[0] : null,
					'feature_image_alt'		=> ( $image_id !== 0 && $image_alt ) ? substr( $image_alt, 0, 125 ) : null,
					'feature_image_caption'		=> ( $image_id !== 0 && $image_caption ) ? substr( html_entity_decode( $image_caption ), 0, 65535 ) : null,
					'featured'			=> 0,
					'type'				=> ( $post->post_type === 'page' ) ? 'page' : 'post',
					'status'			=> substr( $status, 0, 150 ),
					'meta_title'		=> $post_meta_title,
					'meta_description'	=> $post_meta_description,
					'created_at'		=> $this->_get_json_date( $post->post_date ),
					'updated_at'		=> $this->_get_json_date( $post->post_modified ),
					'published_at'		=> ($status !== 'draft') ? $this->_get_json_date( $post->post_date ) : null,
				);

				$this->garray['data']['posts_authors'][] = array(
					'post_id'		=> intval( $post->ID ),
					'author_id'		=> $this->_safe_author_id( $post->post_author )
				);

				$slug_number += 1;
			}
		}

		$this->garray['data']['posts_tags'] = $_post_tags;

		// cleanup
		unset( $posts_args );
		unset( $posts );
		unset( $slug_number );
		unset( $_post_tags );
		unset( $tags );
		unset( $tag );
		unset( $status );
	}

	/**
	 * Utility function. Formats a PHP date into another date object that javascript can handle. Using epoch militime
	 * is bad if PHP is 32 bit simply because there are not enough bits to represent an integer that large.
	 * @param  string 		$date 			how php stores the dates. Usually mysql format
	 * @return string						ISO 8601 format
	 */
	private function _get_json_date( $date ) {
		return date( $this->date_format, strtotime( $date ) );
	}

	/**
	 * Convert the WordPress user slug to the name that Ghost can use
	 * @param string $wp_role The WordPress role slug
	 * @return string The Ghost role name
	 */
	private function _get_ghost_user_role( $wp_role ) {
		switch ( $wp_role ) :
			case "administrator":
				return "Administrator";
				break;
			case "editor":
				return "Editor";
				break;
			case "author":
				return "Author";
				break;
			default:
				return "Contributor";
				break;
		endswitch;
	}

	/**
	 * Populates users on the export object
	 * @return void 						modifies in place
	 */
	private function populate_users() {
		$users = get_users();

		foreach ( $users as $user ) {
			// If the user cannot edit posts, skip them
			// WP capabilities table: https://wordpress.org/support/article/roles-and-capabilities/#capability-vs-role-table
			if ( ! $user->has_cap( 'edit_posts' ) ) {
				continue;
			}

			$user_meta = get_user_meta( $user->ID );

			$this->garray['data']['users'][] = array(
				'id' => $this->_safe_author_id( $user->ID ),
				'slug' => $user->user_login,
				'bio' => substr( $user_meta['description'][0], 0, 199 ),
				'website' => $this->_safe_url( $user->user_url ),
				'created_at' => $this->_get_json_date( $user->user_registered ),
				'email' => ($user->user_email) ? $user->user_email : $user->user_login . '@example.com',
				'name' => ($user->display_name) ? $user->display_name : $user->user_login,
				'profile_image' => get_avatar_url( $user->ID, ['size' => 512] ),
				'roles' => [$this->_get_ghost_user_role( $user->roles[0] )]
			);
		}

		//cleanup
		unset( $users );
		unset( $user );
		unset( $user_meta );
	}

	/**
	 * Gets the raw data in an array that will be later turned into glorious escaped json format
	 * so Ghost can gobble it up
	 * @return array 						everything we need, but in an array
	 */
	public function populate_data() {
		if ( $this->garray !== null ) {
			return;
		}

		$this->_safe_url( 'http://google.com' );

		$this->garray = array();

		// preps the structure
		$this->prepare_garray_structure();

		// attaches metadata
		$this->populate_meta();

		// get the users
		$this->populate_users();

		// attaches tags
		$this->populate_tags();

		// populates posts
		$this->populate_posts();
	}

	/**
	 * Utility function. This is to make sure that user #1 is unique in all cases, and is not 1
	 * @param  integer 		$id 			the ID of a user
	 * @return integer	 					either $id, or self::instead_of_1
	 */
	private function _safe_author_id( $id ) {
		return intval( $id );
	}

	/**
	 * It's a REGEXP for matching against urls. WordPress's http thing is a bit more permissive and could kill the
	 * import script on Ghost's end. So in order to safeguard against that, I had to implement something like the
	 * validator.js regexp. Actually I did implement that one. Had to swap a few things.
	 *
	 * I am not proud of this.
	 *
	 * @param  string 		$url 			what the user should have as their web link
	 * @return string	  					either '' or the $url as is
	 */
	private function _safe_url( $url ) {
		if ( gettype( $url ) != 'string' ) {
			// How did you even achieve this state?
			return '';
		}

		if ( strlen( $url ) >= 2083 ) {
			// Nope, way too long!
			return '';
		}

		// OH MY GOD WHY
		$urlPattern = '/^(?!mailto:)(?:(?:https?):\/\/)(?:\S+(?::\S*)?@)?(?:(?:(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[0-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:www.)?)?(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?-?_?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?-?_?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))|localhost)(?::(\d{1,5}))?(?:(?:\/|\?|#)[^\s]*)?$/iu';

		preg_match( $urlPattern, $url, $matches );

		if ( empty( $matches ) ) {
			// You are not the URL we're looking for
			return '';
		}

		// You can go about your business, move along...
		return $url;
	}

	/**
	 * Helper function to map WordPress statuses to Ghost statuses
	 * @param  string $wp_status the WordPress status
	 * @return string			the Ghost status
	 */
	private function map_status( $wp_status ) {
		$teh_mappingZ = array(
			'publish' => 'published',
			'draft' => 'draft',
			'future' => 'draft',
			'private' => 'draft',
			'pending' => 'draft',
		);
		return $teh_mappingZ[$wp_status];
	}

	/**
	 * Gets an array, returns a json
	 * @param  array $thearray input array
	 * @return string		   output json
	 */
	public function get_json( $thearray ) {
		return wp_json_encode( $thearray );
	}
	
	/**
	 * Remove extra backslashes and quotes added due to double encoding and replace WordPress uploads URL with Ghost relative URL.
	 * @param string $content
	 * @return string
	 */
	private function clean_content_json( $content = '' ) {
		$upload_dir = wp_upload_dir();
		$guploadurl = $upload_dir['baseurl'];
		$guploadurl_escaped = addcslashes( $guploadurl, "/" );

		$cleaned_content = str_replace(
			array(
				'\\"\\"',
				$guploadurl_escaped,
			),
			array(
				'\\"',
				'/' . $this->ghost_image_base,
			),
			$content);

		return $cleaned_content;
	}

	/**
	 * Sends necessary headers and whatnot to allow to download file.
	 * Generates one all inclusive zip archive with images and the JSON export.
	 * @return bin file
	 */
	public function download_file() {
		// Check to confirm that the minimum PHP version is installed.
		if (version_compare(phpversion(), '5.6.0', '<')) {
		  	wp_die( "<p>You are running PHP " . phpversion() . ".</p><p>This version is out of date and not supported.</p><p>Please upgrade to PHP 5.6 or newer.</p>" );
		}

		$this->populate_data();

		$upload_dir = wp_upload_dir();
		$filedir = $upload_dir['basedir'];

		$content = $this->get_json( $this->garray );

		// Remove extra backslashes and quotes added due to double encoding and replace WordPress uploads URL with Ghost relative URL.
		$cleaned_content = $this->clean_content_json($content);

		// Ensure ZipArchive is installed.
		if ( ! class_exists('ZipArchive') ) {
			wp_die( "<p>PHP <a href=\"https://www.php.net/manual/en/class.ziparchive.php\" target=\"_blank\">ZipArchive</a> is not installed or enabled.</p>" );
		}

		// Initialise the archive object
		$gziparchive = new ZipArchive();

		$tmp_file = tmpfile();
		$tmp_location = stream_get_meta_data($tmp_file)['uri'];

		$res = $gziparchive->open($tmp_location, ZipArchive::CREATE);

		// Create recursive directory iterator
		/** @var SplFileInfo[] $files */
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $filedir ),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		// Set which files to exclude from being zipped.
		$gexclusions = array( $this->zip_file_name );
		// Set which extensions to include.
		$gincludedextensions = array( 'jpg', 'jpeg', 'gif', 'png', 'svg', 'svgz', 'ico', 'webp' );

		foreach ( $files as $name => $file ) {
			// Get extension for the file being processed.
			$gextension = pathinfo( $file->getFilename(), PATHINFO_EXTENSION );
			// Skip directories (they get added automatically)
			if ( ! $file->isDir() && ! in_array( $file->getFilename(), $gexclusions ) && in_array( $gextension, $gincludedextensions ) ) {
				// Get real and relative path for current file
				$filePath = $file->getRealPath();
				$relativePath = substr( $filePath, strlen($filedir) + 1 );
				// Add current file to archive in dedicated WordPress folder within a Ghost compatible directory structure.
				$gziparchive->addFile( $filePath, $this->ghost_image_base . '/' . $relativePath );
			}
		}

		$gziparchive->addFromString( 'json/' . $this->json_file_name, $cleaned_content );

		$gziparchive->close();

		header( 'Content-type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename=' . $this->zip_file_name );

		echo( readfile( $tmp_location ) );

		exit;
	}

	/**
	 * JSON only export fallback. This is for in case the server gives trouble generating the ZIP file due to a missing PHP extension.
	 * @return bin file
	 */
	public function download_json() {
		// Check to confirm that the minimum PHP version is installed.
		if (version_compare(phpversion(), '5.6.0', '<')) {
			wp_die( "<p>You are running PHP " . phpversion() . ".</p><p>This version is out of date and not supported.</p><p>Please upgrade to PHP 5.6 or newer.</p>" );
		}

		$this->populate_data();

		$content = $this->get_json( $this->garray );

		// Remove extra backslashes and quotes added due to double encoding and replace WordPress uploads URL with Ghost relative URL.
		$cleaned_content = $this->clean_content_json( $content );

		header ( 'Content-Type: application/octet-stream' );
		header ( 'Content-disposition: attachment; filename=' . $this->json_file_name );
		echo $cleaned_content;

		exit;
	}

	private function _visualize_bool( $bool ) {
		return ($bool) ? '_true' : 'false';
	}

	public function add_full_size_image( $image_sizes ) {
		$image_sizes[] = 'full';
		return $image_sizes;
	}

	public function getghostmigratorversion() {
		return $this->version;
	}
}
