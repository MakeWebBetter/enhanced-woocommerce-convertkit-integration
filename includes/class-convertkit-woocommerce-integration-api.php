<?php

/**
 * All remote requests for ConvertKit API.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Enhanced_Woocommerce_ConvertKit_Integration
 * @subpackage Enhanced_Woocommerce_ConvertKit_Integration/includes
 */

/**
 * All remote requests for ConvertKit API.
 *
 * This class defines all functions for interacting with ConvertKit API.
 *
 * @since      1.0.0
 * @package    Enhanced_Woocommerce_ConvertKit_Integration
 * @subpackage Enhanced_Woocommerce_ConvertKit_Integration/includes
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Convertkit_Woocommerce_Integration_Api {

	/**
	 * Base API Url for ConvertKit API.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $base_api_url    The Base Url that the API makes call to.
	 */
	protected $base_api_url;

	/**
	 * API Secret for ConvertKit API.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $api_secret_key    API Secret required to authenticate API calls.
	 */
	protected $api_secret_key;

	/**
	 * Initialize the Api.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $api_secret ) {

		if ( defined( 'MWB_CWI_API_URL' ) ) {

			$this->base_api_url = MWB_CWI_API_URL;
		} else {

			$this->base_api_url = 'https://api.convertkit.com/v3';
		}

		$this->api_secret_key = $api_secret;

	}

	/**
	 * Using wordpress remote request for ConvertKit API.
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
	protected function remote_request( $url = '', $method = 'GET', $data = '' ) {

		if ( empty( $url ) || empty( $data ) ) {

			$error['message'] = 'Empty Url or Empty Data';
			$error['cwi_response_code'] = '405';

			return $error;
		}

		$args = array(
			'method'      => $method,
			'body'        => $data,
			'sslverify'   => false,
		);

		$response = wp_remote_request( $url, $args );

		$result = wp_remote_retrieve_body( $response );

		$response_code = wp_remote_retrieve_response_code( $response );

		$result = json_decode( $result, true );

		$result['cwi_response_code'] = $response_code;

		return $result;

	}

	/**
	 * Validate ConvertKit API Secret.
	 *
	 * @since    1.0.0
	 */
	public function validate_api_secret() {

		$endpoint = '/account';

		$url = $this->base_api_url . $endpoint;

		$method = 'GET';

		$api_secret = $this->api_secret_key;

		$data = array(
			'api_secret' => $api_secret,
		);

		$result = $this->remote_request( $url, $method, $data );

		if ( '200' == $result['cwi_response_code'] ) {

			update_option( 'mwb_cwi_api_secret_validated', 'true' );

			$name = ! empty( $result['name'] ) ? $result['name'] : '';
			$email_address = ! empty( $result['primary_email_address'] ) ? $result['primary_email_address'] : '';

			$account_info = array(
				'name' => $name,
				'email_address' => $email_address,
			);

			update_option( 'mwb_cwi_api_secret_account_info', $account_info );

		} else {

			update_option( 'mwb_cwi_api_secret_validated', 'false' );
		}

	}

	/**
	 * Create Custom Fields in ConvertKit.
	 *
	 * @since    1.0.0
	 */
	public function create_custom_fields() {

		$endpoint = '/custom_fields';

		$url = $this->base_api_url . $endpoint;

		$method = 'POST';

		$api_secret = $this->api_secret_key;

		$properties_instance = new Convertkit_Woocommerce_Integration_Properties();

		$properties = $properties_instance->get_properties();

		$logging = false;

		if ( $this->logging_enabled() ) {

			$logging = true;
		}

		foreach ( $properties as $key => $value ) {

			$value = ucwords( str_replace( '_', ' ', $value ) );

			$data = array(
				'api_secret' => $api_secret,
				'label' => $value,
			);

			$result = $this->remote_request( $url, $method, $data );

			if ( $logging ) {

				$process = 'Creating Custom Field';

				$success_message = ' created successfully.';

				$process = ! empty( $value ) ? $process . ' \'' . $value . '\'.' : $process . '.';

				$success_message = ! empty( $result['key'] ) ? $result['key'] . $success_message : 'Custom Field' . $success_message;

				$this->create_log( $result, $url, $process, $success_message );

			}

			if ( '401' == $result['cwi_response_code'] || '405' == $result['cwi_response_code'] ) {

				update_option( 'mwb_cwi_custom_fields_creation', 'false' );

				return;
			}
		}

		update_option( 'mwb_cwi_custom_fields_creation', 'true' );

	}

	/**
	 * Create Store Tag in ConvertKit.
	 *
	 * @since    1.0.0
	 */
	public function create_tag() {

		$endpoint = '/tags';

		$url = $this->base_api_url . $endpoint;

		$method = 'POST';

		$api_secret = $this->api_secret_key;

		if ( is_multisite() ) {

			global $blog_id;

			$current_blog_details = get_blog_details( array( 'blog_id' => $blog_id ) );

			$blog_name = ! empty( $current_blog_details->blogname ) ? $current_blog_details->blogname : '';
		} else {

			$blog_name = ! empty( get_bloginfo( 'name' ) ) ? get_bloginfo( 'name' ) : '';
		}

		$tag_name = ! empty( $blog_name ) ? $blog_name . ' ' . current_time( 'd M Y' ) : 'My Woo Store ' . current_time( 'd M Y' );

		$data = array(
			'api_secret' => $api_secret,
			'tag' => array(
				'name' => $tag_name,
			),
		);

		$result = $this->remote_request( $url, $method, $data );

		if ( $this->logging_enabled() ) {

			$process = 'Creating Tag \'' . $tag_name . '\'.';

			$success_message = ! empty( $result['name'] ) ? $result['name'] . ' tag created successfully.' : 'Tag created successfully.';

			$this->create_log( $result, $url, $process, $success_message );

		}

		if ( '201' == $result['cwi_response_code'] ) {

			update_option( 'mwb_cwi_tag_created', 'true' );

			$id = ! empty( $result['id'] ) ? $result['id'] : '';

			$tag_info = array(
				'id' => $id,
				'name' => $tag_name,
			);

			update_option( 'mwb_cwi_tag_info', $tag_info );

		} else {

			update_option( 'mwb_cwi_tag_created', 'false' );
		}

	}

	/**
	 * Upload subscribers to Store Tag in ConvertKit.
	 *
	 * @since    1.0.0
	 */
	public function upload_subscriber( $tag_id = '', $user_id = 0 ) {

		$user_id = ! empty( $user_id ) ? intval( $user_id ) : 0;

		if ( empty( $user_id ) || empty( $tag_id ) ) {

			return;
		}

		$user_object = get_user_by( 'id', $user_id );

		if ( empty( $user_object ) ) {

			return;
		}

		$user_email = ! empty( $user_object->data->user_email ) ? $user_object->data->user_email : '';

		if ( empty( $user_email ) ) {

			return;
		}

		$user_name = ! empty( $user_object->data->display_name ) ? $user_object->data->display_name : '';

		$properties_instance = new Convertkit_Woocommerce_Integration_Properties( $user_id );

		$properties = $properties_instance->get_properties_values();

		$endpoint = '/tags/' . $tag_id . '/subscribe';

		$url = $this->base_api_url . $endpoint;

		$method = 'POST';

		$api_secret = $this->api_secret_key;

		$data = array(
			'api_secret' => $api_secret,
			'email' => $user_email,
			'first_name' => $user_name,
			'fields' => $properties,
		);

		$result = $this->remote_request( $url, $method, $data );

		if ( $this->logging_enabled() ) {

			$process = 'Uploading Subscriber : ' . $user_id;

			$success_message = 'Subscriber uploaded successfully.';

			$this->create_log( $result, $url, $process, $success_message );

		}

		if ( '200' == $result['cwi_response_code'] && ! empty( $result['subscription']['subscriber']['id'] ) ) {

			$subscriber_id = $result['subscription']['subscriber']['id'];

			update_user_meta( $user_id, 'mwb_cwi_user_uploaded', 'true' );
			update_user_meta( $user_id, 'mwb_cwi_user_sub_id', $subscriber_id );
			update_user_meta( $user_id, 'mwb_cwi_user_tag_id', $tag_id );

		} else {

			update_user_meta( $user_id, 'mwb_cwi_user_uploaded', 'false' );
		}

	}

	/**
	 * Update subscribers by subscriber Id in ConvertKit.
	 *
	 * @since    1.0.0
	 */
	public function update_subscriber( $subscriber_id = '', $user_id = 0 ) {

		$user_id = ! empty( $user_id ) ? intval( $user_id ) : 0;

		if ( empty( $user_id ) || empty( $subscriber_id ) ) {

			return;
		}

		$user_object = get_user_by( 'id', $user_id );

		if ( empty( $user_object ) ) {

			return;
		}

		$user_email = ! empty( $user_object->data->user_email ) ? $user_object->data->user_email : '';

		if ( empty( $user_email ) ) {

			return;
		}

		$user_name = ! empty( $user_object->data->display_name ) ? $user_object->data->display_name : '';

		$properties_instance = new Convertkit_Woocommerce_Integration_Properties( $user_id );

		$properties = $properties_instance->get_properties_values();

		$endpoint = '/subscribers/' . $subscriber_id;

		$url = $this->base_api_url . $endpoint;

		$method = 'PUT';

		$api_secret = $this->api_secret_key;

		$data = array(
			'api_secret' => $api_secret,
			'email' => $user_email,
			'first_name' => $user_name,
			'fields' => $properties,
		);

		$result = $this->remote_request( $url, $method, $data );

		if ( $this->logging_enabled() ) {

			$process = 'Updating Subscriber : ' . $user_id;

			$success_message = 'Subscriber updated successfully.';

			$this->create_log( $result, $url, $process, $success_message );

		}

	}

	/**
	 * Check If logging has been enabled.
	 *
	 * @since    1.0.0
	 */
	public function logging_enabled() {

		$log_enabled = get_option( 'mwb_cwi_plug_enable_log', 'no' );

		if ( 'yes' === $log_enabled ) {

			return true;
		} else {

			return false;
		}
	}

	/**
	 * Create Log for ConvertKit.
	 *
	 * @since    1.0.0
	 */
	public function create_log( $result, $url, $process, $success_message ) {

		if ( ! defined( 'WC_LOG_DIR' ) ) {

			return;
		}

		$response_code = ! empty( $result['cwi_response_code'] ) ? $result['cwi_response_code'] : 'Response Code Not Set.';

		$response_message = ! empty( $result['message'] ) ? $result['message'] : 'Response Message Not Set.';

		if ( '200' == $response_code || '201' == $response_code ) {

			$response_message = ! empty( $success_message ) ? $success_message : 'Response Success Message Not Set.';
		}

		$log_file = WC_LOG_DIR . 'MWB-ConvertKit-Woocommerce.log';

		if ( ! is_dir( $log_file ) ) {

			// phpcs:disable
			@fopen( $log_file, 'a' );
			// phpcs:enable
		}

		$user_website = ! empty( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : esc_html__( 'Website N/A', 'enhanced-woocommerce-convertkit-integration' );

		$log  = 'Website: ' . $user_website . PHP_EOL .
				'Time: ' . current_time( 'F j, Y  g:i a' ) . PHP_EOL .
				'Process: ' . $process . PHP_EOL .
				'URL: ' . $url . PHP_EOL .
				'Response: ' . $response_message . PHP_EOL .
				'Response Code: ' . $response_code . PHP_EOL .
				'-----------------------------------' . PHP_EOL;

		// phpcs:disable	
		file_put_contents( $log_file, $log, FILE_APPEND );
		// phpcs:enable

	}

}
