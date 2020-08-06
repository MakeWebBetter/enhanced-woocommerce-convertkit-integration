<?php

/**
 * Manage User properties ( custom fields ) for ConvertKit Subscribers.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Enhanced_Woocommerce_ConvertKit_Integration
 * @subpackage Enhanced_Woocommerce_ConvertKit_Integration/includes
 */

/**
 * Manage User properties ( custom fields ) for ConvertKit Subscribers.
 *
 * This class defines all functions for managing User properties for ConvertKit Subscribers.
 *
 * @since      1.0.0
 * @package    Enhanced_Woocommerce_ConvertKit_Integration
 * @subpackage Enhanced_Woocommerce_ConvertKit_Integration/includes
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Convertkit_Woocommerce_Integration_Properties {

	/**
	 * Custom Fields for ConvertKit Subscribers.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $properties    The array of Custom Fields.
	 */
	protected $properties;

	/**
	 * Wordpress User ID for retrieving User object.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      int    $user_id    Wordpress User ID.
	 */
	protected $user_id;

	/**
	 * Cached array of Custom fields with values.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $cache    The array of Custom Fields with values that has been cached.
	 */
	protected $cache = array();

	/**
	 * Initialize the Class.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $user_id = 0 ) {

		$this->properties = $this->set_properties();

		$this->user_id = $user_id;

	}

	/**
	 * Create Custom Fields array.
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
	protected function set_properties() {

		$properties = array(
			'account_creation_date',
			'shopping_cart_customer_id',
			'subscriber_roles',
			'total_value_of_orders',
			'total_number_of_orders',
			'average_order_value',
			'last_order_date',
			'last_order_value',
			'last_order_status',
			'last_order_order_number',
			'first_order_date',
			'first_order_value',
		);

		$properties = apply_filters( 'mwb_cwi_set_properties', $properties );

		return $properties;

	}

	/**
	 * Retrieve Custom Fields array.
	 *
	 * @since    1.0.0
	 */
	public function get_properties() {

		$properties = $this->properties;

		return $properties;

	}

	/**
	 * Retrieve Custom Fields array with Values.
	 *
	 * @since    1.0.0
	 */
	public function get_properties_values() {

		$properties = $this->properties;

		$set_values = $this->set_properties_values();

		if ( ! $set_values ) {

			return array();
		}

		foreach ( $properties as $field ) {

			$field_value = $this->get_property_value( $field );

			if ( 0 != $field_value && empty( $field_value ) ) {

				continue;
			}

			$properties_values[ $field ] = $field_value;
		}

		return $properties_values;

	}

	/**
	 * Retrieve Custom Field with Value.
	 *
	 * @since    1.0.0
	 */
	public function get_property_value( $field = '' ) {

		if ( array_key_exists( $field, $this->cache ) ) {

			return $this->cache[ $field ];
		}
	}

	/**
	 * Set all Custom Fields with Values in Cache.
	 *
	 * @since    1.0.0
	 */
	public function set_properties_values() {

		$subscriber = get_user_by( 'id', $this->user_id );

		if ( ! $subscriber ) {

			return false;
		}

		$subscriber_orders = get_posts(
			array(
				'numberposts' => -1,
				'fields' => 'ids', // return only ids.
				'meta_key'    => '_customer_user',
				'meta_value'  => $this->user_id,
				'post_type'   => wc_get_order_types(),
				'post_status' => array_keys( wc_get_order_statuses() ),
				'order'   => 'DESC', // get last order first.
			)
		);

		$this->cache['account_creation_date'] = ! empty( $subscriber->data->user_registered ) ? date( 'd M Y', strtotime( $subscriber->data->user_registered ) ) : '';

		$this->cache['shopping_cart_customer_id'] = $this->user_id;

		$this->cache['subscriber_roles'] = ! empty( $subscriber->roles ) && is_array( $subscriber->roles ) ? implode( ', ', $subscriber->roles ) : '';

		$this->cache['total_value_of_orders'] = 0;

		if ( is_array( $subscriber_orders ) && count( $subscriber_orders ) ) {

			$order_frequency = count( $subscriber_orders );

			$this->cache['total_number_of_orders'] = $order_frequency;

			$counter = 0;

			foreach ( $subscriber_orders as $order_id ) {

				$order_id = ! empty( $order_id ) ? $order_id : 0;

				// Continue if order is not a valid one.
				if ( ! $order_id ) {

					continue;
				}

				// Order object.
				$order = new WC_Order( $order_id );

				// Continue if Order object is not a valid one.
				if ( empty( $order ) || is_wp_error( $order ) ) {

					continue;
				}

				$order_items = $order->get_items();

				$order_total = $order->get_total();

				$this->cache['total_value_of_orders'] += floatval( $order_total );

				// Retrieve last Order details when counter is eqaul to 0.
				if ( ! $counter ) {

					$last_order_date = get_post_time( 'd M Y', true, $order_id );

					$this->cache['last_order_date'] = $last_order_date;

					$this->cache['last_order_value'] = floatval( $order_total );

					$this->cache['last_order_order_number'] = $order_id;

					$this->cache['last_order_status'] = 'wc-' . $order->get_status();

				}

				// Retrieve first Order details.
				if ( $counter === count( $subscriber_orders ) - 1 ) {

					$first_order_date = get_post_time( 'd M Y', true, $order_id );

					$this->cache['first_order_date'] = $first_order_date;
					$this->cache['first_order_value'] = floatval( $order_total );

				}

				$counter++;

			}

			// Average Order Value.

			$this->cache['average_order_value'] = round( $this->cache['total_value_of_orders'] / $this->cache['total_number_of_orders'], 2 );

		} else {

			$this->cache['total_number_of_orders'] = 0;
		}

		return true;

	}

}
