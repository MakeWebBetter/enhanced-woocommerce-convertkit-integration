<?php

/**
 * Fired during plugin activation
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Enhanced_Woocommerce_ConvertKit_Integration
 * @subpackage Enhanced_Woocommerce_ConvertKit_Integration/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Enhanced_Woocommerce_ConvertKit_Integration
 * @subpackage Enhanced_Woocommerce_ConvertKit_Integration/includes
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Convertkit_Woocommerce_Integration_Activator {

	/**
	 * Runs on plugin activation.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// Schedule cron for User update.
		if ( ! wp_next_scheduled( 'mwb_cwi_cron_schedule' ) ) {

			wp_schedule_event( time(), 'mwb_cwi_five_minutes', 'mwb_cwi_cron_schedule' );
		}

		// Create Log file on plugin activation.

		if ( defined( 'WC_LOG_DIR' ) ) {

			$log_file = WC_LOG_DIR . 'MWB-ConvertKit-Woocommerce.log';

			if ( ! is_dir( $log_file ) ) {

				@fopen( $log_file, 'a' );
			}
		}
	}

}
