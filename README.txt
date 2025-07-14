=== Mailchimp Newsletter Archive ===
Contributors: brianmoney
Donate link: https://aspereo.com
Tags: mailchimp, newsletter, archive, email marketing, campaigns, seo
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Syncs Mailchimp campaigns into a newsletter Custom Post Type with SEO-friendly URLs and archive pages.

== Description ==

**Mailchimp Newsletter Archive** automatically syncs your Mailchimp email campaigns into WordPress as a custom post type, creating a beautiful, SEO-optimized newsletter archive for your website.

## Key Features

* **Automatic Sync**: Connects to Mailchimp Marketing API to fetch campaigns
* **SEO-Friendly URLs**: Creates clean URLs like `/newsletters/2024/01/my-newsletter/`
* **Custom Post Type**: Organizes newsletters separately from blog posts
* **Archive Pages**: Beautiful paginated archive at `/newsletters/`
* **Shortcode Support**: Display newsletters anywhere with `[mailchimp_archive]`
* **Caching**: Built-in caching for optimal performance
* **Manual & Automatic Sync**: Sync on-demand or set up nightly cron jobs

## Perfect For

* Marketing agencies wanting to showcase client newsletters
* Businesses wanting to archive email campaigns on their website
* Content creators needing SEO-friendly newsletter archives
* Anyone wanting to preserve email marketing content

## Setup

1. Install and activate the plugin
2. Go to **Settings > Mailchimp Newsletter Archive**
3. Enter your Mailchimp API key and server prefix
4. Configure your audience ID and archive settings
5. Click "Sync Now" to import your campaigns

## Requirements

* WordPress 5.0 or higher
* PHP 7.4 or higher
* Mailchimp Marketing API access
* Valid Mailchimp API key

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/mailchimp-newsletter-archive` directory, or install through WordPress admin
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Settings > Mailchimp Newsletter Archive** to configure your Mailchimp API settings
4. Enter your API key, server prefix, and audience ID
5. Click "Sync Now" to import your campaigns
6. Your newsletters will be available at `/newsletters/` and individual posts at `/newsletters/YYYY/MM/post-name/`

== Frequently Asked Questions ==

= How do I get my Mailchimp API key? =

1. Log into your Mailchimp account
2. Go to Account > Extras > API Keys
3. Create a new API key
4. Copy the key and your server prefix (e.g., "us1", "us2")

= What's my server prefix? =

Your server prefix is the part before the dot in your Mailchimp URL. For example, if your Mailchimp URL is `https://us1.admin.mailchimp.com`, your server prefix is `us1`.

= How often does the plugin sync? =

By default, the plugin syncs nightly via WordPress cron. You can also manually sync anytime from the admin settings page.

= Can I customize the archive page design? =

Yes! The plugin includes customizable templates and CSS. You can also use the `[mailchimp_archive]` shortcode to display newsletters anywhere on your site.

= Will this affect my existing blog posts? =

No, newsletters are stored as a separate custom post type and won't interfere with your regular blog posts.

== Screenshots ==

1. Admin settings page showing Mailchimp API configuration
2. Newsletter archive page displaying imported campaigns
3. Individual newsletter post with SEO-optimized layout
4. Shortcode example showing newsletters embedded in a page

== Changelog ==

= 1.0.0 =
* Initial release
* Mailchimp API integration
* Custom post type for newsletters
* SEO-friendly URL structure
* Archive pages and shortcode support
* Admin settings page
* Automatic and manual sync options
* Caching system for performance

== Upgrade Notice ==

= 1.0.0 =
Initial release of Mailchimp Newsletter Archive plugin.

== Support ==

For support, feature requests, or bug reports, please visit [our GitHub repository](https://github.com/brianmoney/mailchimp-newsletter-archive).

== Credits ==

Developed by [Brian Money](https://aspereo.com) for the WordPress community.