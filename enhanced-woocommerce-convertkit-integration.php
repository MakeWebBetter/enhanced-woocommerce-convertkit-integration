<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://makewebbetter.com/
 * @since             1.0.0
 * @package           Enhanced_Woocommerce_ConvertKit_Integration
 *
 * @wordpress-plugin
 * Plugin Name:       Integration with ConvertKit for WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/enhanced-woocommerce-convertkit-integration/
 * Description:       Integrate your Woocommerce store customers to ConvertKit with Real time Syncing.
 * Version:           1.0.1
 * Author:            MakeWebBetter
 * Author URI:        https://makewebbetter.com/
 * Text Domain:       enhanced-woocommerce-convertkit-integration
 * Domain Path:       /languages
 *
 * Requires at least:        4.6
 * Tested up to:             5.2.3
 * WC requires at least:     3.2
 * WC tested up to:          3.7.0
 *
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// To Activate plugin only when WooCommerce is active.
$activated = true;

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	$activated = false;
}

if ( $activated ) {

	// Define plugin constants.
	function define_convertkit_woocommerce_integration_constants() {

		convertkit_woocommerce_integration_constants( 'CONVERTKIT_WOOCOMMERCE_INTEGRATION_VERSION', '1.0.1' );
		convertkit_woocommerce_integration_constants( 'CONVERTKIT_WOOCOMMERCE_INTEGRATION_DIR_PATH', plugin_dir_path( __FILE__ ) );
		convertkit_woocommerce_integration_constants( 'CONVERTKIT_WOOCOMMERCE_INTEGRATION_DIR_URL', plugin_dir_url( __FILE__ ) );
		convertkit_woocommerce_integration_constants( 'MWB_CWI_API_URL', 'https://api.convertkit.com/v3' );

	}

	// Callable function for defining plugin constants.
	function convertkit_woocommerce_integration_constants( $key, $value ) {

		if ( ! defined( $key ) ) {

			define( $key, $value );
		}
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-convertkit-woocommerce-integration-activator.php
	 */
	function activate_convertkit_woocommerce_integration() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-convertkit-woocommerce-integration-activator.php';
		Convertkit_Woocommerce_Integration_Activator::activate();

		// Create transient data.
		set_transient( 'convertkit_woocommerce_integration_transient_user_exp_notice', true, 5 );
	}

	// Add admin notice only on plugin activation.
	add_action( 'admin_notices', 'convertkit_woocommerce_integration_user_exp_notice' );

	// Facebook setup notice on plugin activation.
	function convertkit_woocommerce_integration_user_exp_notice() {

		/**
		 * Check transient.
		 * If transient available display notice.
		 */
		if ( get_transient( 'convertkit_woocommerce_integration_transient_user_exp_notice' ) ) :

			?>

			<div class="notice notice-info is-dismissible">
				<p><strong><?php esc_html_e( 'Welcome to Integration with ConvertKit for WooCommerce', 'enhanced-woocommerce-convertkit-integration' ); ?></strong><?php esc_html_e( ' – a powerful plugin that uploads your Store Customers to ConvertKit with Real time syncing.', 'enhanced-woocommerce-convertkit-integration' ); ?></p>
				<p class="submit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=convertkit_woocommerce_integration_menu' ) ); ?>" class="button-primary"><?php echo esc_html__( 'Go to Settings', 'enhanced-woocommerce-convertkit-integration' ) . ' &#8594'; ?></a></p>
			</div>

			<?php

			delete_transient( 'convertkit_woocommerce_integration_transient_user_exp_notice' );

		endif;
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-convertkit-woocommerce-integration-deactivator.php
	 */
	function deactivate_convertkit_woocommerce_integration() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-convertkit-woocommerce-integration-deactivator.php';
		Convertkit_Woocommerce_Integration_Deactivator::deactivate();
	}

	register_activation_hook( __FILE__, 'activate_convertkit_woocommerce_integration' );
	register_deactivation_hook( __FILE__, 'deactivate_convertkit_woocommerce_integration' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-convertkit-woocommerce-integration.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_convertkit_woocommerce_integration() {

		define_convertkit_woocommerce_integration_constants();

		$plugin = new Convertkit_Woocommerce_Integration();
		$plugin->run();

	}
	run_convertkit_woocommerce_integration();

	// Add settings link on plugin page.
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'convertkit_woocommerce_integration_settings_link' );

	// Settings link.
	function convertkit_woocommerce_integration_settings_link( $links ) {

		$my_link = array(
			'<a href="' . admin_url( 'admin.php?page=convertkit_woocommerce_integration_menu' ) . '">' . esc_html__( 'Settings', 'enhanced-woocommerce-convertkit-integration' ) . '</a>',
		);
		return array_merge( $my_link, $links );
	}
} else {

	// WooCommerce is not active so deactivate this plugin.
	add_action( 'admin_init', 'convertkit_woocommerce_integration_activation_failure' );

	// Deactivate this plugin.
	function convertkit_woocommerce_integration_activation_failure() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	// Add admin error notice.
	add_action( 'admin_notices', 'convertkit_woocommerce_integration_activation_failure_admin_notice' );

	// This function is used to display admin error notice when WooCommerce is not active.
	function convertkit_woocommerce_integration_activation_failure_admin_notice() {

		// to hide Plugin activated notice.
		unset( $_GET['activate'] );

		?>

		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'WooCommerce is not activated, Please activate WooCommerce first to activate Integration with ConvertKit for WooCommerce.' ); ?></p>
		</div>

		<?php
	}
}
