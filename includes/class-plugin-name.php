<?php

// Add this at the top to ensure WordPress functions are available
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Your Name <email@example.com>
 */
class Plugin_Name {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Plugin_Name_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The MailchimpService instance.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Plugin_Name_Mailchimp_Service    $mailchimp_service    The MailchimpService instance.
	 */
	protected $mailchimp_service;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'MAILCHIMP_NEWSLETTER_ARCHIVE_VERSION' ) ) {
			$this->version = MAILCHIMP_NEWSLETTER_ARCHIVE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'plugin-name';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Plugin_Name_Loader. Orchestrates the hooks of the plugin.
	 * - Plugin_Name_i18n. Defines internationalization functionality.
	 * - Plugin_Name_Admin. Defines all hooks for the admin area.
	 * - Plugin_Name_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-plugin-name-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-plugin-name-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-plugin-name-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-plugin-name-public.php';

		require_once plugin_dir_path( __FILE__ ) . 'class-mailchimp-service.php';

		$this->loader = new Plugin_Name_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Plugin_Name_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Plugin_Name_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Plugin_Name_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Register the newsletter Custom Post Type.
	 */
	public function register_newsletter_cpt() {
		global $wp_rewrite;
		$options = get_option( 'mailchimp_newsletter_archive_options' );
		$base_slug = isset($options['base_url']) && $options['base_url'] ? $options['base_url'] : 'newsletters';
		$labels = array(
			'name'               => __( 'Newsletters', 'mailchimp-newsletter-archive' ),
			'singular_name'      => __( 'Newsletter', 'mailchimp-newsletter-archive' ),
			'add_new'            => __( 'Add New', 'mailchimp-newsletter-archive' ),
			'add_new_item'       => __( 'Add New Newsletter', 'mailchimp-newsletter-archive' ),
			'edit_item'          => __( 'Edit Newsletter', 'mailchimp-newsletter-archive' ),
			'new_item'           => __( 'New Newsletter', 'mailchimp-newsletter-archive' ),
			'view_item'          => __( 'View Newsletter', 'mailchimp-newsletter-archive' ),
			'view_items'         => __( 'View Newsletters', 'mailchimp-newsletter-archive' ),
			'not_found'          => __( 'No newsletters found', 'mailchimp-newsletter-archive' ),
			'not_found_in_trash' => __( 'No newsletters found in Trash', 'mailchimp-newsletter-archive' ),
			'all_items'          => __( 'All Newsletters', 'mailchimp-newsletter-archive' ),
			'archives'           => __( 'Newsletter Archives', 'mailchimp-newsletter-archive' ),
		);
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'has_archive'        => true,
			'supports'           => array( 'title', 'editor', 'excerpt' ),
			'rewrite'            => array(
				'slug'       => $base_slug . '/%year%/%monthnum%',
				'with_front' => false,
			),
			'show_in_rest'       => true,
		);
		register_post_type( 'newsletter', $args );
		// Add rewrite tags for year and monthnum
		add_rewrite_tag( '%year%', '([0-9]{4})' );
		add_rewrite_tag( '%monthnum%', '([0-9]{2})' );
		add_filter( 'post_type_link', array( $this, 'newsletter_permalink_filter' ), 10, 2 );
	}

	/**
	 * Filter newsletter permalinks to replace %year% and %monthnum% with post date.
	 */
	public function newsletter_permalink_filter( $permalink, $post ) {
		if ( $post->post_type !== 'newsletter' ) {
			return $permalink;
		}
		$year = get_post_time( 'Y', false, $post );
		$month = get_post_time( 'm', false, $post );
		$permalink = str_replace( '%year%', $year, $permalink );
		$permalink = str_replace( '%monthnum%', $month, $permalink );
		return $permalink;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
		add_action( 'mailchimp_newsletter_archive_cron_sync', array( __CLASS__, 'cron_sync_callback' ) );
		add_action( 'init', array( $this, 'register_newsletter_cpt' ) );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Schedule nightly sync on plugin activation.
	 */
	public static function activate() {
		if ( ! wp_next_scheduled( 'mailchimp_newsletter_archive_cron_sync' ) ) {
			wp_schedule_event( time(), 'daily', 'mailchimp_newsletter_archive_cron_sync' );
		}
		flush_rewrite_rules();
	}

	/**
	 * Clear scheduled sync on plugin deactivation.
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'mailchimp_newsletter_archive_cron_sync' );
		flush_rewrite_rules();
	}

	/**
	 * Cron callback to sync and cache Mailchimp campaigns.
	 */
	public static function cron_sync_callback() {
		$options = get_option( 'mailchimp_newsletter_archive_options' );
		$api_key = $options['api_key'] ?? '';
		$server_prefix = $options['server_prefix'] ?? '';
		$cache_ttl = $options['cache_ttl'] ?? 12;
		if ( ! class_exists( '\brianmoney\NewsletterArchive\MailchimpService' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'class-mailchimp-service.php';
		}
		$mailchimp = new \brianmoney\NewsletterArchive\MailchimpService( $api_key, $server_prefix );
		$mailchimp->sync_campaigns( $cache_ttl );
	}

	/**
	 * Migrate Mailchimp campaigns to newsletter CPT posts.
	 *
	 * Can be called from admin or cron.
	 */
	public static function migrate_campaigns_to_newsletters() {
		$options = get_option( 'mailchimp_newsletter_archive_options' );
		$api_key = $options['api_key'] ?? '';
		$server_prefix = $options['server_prefix'] ?? '';
		$cache_ttl = $options['cache_ttl'] ?? 12;
		$max_campaigns = $options['max_campaigns'] ?? 50;
		$import_status = $options['import_status'] ?? 'draft';
		if ( ! class_exists( '\brianmoney\NewsletterArchive\MailchimpService' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'class-mailchimp-service.php';
		}
		$mailchimp = new \brianmoney\NewsletterArchive\MailchimpService( $api_key, $server_prefix );
		$data = $mailchimp->get_cached_campaigns( $cache_ttl, $max_campaigns );
		if ( is_wp_error( $data ) || empty( $data['campaigns'] ) ) {
			return;
		}
		foreach ( $data['campaigns'] as $campaign ) {
			$post_title = $campaign['settings']['subject_line'] ?? '';
			$post_excerpt = $campaign['long_archive_url'] ?? '';
			$raw_content = $campaign['content']['html'] ?? '';
			$campaign_id = $campaign['id'] ?? '';
			$send_time = $campaign['send_time'] ?? '';
			if ( empty( $post_title ) || empty( $campaign_id ) ) {
				continue;
			}
			// Extract <style> blocks from <head>
			$styles = '';
			if ( preg_match_all( '/<style[^>]*>(.*?)<\/style>/is', $raw_content, $style_matches ) ) {
				foreach ( $style_matches[0] as $style_tag ) {
					$styles .= $style_tag . "\n";
				}
			}
			// Extract <body> content if present
			$post_content = $raw_content;
			if ( preg_match( '/<body[^>]*>(.*?)<\/body>/is', $raw_content, $matches ) ) {
				$post_content = $matches[1];
			}
			// Prepend styles to body content
			if ( $styles ) {
				$post_content = $styles . $post_content;
			}
			// Remove <div style="display: none; ...">...</div> blocks
			$post_content = preg_replace( '/<div[^>]*style=["\'][^>]*display:\s*none;[^>]*>.*?<\/div>/is', '', $post_content );
			// Replace double or more newlines with a styled <div> for vertical spacing
			$post_content = preg_replace( "/\n{2,}/", '<div style="margin: 1.5em 0;"></div>', $post_content );
			// Remove 'View this email in your browser' links with *|ARCHIVE|* href
			$post_content = preg_replace( '/<a[^>]*href=["\\\']\*\|ARCHIVE\|\*["\\\'][^>]*>.*?<\/a>/is', '', $post_content );
			$post_date = $send_time ? get_date_from_gmt( gmdate( 'Y-m-d H:i:s', strtotime( $send_time ) ) ) : current_time( 'mysql' );
			$post_date_gmt = $send_time ? gmdate( 'Y-m-d H:i:s', strtotime( $send_time ) ) : current_time( 'mysql', 1 );

			// 1. Try to find by campaign ID meta
			$existing = get_posts([
				'post_type'  => 'newsletter',
				'numberposts'=> 1,
				'fields'     => 'ids',
				'post_status' => 'any', // <-- ensure all statuses are included
				'meta_key'   => '_mailchimp_campaign_id',
				'meta_value' => $campaign_id,
			]);

			// 2. If not found, robust fallback: search for posts within ±1 day of campaign date and compare sanitized titles
			if ( ! $existing ) {
				$campaign_time = strtotime($post_date);
				$day_before = date('Y-m-d', $campaign_time - DAY_IN_SECONDS);
				$day_after  = date('Y-m-d', $campaign_time + DAY_IN_SECONDS);
				$possible_matches = get_posts([
					'post_type'   => 'newsletter',
					'posts_per_page' => -1,
					'post_status' => 'any', // <-- ensure all statuses are included
					'date_query'  => [
						['after' => $day_before, 'before' => $day_after, 'inclusive' => true]
					],
				]);
				$sanitized_campaign_title = sanitize_title($post_title);
				foreach ($possible_matches as $possible_post) {
					if (sanitize_title($possible_post->post_title) === $sanitized_campaign_title) {
						$existing = [$possible_post->ID];
						break;
					}
				}
			}

			$postarr = [
				'post_title'   => $post_title,
				'post_excerpt' => $post_excerpt,
				'post_content' => $post_content,
				'post_type'    => 'newsletter',
				'post_date'    => $post_date,
				'post_date_gmt'=> $post_date_gmt,
			];

			if ( $existing ) {
				$postarr['ID'] = $existing[0];
				// Do NOT set or change post_status for existing posts
				$post_id = wp_update_post( $postarr );
				// Ensure campaign ID meta is set
				if ( ! get_post_meta( $post_id, '_mailchimp_campaign_id', true ) ) {
					add_post_meta( $post_id, '_mailchimp_campaign_id', $campaign_id, true );
				}
			} else {
				$postarr['post_status'] = $import_status;
				// Ensure unique slug
				$postarr['post_name'] = wp_unique_post_slug( sanitize_title( $post_title ), 0, $import_status, 'newsletter', 0 );
				$post_id = wp_insert_post( $postarr );
				if ( $post_id && ! is_wp_error( $post_id ) ) {
					add_post_meta( $post_id, '_mailchimp_campaign_id', $campaign_id, true );
				}
			}
		}
	}

	// Flush rewrite rules if base_url changes and set a transient for admin notice
	public static function maybe_flush_rewrite_on_base_url_change() {
		if ( isset($_POST['option_page']) && $_POST['option_page'] === 'mailchimp_newsletter_archive_options' ) {
			$old = get_option('mailchimp_newsletter_archive_options');
			$new = $_POST['mailchimp_newsletter_archive_options'] ?? [];
			if ( isset($old['base_url'], $new['base_url']) && $old['base_url'] !== $new['base_url'] ) {
				flush_rewrite_rules();
				set_transient('mcna_base_url_changed', 1, 60);
			}
		}
	}

}

add_action('admin_notices', function() {
	if ( get_transient('mcna_base_url_changed') ) {
		delete_transient('mcna_base_url_changed');
		echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('You changed the Base URL for newsletters. Please go to Settings → Permalinks and click "Save Changes" to update your site URLs.','mailchimp-newsletter-archive') . '</p></div>';
	}
});
