<?php
namespace brianmoney\NewsletterArchive;

/**
 * Handles Mailchimp API requests for the Newsletter Archive plugin.
 */
class MailchimpService {
	protected $api_key;
	protected $server_prefix;

	public function __construct( $api_key, $server_prefix ) {
		$this->api_key = $api_key;
		$this->server_prefix = $server_prefix;
	}

	/**
	 * Fetch campaigns from the Mailchimp /campaigns endpoint, including HTML content for each.
	 *
	 * @param int $max_campaigns Number of campaigns to fetch.
	 * @return array|\WP_Error Decoded response data or WP_Error on failure.
	 */
	public function fetch_campaigns( $max_campaigns = 10 ) {
		if ( empty( $this->api_key ) || empty( $this->server_prefix ) ) {
			return new \WP_Error( 'mailchimp_missing_credentials', __( 'Mailchimp API key or server prefix is missing.', 'mailchimp-newsletter-archive' ) );
		}

		$all_campaigns = array();
		$count = min( 1000, absint( $max_campaigns ) );
		$offset = 0;
		while ( count( $all_campaigns ) < $max_campaigns ) {
			$url = sprintf( 'https://%s.api.mailchimp.com/3.0/campaigns?count=%d&offset=%d', $this->server_prefix, $count, $offset );
			$args = array(
				'headers' => array(
					'Authorization' => 'apikey ' . $this->api_key,
					'Accept'        => 'application/json',
				),
				'timeout' => 20,
			);
			$response = wp_remote_get( $url, $args );
			if ( is_wp_error( $response ) ) {
				return $response;
			}
			$code = wp_remote_retrieve_response_code( $response );
			$body = wp_remote_retrieve_body( $response );
			if ( $code !== 200 ) {
				return new \WP_Error( 'mailchimp_api_error', sprintf( __( 'Mailchimp API error: %s', 'mailchimp-newsletter-archive' ), $body ), array( 'response' => $response ) );
			}
			$data = json_decode( $body, true );
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				return new \WP_Error( 'mailchimp_json_error', __( 'Failed to decode Mailchimp API response.', 'mailchimp-newsletter-archive' ) );
			}
			$batch = $data['campaigns'] ?? [];
			if ( empty( $batch ) ) {
				break;
			}
			$all_campaigns = array_merge( $all_campaigns, $batch );
			if ( count( $batch ) < $count ) {
				break; // No more campaigns
			}
			$offset += $count;
		}
		// Trim to max_campaigns
		$all_campaigns = array_slice( $all_campaigns, 0, $max_campaigns );

		// Fetch HTML content for each campaign, with a short delay to avoid rate limiting
		foreach ( $all_campaigns as &$campaign ) {
			if ( empty( $campaign['id'] ) ) {
				continue;
			}
			$content_url = sprintf( 'https://%s.api.mailchimp.com/3.0/campaigns/%s/content', $this->server_prefix, $campaign['id'] );
			$content_response = wp_remote_get( $content_url, $args );
			if ( ! is_wp_error( $content_response ) && wp_remote_retrieve_response_code( $content_response ) === 200 ) {
				$content_data = json_decode( wp_remote_retrieve_body( $content_response ), true );
				if ( isset( $content_data['html'] ) ) {
					$campaign['content']['html'] = $content_data['html'];
				}
			}
			usleep( 300000 ); // 0.3 second delay between requests
		}
		unset( $campaign );

		return array( 'campaigns' => $all_campaigns );
	}

	/**
	 * Get campaigns from cache or fetch and cache them if not present or expired.
	 *
	 * @param int $cache_ttl Cache time-to-live in hours.
	 * @param int $max_campaigns Number of campaigns to fetch.
	 * @return array|\WP_Error
	 */
	public function get_cached_campaigns( $cache_ttl = 12, $max_campaigns = 10 ) {
		$transient_key = 'mailchimp_newsletter_archive_campaigns';
		$cached = get_transient( $transient_key );
		if ( false !== $cached ) {
			return $cached;
		}
		return $this->sync_campaigns( $cache_ttl, $max_campaigns );
	}

	/**
	 * Fetch campaigns from Mailchimp and cache them.
	 *
	 * @param int $cache_ttl Cache time-to-live in hours.
	 * @param int $max_campaigns Number of campaigns to fetch.
	 * @return array|\WP_Error
	 */
	public function sync_campaigns( $cache_ttl = 12, $max_campaigns = 10 ) {
		$data = $this->fetch_campaigns( $max_campaigns );
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		$transient_key = 'mailchimp_newsletter_archive_campaigns';
		set_transient( $transient_key, $data, HOUR_IN_SECONDS * absint( $cache_ttl ) );
		return $data;
	}
} 