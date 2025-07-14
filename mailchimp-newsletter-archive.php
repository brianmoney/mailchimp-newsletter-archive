<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/brianmoney/mailchimp-newsletter-archive
 * @since             1.0.0
 * @package           brianmoney\NewsletterArchive
 *
 * @wordpress-plugin
 * Plugin Name:       Mailchimp Newsletter Archive
 * Plugin URI:        https://github.com/brianmoney/mailchimp-newsletter-archive
 * Description:       Syncs Mailchimp campaigns into a newsletter Custom Post Type and exposes an SEO-friendly archive at /newsletters/.
 * Version:           1.0.0
 * Author:            Brian Money
 * Author URI:        https://aspereo.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mailchimp-newsletter-archive
 * Domain Path:       /languages
 * Requires at least: 5.0
 * Tested up to:      6.4
 * Requires PHP:      7.4
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MAILCHIMP_NEWSLETTER_ARCHIVE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_mailchimp_newsletter_archive() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin-name-activator.php';
	Plugin_Name_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_mailchimp_newsletter_archive() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin-name-deactivator.php';
	Plugin_Name_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mailchimp_newsletter_archive' );
register_deactivation_hook( __FILE__, 'deactivate_mailchimp_newsletter_archive' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-plugin-name.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mailchimp_newsletter_archive() {

	$plugin = new Plugin_Name();
	$plugin->run();

}
run_mailchimp_newsletter_archive();

add_action('admin_init', array('Plugin_Name', 'maybe_flush_rewrite_on_base_url_change'));
