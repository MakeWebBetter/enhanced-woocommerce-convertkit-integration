<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Enhanced_Woocommerce_ConvertKit_Integration
 * @subpackage Enhanced_Woocommerce_ConvertKit_Integration/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Enhanced_Woocommerce_ConvertKit_Integration
 * @subpackage Enhanced_Woocommerce_ConvertKit_Integration/includes
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Convertkit_Woocommerce_Integration_Deactivator {

	/**
	 * Runs on plugin deactivation.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		// Clear scheduled cron for User update.
		if ( wp_next_scheduled( 'mwb_cwi_cron_schedule' ) ) {

			wp_clear_scheduled_hook( 'mwb_cwi_cron_schedule' );
		}

	}

}
