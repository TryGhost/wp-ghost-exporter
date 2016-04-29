<?php
/**
 * WordPress to Ghost exporter
 *
 * @package   Ghost
 * @author	Ghost Foundation
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
	protected $version = '0.5.6';

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

		if ( isset( $_GET['ghostexport'] ) ) {
			add_action( 'init', array( $this, 'download_file' ) );
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
			'exported_on' 	=> date( 'r' ),
			'version'		=> '000',
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

				$post->post_markdown = new HTML_To_Markdown( apply_filters( 'the_content', $post->post_content ) );

				$tags = get_the_tags();
				if ( ! empty( $tags ) ) {
					foreach ( $tags as $tag ) {
						$_post_tags[] = array(
							'tag_id' => intval( $tag->term_id ),
							'post_id' => intval( $post->ID )
						);
					}
				}

				$s = $this->map_status( $post->post_status );

				$image_id = get_post_thumbnail_id( $post->ID );
				if ( $image_id !== '' ) {
					$image = wp_get_attachment_image_src( $image_id, 'full' );
				}

				$this->garray['data']['posts'][] = array(
					'id'			=> intval( $post->ID ),
					'title'			=> substr( ( empty( $post->post_title ) ) ? '(no title)' : $post->post_title, 0, 150 ),
					'slug'			=> substr( ( empty( $post->post_name ) ) ? 'temp-slug-' . $slug_number : $post->post_name, 0, 150 ),
					'markdown'		=> $post->post_markdown->output(),
					'html'			=> apply_filters( 'the_content', $post->post_content ),
					'image'			=> ( $image_id ) ? $image[0] : null,
					'featured'		=> 0,
					'page'			=> ( $post->post_type === 'page' ) ? 1 : 0,
					'status'		=> substr( $s, 0, 150 ),
					'language'		=> substr( 'en_US', 0, 6 ),
					'meta_title'	=> null,
					'meta_description'	=> null,
					'author_id'		=> $this->_safe_author_id( $post->post_author ),
					'created_at'	=> $this->_get_json_date( $post->post_date ),
					'created_by'	=> 1,
					'updated_at'	=> $this->_get_json_date( $post->post_modified ),
					'updated_by'	=> 1,
					'published_at'	=> ($s !== 'draft') ? $this->_get_json_date( $post->post_date ) : '',
					'published_by'	=> 1,
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
		unset( $s );
	}


	/**
	 * Utility function. Formats a PHP date into another date object that javascript can handle. Using epoch militime
	 * is bad if PHP is 32 bit simply because there are not enough bits to represent an integer that large.
	 * @param  string 		$date 			how php stores the dates. Usually mysql format
	 * @return string						RFC 2822 format
	 */
	private function _get_json_date( $date ) {
		return date( 'r', strtotime( $date ) );
	}


	/**
	 * Populates users on the export object
	 * @return void 						modifies in place
	 */
	private function populate_users() {
		$users = get_users();

		foreach ( $users as $user ) {
			$user_meta = get_user_meta( $user->ID );

			$this->garray['data']['users'][] = array(
				'id' => $this->_safe_author_id( $user->ID ),
				'slug' => $user->user_login,
				'bio' => substr( $user_meta['description'][0], 0, 199 ),
				'website' => $this->_safe_url( $user->user_url ),
				'created_at' => $this->_get_json_date( $user->user_registered ),
				'created_by' => 1,
				'email' => $user->user_email,
				'name' => $user->user_nicename,
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
	 * @return stroing			the Ghost status
	 */
	private function map_status( $wp_status ) {
		$teh_mappingZ = array(
			'publish' => 'published',
			'draft' => 'draft',
			'future' => 'draft',
			'private' => 'draft',
			'pending' => 'draft',
		);
		return $teh_mappingZ[ $wp_status ];
	}


	/**
	 * Gets an array, returns a json
	 * @param  array $thearray input array
	 * @return string		   output json
	 */
	public function get_json( $thearray ) {
		return json_encode( $thearray );
	}


	/**
	 * Sends necessary headers and whatnot to allow to download file
	 * @return bin file
	 */
	public function download_file() {

		// Ensure the user accessing the function actually has permission to do this
		if ( ! current_user_can('export') ) {
			wp_die( "<p>You are not allowed to do that.</p>", 'Permission error' );
		}

		$this->populate_data();

		$upload_dir = wp_upload_dir();
		$filedir = $upload_dir['path'];
		$filename = 'wp2ghost_export_' . time() . '.json';

		if ( ! is_writable( $filedir ) ) {
			wp_die( "<p>Uploads directory is not writable, can't save the Ghost json file :/</p><p>Generated by the Ghost plugin version {$this->version}.</p>", 'Directory not writable' );
		}

		$handle = fopen( $filedir . '/' . $filename, 'w' );
		$content = $this->get_json( $this->garray );
		fwrite( $handle, $content );
		fclose( $handle );

		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename='.$filename );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . filesize( $filedir . '/' . $filename ) );

		flush();
		readfile( $filedir . '/' . $filename );
		exit;
	}

	private function _visualize_bool( $bool ) {
		return ($bool) ? '_true' : 'false';
	}

	public function add_full_size_image( $image_sizes ) {
		$image_sizes[] = 'full';
		return $image_sizes;
	}
}
