<?php
/**
 * Plugin Name.
 *
 * @package   Ghost
 * @author    Ghost Foundation
 * @license   GPL-2.0+
 * @link      http://ghost.org
 * @copyright 2013 Ghost Foundation
 */

// ini_set('display_errors', E_ALL);

if( !function_exists('es_preit') ) {
	function es_preit( $obj, $echo = true ) {
		if( $echo ) {
			echo '<pre>';
			print_r( $obj );
			echo '</pre>';
		} else {
			return print_r( $obj, true );
		}
	}
}

if( !function_exists('es_silent') ) {
	function es_silent( $obj ) {
	  	?>
	    <div style="display: none">
	        <pre>
	            <?php print_r( $obj ); ?>
	        </pre>
	    </div>
	    <?php
	}
}
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
	 * @var     string
	 */
	protected $version = '0.3.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    0.0.1
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'ghost';

	/**
	 * Instance of this class.
	 *
	 * @since    0.0.1
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    0.0.1
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     0.0.1
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
		// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Define custom functionality. Read more about actions and filters: http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		if( isset($_GET['ghostexport'])) {
			add_action( 'init', array( $this, 'download_file' ) );
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
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
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		// TODO: Define activation functionality here
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'ghost', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
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
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), false, $this->version );
			// wp_enqueue_script( $this->plugin_slug . '-modernizr', plugins_url( 'js/modernizr.dnd.min.js', __FILE__ ), array( 'underscore' ), $this->version );
			// wp_enqueue_script( 'underscore', 'jquery' );
		}

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), $this->version );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * TODO:
		 *
		 * Change 'Page Title' to the title of your plugin admin page
		 * Change 'Menu Text' to the text for menu item for the plugin settings page
		 * Change 'plugin-name' to the name of your plugin
		 */
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
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Gets the raw data in an array that will be later turned into glorious escaped json format
	 * so Ghost can gobble it up
	 * @return array everything we need, but in an array
	 */
	public function get_array() {
		$garray = array();
		$garray['meta'] = array(
			'exported_on' 	=> time() * 1000,
			'version'		=> "000"
		);
		$garray['data'] = array();
		$garray['data']['posts'] = array();
		$garray['data']['tags'] = array();
		$garray['data']['posts_tags'] = array();

		$all_tags = get_tags();

		if(!empty($all_tags)) {
			foreach ($all_tags as $tag) {
				$garray['data']['tags'][] = array(
					'id' => intval( $tag->term_id ),
					'name' => $tag->name,
					'slug' => $tag->slug,
					'description' => $tag->description
				);
			}
		}


		$posts_args = array(
			'post_type'			=> 'post',
			'posts_per_page'	=> -1,
			'order'				=> 'ASC',
			'orderby'			=> 'date',
		);
		$posts = new WP_Query( $posts_args );
		$slug_number = 0;
		$_post_tags = array();

		if( $posts->have_posts() ) {
			while( $posts->have_posts() ) {
				global $post;
				$posts->the_post();


				$tags = get_the_tags();
				if(!empty($tags)){
					foreach ($tags as $tag) {
						$_post_tags[] = array(
							'tag_id' => intval( $tag->term_id ),
							'post_id' => intval( $post->ID )
						);
					}
 				}

				$garray['data']['posts'][] = array(
					'id'			=> intval( $post->ID ),
					'title'			=> (empty($post->post_title))?'(no title)':$post->post_title,
					'slug'			=> (empty($post->post_name))?'temp-slug-'.$slug_number:$post->post_name,
					'markdown'		=> $post->post_content,
					'html'			=> $post->post_content,
					"image"			=> null,
            		"featured"		=> 0,
            		"page"			=> 0,
		            "status"		=> self::map_status( $post->post_status ),
		            "language"		=> "en_US",
		            "meta_title"	=> null,
		            "meta_description"	=> null,
		            "author_id"		=> 1,
		            "created_at"	=> strtotime( $post->post_date ) * 1000,
		            "created_by"	=> 1,
		            "updated_at"	=> strtotime( $post->post_modified ) * 1000,
		            "updated_by"	=> 1,
		            "published_at"	=> strtotime( $post->post_date ) * 1000,
		            "published_by"	=> 1
	            );

	            $slug_number += 1;
			}
		}

		$garray['data']['posts_tags'] = $_post_tags;

		return $garray;
	}


	/**
	 * Helper function to map WordPress statuses to Ghost statuses
	 * @param  string $wp_status the WordPress status
	 * @return stroing            the Ghost status
	 */
	private function map_status ($wp_status) {
		$teh_mappingZ = array(
			'publish' => 'published',
			'draft' => 'draft',
			'future' => 'draft'
		);
		return $teh_mappingZ[ $wp_status ];
	}


	/**
	 * Gets an array, returns a json
	 * @param  array $thearray input array
	 * @return string           output json
	 */
	public function get_json( $thearray ) {
		return json_encode( $thearray );
	}


	/**
	 * Sends necessary headers and whatnot to allow to download file
	 * @return bin file
	 */
	public function download_file() {
		$filedir = dirname(__FILE__);
		$filename = 'wp2ghost_export_' . time() . '.json';

		!$handle = fopen($filedir . "/" . $filename, 'w');
		$content = self::get_json( self::get_array() );
		fwrite( $handle, $content );
		fclose( $handle );

		header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename='.$filename);
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($filedir . "/" . $filename));

	    flush();
	    readfile($filedir . "/" . $filename);
	    exit;
	}
}
