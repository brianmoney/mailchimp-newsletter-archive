<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class Plugin_Name_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'settings_admin_notice' ) );
	}

	/**
	 * Add the plugin settings page to the admin menu.
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'Mailchimp Newsletter Archive Settings', 'mailchimp-newsletter-archive' ),
			__( 'Mailchimp Archive', 'mailchimp-newsletter-archive' ),
			'manage_options',
			'mailchimp-newsletter-archive',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings() {
		register_setting( 'mailchimp_newsletter_archive_options', 'mailchimp_newsletter_archive_options', array( $this, 'sanitize_settings' ) );

		add_settings_section(
			'mailchimp_newsletter_archive_main',
			__( 'Mailchimp API Settings', 'mailchimp-newsletter-archive' ),
			'__return_false',
			'mailchimp-newsletter-archive'
		);

		add_settings_field(
			'api_key',
			__( 'API Key', 'mailchimp-newsletter-archive' ),
			array( $this, 'field_api_key' ),
			'mailchimp-newsletter-archive',
			'mailchimp_newsletter_archive_main'
		);
		add_settings_field(
			'server_prefix',
			__( 'Server Prefix', 'mailchimp-newsletter-archive' ),
			array( $this, 'field_server_prefix' ),
			'mailchimp-newsletter-archive',
			'mailchimp_newsletter_archive_main'
		);
		add_settings_field(
			'audience_id',
			__( 'Audience ID', 'mailchimp-newsletter-archive' ),
			array( $this, 'field_audience_id' ),
			'mailchimp-newsletter-archive',
			'mailchimp_newsletter_archive_main'
		);
		add_settings_field(
			'cache_ttl',
			__( 'Cache TTL (hours)', 'mailchimp-newsletter-archive' ),
			array( $this, 'field_cache_ttl' ),
			'mailchimp-newsletter-archive',
			'mailchimp_newsletter_archive_main'
		);
		add_settings_field(
			'max_campaigns',
			__( 'Max Campaigns', 'mailchimp-newsletter-archive' ),
			array( $this, 'field_max_campaigns' ),
			'mailchimp-newsletter-archive',
			'mailchimp_newsletter_archive_main'
		);
		add_settings_field(
			'import_status',
			__( 'Import Status', 'mailchimp-newsletter-archive' ),
			array( $this, 'field_import_status' ),
			'mailchimp-newsletter-archive',
			'mailchimp_newsletter_archive_main'
		);
		add_settings_field(
			'base_url',
			__( 'Base URL', 'mailchimp-newsletter-archive' ),
			array( $this, 'field_base_url' ),
			'mailchimp-newsletter-archive',
			'mailchimp_newsletter_archive_main'
		);
	}

	/**
	 * Sanitize settings before saving.
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();
		$sanitized['api_key'] = sanitize_text_field( $input['api_key'] ?? '' );
		$sanitized['server_prefix'] = sanitize_text_field( $input['server_prefix'] ?? '' );
		$sanitized['audience_id'] = sanitize_text_field( $input['audience_id'] ?? '' );
		$sanitized['cache_ttl'] = absint( $input['cache_ttl'] ?? 12 );
		$sanitized['max_campaigns'] = absint( $input['max_campaigns'] ?? 50 );
		$allowed_statuses = array( 'draft', 'publish', 'pending', 'private' );
		$sanitized['import_status'] = in_array( $input['import_status'] ?? 'draft', $allowed_statuses, true ) ? $input['import_status'] : 'draft';
		// Sanitize base_url (slug): lowercase, no spaces, only a-z0-9-_ (default: newsletters)
		$base_url = sanitize_title_with_dashes( $input['base_url'] ?? 'newsletters' );
		$sanitized['base_url'] = $base_url ? $base_url : 'newsletters';
		return $sanitized;
	}

	/**
	 * Render the settings page.
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Mailchimp Newsletter Archive Settings', 'mailchimp-newsletter-archive' ); ?></h1>
			<?php $this->handle_manual_sync(); ?>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'mailchimp_newsletter_archive_options' );
				do_settings_sections( 'mailchimp-newsletter-archive' );
				submit_button();
				?>
			</form>
			<form method="post">
				<?php wp_nonce_field( 'mailchimp_newsletter_archive_sync', 'mailchimp_newsletter_archive_sync_nonce' ); ?>
				<input type="hidden" name="mailchimp_newsletter_archive_sync" value="1" />
				<?php submit_button( __( 'Sync Now', 'mailchimp-newsletter-archive' ), 'secondary', 'mailchimp_newsletter_archive_sync_btn', false ); ?>
			</form>
			<form method="post">
				<?php wp_nonce_field( 'mailchimp_newsletter_archive_migrate', 'mailchimp_newsletter_archive_migrate_nonce' ); ?>
				<input type="hidden" name="mailchimp_newsletter_archive_migrate" value="1" />
				<?php submit_button( __( 'Import Campaigns', 'mailchimp-newsletter-archive' ), 'secondary', 'mailchimp_newsletter_archive_migrate_btn', false ); ?>
			</form>

			<button id="mcna-advanced-toggle" class="button" type="button" style="margin-top:1em;">Show Advanced Settings</button>
			<div id="mcna-advanced-section" style="display:none; margin-top:2em;">
				<?php $this->render_campaigns_preview(); ?>
				<?php $this->render_cron_status(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Handle manual sync request from the settings page.
	 */
	public function handle_manual_sync() {
		if ( isset( $_POST['mailchimp_newsletter_archive_sync'] ) && check_admin_referer( 'mailchimp_newsletter_archive_sync', 'mailchimp_newsletter_archive_sync_nonce' ) ) {
			$options = get_option( 'mailchimp_newsletter_archive_options' );
			$api_key = $options['api_key'] ?? '';
			$server_prefix = $options['server_prefix'] ?? '';
			$cache_ttl = $options['cache_ttl'] ?? 12;
			$max_campaigns = $options['max_campaigns'] ?? 50;
			// Clear the transient cache before syncing
			delete_transient( 'mailchimp_newsletter_archive_campaigns' );
			if ( ! class_exists( '\brianmoney\NewsletterArchive\MailchimpService' ) ) {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mailchimp-service.php';
			}
			$mailchimp = new \brianmoney\NewsletterArchive\MailchimpService( $api_key, $server_prefix );
			$result = $mailchimp->sync_campaigns( $cache_ttl, $max_campaigns );
			if ( is_wp_error( $result ) ) {
				echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $result->get_error_message() ) . '</p></div>';
			} else {
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Mailchimp campaigns synced successfully.', 'mailchimp-newsletter-archive' ) . '</p></div>';
			}
		}
		if ( isset( $_POST['mailchimp_newsletter_archive_migrate'] ) && check_admin_referer( 'mailchimp_newsletter_archive_migrate', 'mailchimp_newsletter_archive_migrate_nonce' ) ) {
			if ( class_exists( 'Plugin_Name' ) ) {
				Plugin_Name::migrate_campaigns_to_newsletters();
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Mailchimp campaigns imported to newsletters.', 'mailchimp-newsletter-archive' ) . '</p></div>';
			} else {
				echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Import failed: plugin class not found.', 'mailchimp-newsletter-archive' ) . '</p></div>';
			}
		}
	}

	public function field_api_key() {
		$options = get_option( 'mailchimp_newsletter_archive_options' );
		?>
		<input type="text" name="mailchimp_newsletter_archive_options[api_key]" value="<?php echo esc_attr( $options['api_key'] ?? '' ); ?>" class="regular-text" />
		<p class="description">
			<b><?php esc_html_e( 'Instructions:', 'mailchimp-newsletter-archive' ); ?></b><br>
			<?php esc_html_e( 'Log in to Mailchimp: Go to the Mailchimp website and sign in to your account.', 'mailchimp-newsletter-archive' ); ?><br>
			<?php esc_html_e( 'Access your profile: Click on your profile name or icon (usually in the bottom left or top right corner).', 'mailchimp-newsletter-archive' ); ?><br>
			<?php esc_html_e( 'Go to Extras: Click on the "Extras" menu option.', 'mailchimp-newsletter-archive' ); ?><br>
			<?php esc_html_e( 'Select API keys: Click on "API keys" from the Extras menu.', 'mailchimp-newsletter-archive' ); ?><br>
			<?php esc_html_e( 'Find or create your key: You\'ll see a list of your API keys. If you don\'t have one, click "Create a Key".', 'mailchimp-newsletter-archive' ); ?><br>
			<?php esc_html_e( 'Copy your key: Once generated, copy the API key and enter it into the API Key field.', 'mailchimp-newsletter-archive' ); ?>
		</p>
		<?php
	}

	public function field_server_prefix() {
		$options = get_option( 'mailchimp_newsletter_archive_options' );
		?>
		<input type="text" name="mailchimp_newsletter_archive_options[server_prefix]" value="<?php echo esc_attr( $options['server_prefix'] ?? '' ); ?>" class="regular-text" />
		<p class="description">
			<b><?php esc_html_e( 'Instructions:', 'mailchimp-newsletter-archive' ); ?></b><br>
			<?php esc_html_e( 'The server prefix is the part of your Mailchimp API URL before ".api.mailchimp.com" (e.g., us1, us20, etc.).', 'mailchimp-newsletter-archive' ); ?><br>
			<?php esc_html_e( 'You can find it in your API key (e.g., us1 in abc123-us1) or in your Mailchimp dashboard URL.', 'mailchimp-newsletter-archive' ); ?>
		</p>
		<?php
	}

	public function field_audience_id() {
		$options = get_option( 'mailchimp_newsletter_archive_options' );
		?>
		<input type="text" name="mailchimp_newsletter_archive_options[audience_id]" value="<?php echo esc_attr( $options['audience_id'] ?? '' ); ?>" class="regular-text" />
		<p class="description">
			<b><?php esc_html_e( 'Instructions:', 'mailchimp-newsletter-archive' ); ?></b><br>
			<?php esc_html_e( 'The Audience ID (also called List ID) identifies which Mailchimp audience to sync.', 'mailchimp-newsletter-archive' ); ?><br>
			<?php esc_html_e( 'In Mailchimp, go to Audience Dashboard > Manage Audiences > Settings > Audience name and defaults. The Audience ID is shown there.', 'mailchimp-newsletter-archive' ); ?>
		</p>
		<?php
	}

	public function field_cache_ttl() {
		$options = get_option( 'mailchimp_newsletter_archive_options' );
		?>
		<input type="number" min="1" name="mailchimp_newsletter_archive_options[cache_ttl]" value="<?php echo esc_attr( $options['cache_ttl'] ?? 12 ); ?>" class="small-text" />
		<span class="description"> <?php esc_html_e( 'Hours', 'mailchimp-newsletter-archive' ); ?> </span>
		<p class="description">
			<b><?php esc_html_e( 'Instructions:', 'mailchimp-newsletter-archive' ); ?></b><br>
			<?php esc_html_e( 'How long (in hours) Mailchimp data should be cached before refreshing. Default is 12 hours.', 'mailchimp-newsletter-archive' ); ?>
		</p>
		<?php
	}

	public function field_max_campaigns() {
		$options = get_option( 'mailchimp_newsletter_archive_options' );
		?>
		<input type="number" min="1" name="mailchimp_newsletter_archive_options[max_campaigns]" value="<?php echo esc_attr( $options['max_campaigns'] ?? 50 ); ?>" class="small-text" />
		<p class="description">
			<b><?php esc_html_e( 'Instructions:', 'mailchimp-newsletter-archive' ); ?></b><br>
			<?php esc_html_e( 'The maximum number of Mailchimp campaigns to fetch and archive. Default is 50.', 'mailchimp-newsletter-archive' ); ?>
		</p>
		<?php
	}

	public function field_import_status() {
		$options = get_option( 'mailchimp_newsletter_archive_options' );
		$statuses = array(
			'draft'   => __( 'Draft', 'mailchimp-newsletter-archive' ),
			'publish' => __( 'Published', 'mailchimp-newsletter-archive' ),
			'pending' => __( 'Pending Review', 'mailchimp-newsletter-archive' ),
			'private' => __( 'Private', 'mailchimp-newsletter-archive' ),
		);
		$current = $options['import_status'] ?? 'draft';
		?>
		<select name="mailchimp_newsletter_archive_options[import_status]">
			<?php foreach ( $statuses as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	public function field_base_url() {
		$options = get_option( 'mailchimp_newsletter_archive_options' );
		$base_url = $options['base_url'] ?? 'newsletters';
		?>
		<input type="text" name="mailchimp_newsletter_archive_options[base_url]" value="<?php echo esc_attr( $base_url ); ?>" class="regular-text" />
		<p class="description">
			<?php esc_html_e( 'This controls the base URL for the newsletter archive (e.g., /newsletters/ or /mailers/). Changing this will change all newsletter URLs. After saving, permalinks will be flushed automatically. Changing this may affect SEO and break old links.', 'mailchimp-newsletter-archive' ); ?>
		</p>
		<?php
	}

	/**
	 * Show admin notice if required settings are missing.
	 */
	public function settings_admin_notice() {
		$screen = get_current_screen();
		if ( $screen && $screen->id !== 'settings_page_mailchimp-newsletter-archive' ) {
			return;
		}
		$options = get_option( 'mailchimp_newsletter_archive_options' );
		$missing = array();
		if ( empty( $options['api_key'] ) ) {
			$missing[] = __( 'API Key', 'mailchimp-newsletter-archive' );
		}
		if ( empty( $options['server_prefix'] ) ) {
			$missing[] = __( 'Server Prefix', 'mailchimp-newsletter-archive' );
		}
		if ( empty( $options['audience_id'] ) ) {
			$missing[] = __( 'Audience ID', 'mailchimp-newsletter-archive' );
		}
		if ( ! empty( $missing ) ) {
			echo '<div class="notice notice-warning is-dismissible"><p>';
			printf(
				/* translators: %s: comma-separated list of missing fields */
				esc_html__( 'Mailchimp Newsletter Archive: Please fill in the following required settings: %s', 'mailchimp-newsletter-archive' ),
				esc_html( implode( ', ', $missing ) )
			);
			echo '</p></div>';
		}
	}

	/**
	 * Render the campaigns preview section.
	 */
	public function render_campaigns_preview() {
		$options = get_option( 'mailchimp_newsletter_archive_options' );
		$api_key = $options['api_key'] ?? '';
		$server_prefix = $options['server_prefix'] ?? '';
		$cache_ttl = $options['cache_ttl'] ?? 12;
		$max_campaigns = $options['max_campaigns'] ?? 50;
		if ( ! class_exists( '\brianmoney\NewsletterArchive\MailchimpService' ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mailchimp-service.php';
		}
		$mailchimp = new \brianmoney\NewsletterArchive\MailchimpService( $api_key, $server_prefix );
		$data = $mailchimp->get_cached_campaigns( $cache_ttl, $max_campaigns );
		if ( is_wp_error( $data ) ) {
			echo '<div class="notice notice-error"><p>' . esc_html( $data->get_error_message() ) . '</p></div>';
			return;
		}
		$campaigns = $data['campaigns'] ?? [];
		$count = count( $campaigns );
		if ( $count === 0 ) {
			echo '<h2>' . esc_html__( 'Preview Synced Campaigns', 'mailchimp-newsletter-archive' ) . '</h2>';
			echo '<p>' . esc_html__( 'No campaigns are currently cached from Mailchimp.', 'mailchimp-newsletter-archive' ) . '</p>';
			return;
		}
		$first_id = $campaigns[0]['id'] ?? '';
		$last_id = $campaigns[$count-1]['id'] ?? '';
		echo '<h2>' . esc_html__( 'Preview Synced Campaigns', 'mailchimp-newsletter-archive' ) . '</h2>';
		echo '<p>' . sprintf( esc_html__( 'Currently %d campaigns are cached from Mailchimp.', 'mailchimp-newsletter-archive' ), $count ) . '</p>';
		echo '<p>' . esc_html__( 'First Campaign ID:', 'mailchimp-newsletter-archive' ) . ' <code>' . esc_html( $first_id ) . '</code><br>';
		echo esc_html__( 'Last Campaign ID:', 'mailchimp-newsletter-archive' ) . ' <code>' . esc_html( $last_id ) . '</code></p>';
		// Debug output
		echo '<p style="color:#888;font-size:smaller;">Debug: Max Campaigns: ' . esc_html( $max_campaigns ) . '</p>';
		echo '<table class="widefat fixed striped"><thead><tr>';
		echo '<th>' . esc_html__( 'Subject', 'mailchimp-newsletter-archive' ) . '</th>';
		echo '<th>' . esc_html__( 'Campaign ID', 'mailchimp-newsletter-archive' ) . '</th>';
		echo '<th>' . esc_html__( 'Send Date', 'mailchimp-newsletter-archive' ) . '</th>';
		echo '</tr></thead><tbody>';
		foreach ( $campaigns as $c ) {
			echo '<tr>';
			echo '<td>' . esc_html( $c['settings']['subject_line'] ?? '' ) . '</td>';
			echo '<td><code>' . esc_html( $c['id'] ?? '' ) . '</code></td>';
			echo '<td>' . esc_html( $c['send_time'] ?? '' ) . '</td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	}

	public function render_cron_status() {
		echo '<h2>' . esc_html__( 'Cron Status', 'mailchimp-newsletter-archive' ) . '</h2>';
		$timestamp = wp_next_scheduled('mailchimp_newsletter_archive_cron_sync');
		if ($timestamp) {
			echo '<p>' . esc_html__('Next Mailchimp sync:', 'mailchimp-newsletter-archive') . ' <strong>' . esc_html(date('Y-m-d H:i:s', $timestamp)) . '</strong></p>';
		} else {
			echo '<p style="color:red;">' . esc_html__('Mailchimp sync cron is NOT scheduled.', 'mailchimp-newsletter-archive') . '</p>';
		}
		?>
		<form method="post">
			<?php wp_nonce_field( 'mailchimp_newsletter_archive_cron', 'mailchimp_newsletter_archive_cron_nonce' ); ?>
			<input type="hidden" name="mailchimp_newsletter_archive_run_cron" value="1" />
			<?php submit_button( __( 'Run Cron Now', 'mailchimp-newsletter-archive' ), 'secondary', 'mailchimp_newsletter_archive_run_cron_btn', false ); ?>
		</form>
		<?php
		if ( isset( $_POST['mailchimp_newsletter_archive_run_cron'] ) && check_admin_referer( 'mailchimp_newsletter_archive_cron', 'mailchimp_newsletter_archive_cron_nonce' ) ) {
			do_action('mailchimp_newsletter_archive_cron_sync');
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Mailchimp sync cron event triggered.', 'mailchimp-newsletter-archive' ) . '</p></div>';
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin-name-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );

	}

}
