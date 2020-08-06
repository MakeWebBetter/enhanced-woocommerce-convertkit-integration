<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Enhanced_Woocommerce_ConvertKit_Integration
 * @subpackage Enhanced_Woocommerce_ConvertKit_Integration/includes
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
 * @package    Enhanced_Woocommerce_ConvertKit_Integration
 * @subpackage Enhanced_Woocommerce_ConvertKit_Integration/includes
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Convertkit_Woocommerce_Integration {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Convertkit_Woocommerce_Integration_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'CONVERTKIT_WOOCOMMERCE_INTEGRATION_VERSION' ) ) {

			$this->version = CONVERTKIT_WOOCOMMERCE_INTEGRATION_VERSION;
		} else {

			$this->version = '1.0.0';
		}

		$this->plugin_name = 'enhanced-woocommerce-convertkit-integration';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Convertkit_Woocommerce_Integration_Loader. Orchestrates the hooks of the plugin.
	 * - Convertkit_Woocommerce_Integration_i18n. Defines internationalization functionality.
	 * - Convertkit_Woocommerce_Integration_Admin. Defines all hooks for the admin area.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-convertkit-woocommerce-integration-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-convertkit-woocommerce-integration-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-convertkit-woocommerce-integration-admin.php';

		$this->loader = new Convertkit_Woocommerce_Integration_Loader();

		/**
		 * The class responsible for defining all ConverKit API related functionality.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-convertkit-woocommerce-integration-api.php';

		/**
		 * The class responsible for managing User properties ( custom fields ) for ConvertKit Subscribers.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-convertkit-woocommerce-integration-properties.php';

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Convertkit_Woocommerce_Integration_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Convertkit_Woocommerce_Integration_i18n();

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

		$plugin_admin = new Convertkit_Woocommerce_Integration_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Add settings menu for ConvertKit WooCommerce Integration.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );

		// Set cron recurrence time for 'mwb_cwi_five_minutes' schedule.
		$this->loader->add_filter( 'cron_schedules', $plugin_admin, 'set_cron_schedule_time' );

		// Using Settings API for settings menu.
		$this->loader->add_action( 'admin_init', $plugin_admin, 'settings_api' );

		// Fires on Save changes submit.
		$this->loader->add_action( 'admin_init', $plugin_admin, 'submit_click' );

		// Running action for ConvertKit integration.
		$this->loader->add_action( 'wp_ajax_convertkit_woocommerce_integration_integration', $plugin_admin, 'integration_handle' );

		global $wp_version;

		if ( '4.9.6' <= $wp_version ) {

			// Add GDPR privacy policy for convert kit.
			$this->loader->add_action( 'admin_init', $plugin_admin, 'privacy_policy' );
		}

		$sync_enabled = get_option( 'mwb_cwi_plug_sync_enable', 'yes' );

		if ( 'yes' == $sync_enabled ) {

			// Update user meta on User Profile update.
			$this->loader->add_action( 'profile_update', $plugin_admin, 'user_updated' );

			// Update user meta when New User registers.
			$this->loader->add_action( 'user_register', $plugin_admin, 'user_updated' );

			// Update user meta on Order Checkout.
			$this->loader->add_action( 'woocommerce_checkout_update_user_meta', $plugin_admin, 'user_updated' );

			// Update user meta on Order Status change.
			$this->loader->add_action( 'woocommerce_order_status_changed', $plugin_admin, 'user_order_updated' );

			// Define Cron schedule fire Event for User update.
			$this->loader->add_action( 'mwb_cwi_cron_schedule', $plugin_admin, 'cron_fire_event' );
		}

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
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
	 * @return    Convertkit_Woocommerce_Integration_Loader    Orchestrates the hooks of the plugin.
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

}
