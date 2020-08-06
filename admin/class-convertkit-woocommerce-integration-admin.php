<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Enhanced_Woocommerce_ConvertKit_Integration
 * @subpackage Enhanced_Woocommerce_ConvertKit_Integration/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Enhanced_Woocommerce_ConvertKit_Integration
 * @subpackage Enhanced_Woocommerce_ConvertKit_Integration/admin
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Convertkit_Woocommerce_Integration_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook ) {

		// Enqueue styles only on this plugin's menu page.
		if ( 'toplevel_page_convertkit_woocommerce_integration_menu' != $hook ) {

			return;
		}

		wp_enqueue_style( $this->plugin_name, CONVERTKIT_WOOCOMMERCE_INTEGRATION_DIR_URL . 'admin/css/convertkit-woocommerce-integration-admin.css', array(), $this->version, 'all' );

		// Enqueue style for using WooCommerce Tooltip.
		wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {

		// Enqueue scripts only on this plugin's menu page.
		if ( 'toplevel_page_convertkit_woocommerce_integration_menu' != $hook ) {

			return;
		}

		wp_enqueue_script( $this->plugin_name . 'admin-js', CONVERTKIT_WOOCOMMERCE_INTEGRATION_DIR_URL . 'admin/js/convertkit-woocommerce-integration-admin.js', array( 'jquery' ), $this->version, false );

		wp_localize_script(
			$this->plugin_name . 'admin-js',
			'ajax_object',
			array(
				'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
				'reloadurl' => esc_url( admin_url( 'admin.php?page=convertkit_woocommerce_integration_menu' ) ),
				'ajax_nonce' => wp_create_nonce( 'convertkit-woocommerce-integration-ajax-nonce-action' ),
			)
		);

		// Enqueue and Localize script for using WooCommerce Tooltip.

		wp_enqueue_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip', 'wc-enhanced-select' ), WC_VERSION );

		$params = array(
			'strings' => '',
			'urls' => '',
		);

		wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );

	}

	/**
	 * Adding settings menu for ConvertKit WooCommerce Integration.
	 *
	 * @since    1.0.0
	 */
	public function add_options_page() {

		add_menu_page(
			esc_html__( 'ConvertKit - Woo', 'enhanced-woocommerce-convertkit-integration' ),
			esc_html__( 'ConvertKit - Woo', 'enhanced-woocommerce-convertkit-integration' ),
			'manage_options',
			'convertkit_woocommerce_integration_menu',
			array( $this, 'options_menu_html' ),
			'dashicons-email-alt',
			85
		);
	}

	/**
	 * ConvertKit WooCommerce Integration admin menu page.
	 *
	 * @since    1.0.0
	 */
	public function options_menu_html() {

		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {

			return;
		}

		require_once( CONVERTKIT_WOOCOMMERCE_INTEGRATION_DIR_PATH . 'admin/partials/convertkit-woocommerce-integration-admin-display.php' );
	}

	/**
	 * Using Settings API for settings menu.
	 *
	 * @since    1.0.0
	 */
	public function settings_api() {

		register_setting( 'convertkit_woocommerce_integration_ck_menu', 'mwb_cwi_plug_sync_enable' );

		register_setting( 'convertkit_woocommerce_integration_ck_menu', 'mwb_cwi_plug_enable_log' );

		add_settings_section(
			'convertkit_woocommerce_integration_ck_menu_sec',
			null,
			null,
			'convertkit_woocommerce_integration_ck_menu'
		);

		add_settings_field(
			'convertkit_woocommerce_integration_enable',
			esc_html__( 'Enable', 'enhanced-woocommerce-convertkit-integration' ),
			array( $this, 'enable_syncing_cb' ),
			'convertkit_woocommerce_integration_ck_menu',
			'convertkit_woocommerce_integration_ck_menu_sec'
		);

		$api_secret = get_option( 'mwb_cwi_plug_api_secret', '' );

		$api_secret_validated = get_option( 'mwb_cwi_api_secret_validated', 'false' );

		if ( empty( $api_secret ) || 'false' == $api_secret_validated ) {

			register_setting( 'convertkit_woocommerce_integration_ck_menu', 'mwb_cwi_plug_api_secret' );

			add_settings_field(
				'convertkit_woocommerce_integration_api_secret',
				esc_html__( 'API Secret', 'enhanced-woocommerce-convertkit-integration' ),
				array( $this, 'api_secret_cb' ),
				'convertkit_woocommerce_integration_ck_menu',
				'convertkit_woocommerce_integration_ck_menu_sec'
			);
		}

		add_settings_field(
			'convertkit_woocommerce_integration_enable_log',
			esc_html__( 'Debugging', 'enhanced-woocommerce-convertkit-integration' ),
			array( $this, 'enable_log_cb' ),
			'convertkit_woocommerce_integration_ck_menu',
			'convertkit_woocommerce_integration_ck_menu_sec'
		);

	}

	/**
	 * Callback for Enable Plugin option.
	 *
	 * @since    1.0.0
	 */
	public function enable_syncing_cb() {

		?>

		<div class="convertkit-woocommerce-integration-option-sec">

			<?php

			$tip_description = esc_html__( 'Enable ConvertKit Woocommerce Syncing. New users and users with updated info will be synced to ConvertKit after integration is successful.', 'enhanced-woocommerce-convertkit-integration' );

			// phpcs:disable
			echo wc_help_tip( $tip_description );
			// phpcs:enable

			?>

			<label for="mwb_cwi_plug_sync_enable">
				<input type="checkbox" name="mwb_cwi_plug_sync_enable" value="yes" <?php checked( 'yes', get_option( 'mwb_cwi_plug_sync_enable', 'yes' ) ); ?> >
				<?php esc_html_e( 'Enable Syncing.', 'enhanced-woocommerce-convertkit-integration' ); ?>	
			</label>
		
		</div>

		<?php
	}

	/**
	 * Callback for ConvertKit API Secret.
	 *
	 * @since    1.0.0
	 */
	public function api_secret_cb() {

		$api_secret = get_option( 'mwb_cwi_plug_api_secret', '' );

		?>

		<div class="convertkit-woocommerce-integration-option-sec">

			<?php

			$tip_description = esc_html__( 'Enter ConvertKit API Secret. Make sure it\'s your appropriate Convertkit account\'s API Secret. After successful validation you won\'t be able to change it again.', 'enhanced-woocommerce-convertkit-integration' );

			// phpcs:disable
			echo wc_help_tip( $tip_description );
			// phpcs:enable

			?>

			<input type="text" required="" name="mwb_cwi_plug_api_secret" value="<?php echo esc_html( $api_secret ); ?>">

			<?php printf( '<p class="description">%s<a target="_blank" href="%s" >%s</a>%s</p>', esc_html__( 'To retrieve your ConvetKit API Secret, click ', 'enhanced-woocommerce-convertkit-integration' ), 'https://app.convertkit.com/account/edit', esc_html__( 'here', 'enhanced-woocommerce-convertkit-integration' ), esc_html__( '.', 'enhanced-woocommerce-convertkit-integration' ) ); ?>
			
		</div>

		<?php
	}

	/**
	 * Callback for Enable Plugin option.
	 *
	 * @since    1.0.0
	 */
	public function enable_log_cb() {

		?>

		<div class="convertkit-woocommerce-integration-option-sec">

			<?php

			$tip_description = esc_html__( 'Enable Logging of data for Debugging.', 'enhanced-woocommerce-convertkit-integration' );

			// phpcs:disable
			echo wc_help_tip( $tip_description );
			// phpcs:enable

			?>

			<label for="mwb_cwi_plug_enable_log">
				<input type="checkbox" name="mwb_cwi_plug_enable_log" value="yes" <?php checked( 'yes', get_option( 'mwb_cwi_plug_enable_log', 'no' ) ); ?> >
				<?php esc_html_e( 'Enable Logging of data.', 'enhanced-woocommerce-convertkit-integration' ); ?>	
			</label>

			<?php printf( '<p class="description">%s<a href="%s" >%s</a>%s</p>', esc_html__( 'To view the log file click ', 'enhanced-woocommerce-convertkit-integration' ), esc_url( admin_url( 'admin.php?page=wc-status&tab=logs' ) ), esc_html__( 'here', 'enhanced-woocommerce-convertkit-integration' ), esc_html__( ' and select \'MWB-ConvertKit-Woocommerce.log\'.', 'enhanced-woocommerce-convertkit-integration' ) ); ?>
		
		</div>

		<?php
	}

	/**
	 * Handle Convertkit Integration.
	 *
	 * @since    1.0.0
	 */
	public function integration_handle() {

		// First check the nonce, if it fails the function will break.
		check_ajax_referer( 'convertkit-woocommerce-integration-ajax-nonce-action', 'convertkit-woocommerce-integration-ajax-nonce' );

		$api_secret = get_option( 'mwb_cwi_plug_api_secret', '' );

		$api_instance = new Convertkit_Woocommerce_Integration_Api( $api_secret );

		// Create Custom Fields in ConvertKit.

		$custom_fields_creation = get_option( 'mwb_cwi_custom_fields_creation', 'false' );

		if ( 'false' === $custom_fields_creation ) {

			$api_instance->create_custom_fields();
		}

		// Create Store Tag in ConvertKit.

		$tag_created = get_option( 'mwb_cwi_tag_created', 'false' );

		if ( 'false' === $tag_created ) {

			$api_instance->create_tag();
		}

		// Sending Response according to the way integration took place.

		$custom_fields_creation = get_option( 'mwb_cwi_custom_fields_creation', 'false' );

		if ( 'true' === $custom_fields_creation ) {

			$handle_array['custom_fields_status'] = 'true';
			$handle_array['custom_fields_message'] = esc_html__( 'Custom Fields Created Sucessfully.', 'enhanced-woocommerce-convertkit-integration' );

		} else {

			$handle_array['custom_fields_status'] = 'false';
			$handle_array['custom_fields_message'] = esc_html__( 'Couldn\'t create Custom Fields. Api Secret Error. Contact MakeWebBetter support.', 'enhanced-woocommerce-convertkit-integration' );
		}

		$tag_created = get_option( 'mwb_cwi_tag_created', 'false' );

		if ( 'true' === $tag_created ) {

			$tag_info = get_option( 'mwb_cwi_tag_info', array() );

			$tag_info_name = $tag_info['name'];

			$handle_array['tag_created_status'] = 'true';
			$handle_array['tag_created_message'] = $tag_info['name'] . esc_html__( ' Tag Created Sucessfully. Your Store Customers will be uploaded to this Tag in ConvertKit as Subscribers.', 'enhanced-woocommerce-convertkit-integration' );
		} else {

			$handle_array['tag_created_status'] = 'false';
			$handle_array['tag_created_message'] = esc_html__( 'Couldn\'t create your Store Tag. Contact MakeWebBetter support.', 'enhanced-woocommerce-convertkit-integration' );
		}

		echo wp_json_encode( $handle_array );

		wp_die();
	}

	/**
	 * Fires on Save changes submit.
	 *
	 * @since    1.0.0
	 */
	public function submit_click() {

		if ( isset( $_POST['mwb_cwi_nonce_name'] ) && check_admin_referer( 'mwb_cwi_creation_nonce', 'mwb_cwi_nonce_name' ) && isset( $_POST['mwb_cwi_submit'] ) && isset( $_POST['mwb_cwi_plug_api_secret'] ) ) {

			$api_secret_db = get_option( 'mwb_cwi_plug_api_secret', 'not_set' );

			$api_secret = ! empty( $_POST['mwb_cwi_plug_api_secret'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_cwi_plug_api_secret'] ) ) : '';

			if ( $api_secret_db != $api_secret ) {

				$api_instance = new Convertkit_Woocommerce_Integration_Api( $api_secret );

				$result = $api_instance->validate_api_secret();
			}
		}

	}

	/**
	 * Update user meta as soon as User data is updated.
	 *
	 * @since    1.0.0
	 * @param      int $user_id       User Id.
	 */
	public function user_updated( $user_id ) {

		update_user_meta( $user_id, 'mwb_cwi_user_data_change', 'yes' );

	}

	/**
	 * Update user meta as user Order status is changed.
	 *
	 * @since    1.0.0
	 * @param      int $order_id       Order Id.
	 */
	public function user_order_updated( $order_id ) {

		if ( ! empty( $order_id ) ) {

			$user_id = (int) get_post_meta( $order_id, '_customer_user', true );

			if ( $user_id != 0 && $user_id > 0 ) {

				update_user_meta( $user_id, 'mwb_cwi_user_data_change', 'yes' );
			}
		}

	}

	/**
	 * Add custom cron recurrence time interval.
	 *
	 * @since    1.0.0
	 * @param       array $schedules       Array of cron Schedule times for recurrence.
	 */
	public function set_cron_schedule_time( $schedules ) {

		if ( ! isset( $schedules['mwb_cwi_five_minutes'] ) ) {

			$schedules['mwb_cwi_five_minutes'] = array(
				'interval' => 5 * 60,
				'display' => esc_html__( 'Once every 5 minutes', 'enhanced-woocommerce-convertkit-integration' ),
			);
		}

		return $schedules;
	}


	/**
	 * Cron schedule fire Event for User update.
	 *
	 * @since    1.0.0
	 */
	public function cron_fire_event() {

		$custom_fields_creation = get_option( 'mwb_cwi_custom_fields_creation', 'false' );

		$tag_created = get_option( 'mwb_cwi_tag_created', 'false' );

		if ( 'true' === $custom_fields_creation && 'true' === $tag_created ) {

			$args['fields'] = 'ID';

			$args['meta_query'] = array(

				array(
					'key'       => 'mwb_cwi_user_data_change',
					'value'     => 'yes',
					'compare'   => '==',
				),
			);

			$updated_users = get_users( $args );

			$updated_users = apply_filters( 'mwb_cwi_updated_users', $updated_users );

			if ( ! empty( $updated_users ) && is_array( $updated_users ) && count( $updated_users ) ) {

				foreach ( $updated_users as $key => $user_id ) {

					// Update no more than 99 (0 to 98 as it will break on 99) users once.
					if ( 99 <= $key ) {

						break;
					}

					$api_secret = get_option( 'mwb_cwi_plug_api_secret', '' );

					if ( empty( $api_secret ) ) {

						return;
					}

					$api_instance = new Convertkit_Woocommerce_Integration_Api( $api_secret );

					$uploaded = get_user_meta( $user_id, 'mwb_cwi_user_uploaded', true );

					if ( 'true' === $uploaded ) {

						$subscriber_id = get_user_meta( $user_id, 'mwb_cwi_user_sub_id', true );

						$api_instance->update_subscriber( $subscriber_id, $user_id );
					} else {

						$tag_info = get_option( 'mwb_cwi_tag_info', array() );

						$tag_id = $tag_info['id'];

						$api_instance->upload_subscriber( $tag_id, $user_id );
					}

					update_user_meta( $user_id, 'mwb_cwi_user_data_change', 'no' );
				}
			}
		}
	}

	/**
	 * GDPR privacy policy for ConvertKit.
	 *
	 * @since    1.0.0
	 */
	public function privacy_policy() {

		$content = '<h2>' . esc_html__( 'We send your email and Order data to ConvertKit.', 'enhanced-woocommerce-convertkit-integration' ) . '</h2>';

		$content .= '<p>' . esc_html__( 'ConvertKit is an leading email marketing platform where we use your data accordingly.', 'enhanced-woocommerce-convertkit-integration' ) . '</p>';

		$content .= '<p>' . esc_html__( 'Please see the ', 'enhanced-woocommerce-convertkit-integration' ) . '<a href="https://convertkit.com/privacy/" target="_blank" >' . esc_html__( 'ConvertKit Privacy Policy', 'enhanced-woocommerce-convertkit-integration' ) . '</a>' . esc_html__( ' for more details.', 'enhanced-woocommerce-convertkit-integration' ) . '</p>';

		wp_add_privacy_policy_content( esc_html__( 'Integration with ConvertKit for WooCommerce', 'enhanced-woocommerce-convertkit-integration' ), $content );
	}


}
