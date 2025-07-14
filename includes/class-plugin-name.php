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
			'has_archive'        => $base_slug, // archive at /newsletters/
			'supports'           => array( 'title', 'editor', 'excerpt', 'custom-fields', 'revisions', 'author' ),
			'rewrite'            => array(
				'slug'       => $base_slug . '/%year%/%monthnum%',
				'with_front' => false,
			),
			'show_in_rest'       => true,
			'show_in_menu'       => true,
			'menu_icon'          => 'dashicons-email-alt',
			'menu_position'      => 20,
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
		add_shortcode( 'mailchimp_archive', array( $this, 'shortcode_mailchimp_archive' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_archive_styles' ) );
		// Use the correct WordPress method for CPT template loading
		add_filter( 'single_template', array( $this, 'load_single_newsletter_template' ) );
		

		add_action( 'wp_head', array( $this, 'add_newsletter_meta_tags' ) );
		add_action( 'admin_init', array( $this, 'maybe_flush_rewrite_rules' ) );
		// Ensure newsletter archive only shows newsletter posts
		add_action( 'pre_get_posts', array( $this, 'filter_newsletter_archive_query' ) );
		// Remove Archives widget/links from newsletter pages
		add_filter( 'widget_archives_args', array( $this, 'remove_archives_widget' ) );
		add_filter( 'get_archives_link', array( $this, 'remove_archives_links' ), 10, 6 );
	}

	/**
	 * Only show newsletter CPT posts on the /newsletters/ archive
	 */
	public function filter_newsletter_archive_query( $query ) {
		if (
			!is_admin() &&
			$query->is_main_query() &&
			is_post_type_archive( 'newsletter' )
		) {
			$query->set( 'post_type', 'newsletter' );
		}
	}

	public function maybe_flush_rewrite_rules() {
		// Flush rewrite rules once to ensure CPT is properly registered
		if ( ! get_option( 'mailchimp_newsletter_archive_rewrite_flushed' ) ) {
			flush_rewrite_rules();
			update_option( 'mailchimp_newsletter_archive_rewrite_flushed', true );
		}
	}



	public function load_single_newsletter_template( $template ) {
		// Simple, proven method for CPT template loading
		if ( is_singular( 'newsletter' ) ) {
			$plugin_template = plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/single-newsletter.php';
			
			if ( file_exists( $plugin_template ) ) {
				return $plugin_template;
			}
		}
		return $template;
	}



	public function enqueue_archive_styles() {
		wp_register_style( 'mailchimp-newsletter-archive-shortcode', plugin_dir_url( dirname( __FILE__ ) ) . 'public/css/plugin-name-public.css', array(), $this->get_version() );
		wp_enqueue_style( 'mailchimp-newsletter-archive-shortcode' );
	}

	public function shortcode_mailchimp_archive( $atts ) {
		$options = get_option( 'mailchimp_newsletter_archive_options' );
		$per_page = isset($options['newsletters_per_page']) ? absint($options['newsletters_per_page']) : 10;
		$paged = max( 1, get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1) );
		$args = array(
			'post_type'      => 'newsletter',
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'paged'          => $paged,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
		$query = new WP_Query($args);
		if ( ! $query->have_posts() ) {
			return '<div class="mailchimp-archive-list"><p>' . esc_html__('No newsletters found.', 'mailchimp-newsletter-archive') . '</p></div>';
		}
		$output = '<div class="mailchimp-archive-list">';
		$output .= '<ul class="mailchimp-archive-items">';
		while ( $query->have_posts() ) {
			$query->the_post();
			$output .= '<li class="mailchimp-archive-item">';
			$output .= '<a class="mailchimp-archive-title" href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a>';
			$output .= '<span class="mailchimp-archive-date">' . esc_html( get_the_date() ) . '</span>';
			// Show excerpt if available, otherwise show a brief description
			if ( has_excerpt() ) {
				$output .= '<div class="mailchimp-archive-excerpt">' . esc_html( get_the_excerpt() ) . '</div>';
			} else {
				$output .= '<div class="mailchimp-archive-excerpt">' . esc_html__('Newsletter from our mailing list.', 'mailchimp-newsletter-archive') . '</div>';
			}
			$output .= '</li>';
		}
		$output .= '</ul>';
		// Pagination
		$big = 999999999;
		$pagination = paginate_links( array(
			'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'    => '?paged=%#%',
			'current'   => $paged,
			'total'     => $query->max_num_pages,
			'type'      => 'array',
			'prev_text' => __('« Previous', 'mailchimp-newsletter-archive'),
			'next_text' => __('Next »', 'mailchimp-newsletter-archive'),
		) );
		if ( $pagination ) {
			$output .= '<div class="mailchimp-archive-pagination">';
			$output .= '<ul class="page-numbers">';
			foreach ( $pagination as $link ) {
				$output .= '<li>' . $link . '</li>';
			}
			$output .= '</ul>';
			$output .= '</div>';
		}
		$output .= '</div>';
		wp_reset_postdata();
		return $output;
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
			
			// Create a proper excerpt from the content
			$post_excerpt = '';
			$text_content = wp_strip_all_tags($post_content);
			if ( !empty($text_content) ) {
				// Remove Mailchimp merge tags like *|MC_PREVIEW_TEXT|*, *|MC:SUBJECT|*, etc.
				$text_content = preg_replace('/\*\|[^*]+\|\*/', '', $text_content);
				// Clean up extra whitespace and newlines
				$text_content = preg_replace('/\s+/', ' ', $text_content);
				$text_content = trim($text_content);
				// Take first 20 words if content is available
				if ( !empty($text_content) ) {
					$post_excerpt = wp_trim_words($text_content, 20, '...');
				} else {
					$post_excerpt = __('Newsletter from our mailing list.', 'mailchimp-newsletter-archive');
				}
			} else {
				$post_excerpt = __('Newsletter from our mailing list.', 'mailchimp-newsletter-archive');
			}
			
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
				'post_content' => $post_content,
				'post_type'    => 'newsletter',
				'post_date'    => $post_date,
				'post_date_gmt'=> $post_date_gmt,
			];

			if ( $existing ) {
				$postarr['ID'] = $existing[0];
				// Do NOT update excerpt for existing posts - let users edit it manually
				$post_id = wp_update_post( $postarr );
				// Ensure campaign ID meta is set
				if ( ! get_post_meta( $post_id, '_mailchimp_campaign_id', true ) ) {
					add_post_meta( $post_id, '_mailchimp_campaign_id', $campaign_id, true );
				}
			} else {
				$postarr['post_status'] = $import_status;
				$postarr['post_excerpt'] = $post_excerpt; // Only set excerpt for new posts
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

	public function add_newsletter_meta_tags() {
		if ( is_singular( 'newsletter' ) ) {
			global $post;
			?>
			<!-- Open Graph Meta Tags -->
			<meta property="og:type" content="article" />
			<meta property="og:title" content="<?php echo esc_attr( get_the_title() ); ?>" />
			<meta property="og:url" content="<?php echo esc_attr( get_permalink() ); ?>" />
			<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo('name') ); ?>" />
			<?php if ( has_excerpt() ) : ?>
			<meta property="og:description" content="<?php echo esc_attr( get_the_excerpt() ); ?>" />
			<?php endif; ?>
			<meta property="article:published_time" content="<?php echo get_the_date('c'); ?>" />
			<meta property="article:modified_time" content="<?php echo get_the_modified_date('c'); ?>" />
			<meta property="article:section" content="Newsletter" />
			
			<!-- Twitter Card Meta Tags -->
			<meta name="twitter:card" content="summary" />
			<meta name="twitter:title" content="<?php echo esc_attr( get_the_title() ); ?>" />
			<?php if ( has_excerpt() ) : ?>
			<meta name="twitter:description" content="<?php echo esc_attr( get_the_excerpt() ); ?>" />
			<?php endif; ?>
			
			<!-- Additional SEO Meta -->
			<meta name="robots" content="index, follow" />
			<link rel="canonical" href="<?php echo esc_attr( get_permalink() ); ?>" />
			<?php
		}
	}

	/**
	 * Remove Archives widget from newsletter pages
	 */
	public function remove_archives_widget( $args ) {
		if ( is_singular( 'newsletter' ) || is_post_type_archive( 'newsletter' ) ) {
			return false;
		}
		return $args;
	}

	/**
	 * Remove Archives links from newsletter pages
	 */
	public function remove_archives_links( $link_html, $url, $text, $format, $before, $after ) {
		if ( is_singular( 'newsletter' ) || is_post_type_archive( 'newsletter' ) ) {
			return '';
		}
		return $link_html;
	}

}

add_action('admin_notices', function() {
	if ( get_transient('mcna_base_url_changed') ) {
		delete_transient('mcna_base_url_changed');
		echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('You changed the Base URL for newsletters. Please go to Settings → Permalinks and click "Save Changes" to update your site URLs.','mailchimp-newsletter-archive') . '</p></div>';
	}
});
