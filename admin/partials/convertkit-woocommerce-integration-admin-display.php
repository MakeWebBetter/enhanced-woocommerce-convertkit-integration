<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Enhanced_Woocommerce_ConvertKit_Integration
 * @subpackage Enhanced_Woocommerce_ConvertKit_Integration/admin/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="convertkit-woocommerce-integration-wrap">

	<h2><?php esc_html_e( 'Integration with ConvertKit for WooCommerce', 'enhanced-woocommerce-convertkit-integration' ); ?></h2>

	<?php

	$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'convertkit';

	// Redirect to default when tab value is not one of the valid ones.
	if ( 'convertkit' != $active_tab && 'pro' != $active_tab ) {

		wp_safe_redirect( admin_url( 'admin.php?page=convertkit_woocommerce_integration_menu' ) );
		exit;
	}

	?>

	<h2 class="nav-tab-wrapper">

		<a href="?page=convertkit_woocommerce_integration_menu&tab=convertkit" class="nav-tab <?php echo 'convertkit' == $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'ConvertKit Setup', 'enhanced-woocommerce-convertkit-integration' ); ?></a>

		<a href="?page=convertkit_woocommerce_integration_menu&tab=pro" class="nav-tab <?php echo 'pro' == $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Our Pro Version', 'enhanced-woocommerce-convertkit-integration' ); ?></a>

	</h2>

	<?php

	if ( 'convertkit' == $active_tab ) {

		// Menu HTML and PHP code for ConvertKit Setup goes here.

		$api_secret = get_option( 'mwb_cwi_plug_api_secret', '' );

		if ( ! empty( $api_secret ) ) {

			$api_secret_validated = get_option( 'mwb_cwi_api_secret_validated', 'false' );

			if ( 'true' === $api_secret_validated ) {

				$account_info = get_option( 'mwb_cwi_api_secret_account_info', array() );

				$name = ! empty( $account_info['name'] ) ? $account_info['name'] : '';
				$email_address = ! empty( $account_info['email_address'] ) ? $account_info['email_address'] : '';

				$custom_fields_creation = get_option( 'mwb_cwi_custom_fields_creation', 'false' );

				$tag_created = get_option( 'mwb_cwi_tag_created', 'false' );

				?>

				<div id="convertkit-woocommerce-integration-account-info-div" class="notice notice-success">
					<h3><strong><?php esc_html_e( 'Connected to ConvertKit Account', 'enhanced-woocommerce-convertkit-integration' ); ?></strong></h3>

					<div id="convertkit-woocommerce-integration-account-info">

						<div id="convertkit-woocommerce-integration-name-email">

							<p id="convertkit-woocommerce-integration-name"><strong><?php esc_html_e( 'Name : ', 'enhanced-woocommerce-convertkit-integration' ); ?></strong><?php echo esc_html( $name ); ?></p>

							<p><strong><?php esc_html_e( 'Email Address : ', 'enhanced-woocommerce-convertkit-integration' ); ?></strong><?php echo esc_html( $email_address ); ?></p>
						</div>

						<?php
						if ( 'true' === $custom_fields_creation && 'true' === $tag_created ) :

							$tag_info = get_option( 'mwb_cwi_tag_info', array() );

							$tag_id = $tag_info['id'];
							$tag_name = $tag_info['name'];

							$tag_url = 'https://app.convertkit.com/subscribers?subscribable_ids=' . $tag_id . '&subscribable_type=Tag';

							?>

						<div id="convertkit-woocommerce-integration-status">

							<p><strong><?php esc_html_e( 'Status : ', 'enhanced-woocommerce-convertkit-integration' ); ?></strong></p>

							<div id="convertkit-woocommerce-integration-status-content">

								<p>	
									<img src="<?php echo esc_url( CONVERTKIT_WOOCOMMERCE_INTEGRATION_DIR_URL ) . 'admin/resources/checked.png'; ?>">
									<span><?php esc_html_e( 'Integration Successful', 'enhanced-woocommerce-convertkit-integration' ); ?></span>
									<?php printf( ' %s <a href="%s" target="_blank">%s</a>', '&#8594', esc_html( $tag_url ), esc_html( $tag_name ) ); ?>
								</p>

								<?php

								$sync_enabled = get_option( 'mwb_cwi_plug_sync_enable', 'yes' );

								if ( 'yes' == $sync_enabled ) :

									?>

								<p> 
									<img src="<?php echo esc_url( CONVERTKIT_WOOCOMMERCE_INTEGRATION_DIR_URL ) . 'admin/resources/sync.png'; ?>">
									<span><?php esc_html_e( 'Syncing', 'enhanced-woocommerce-convertkit-integration' ); ?></span>
								</p>

							<?php endif; ?>
						</div>	
					</div>

				<?php endif; ?>
			</div>
		</div>

				<?php if ( 'false' === $custom_fields_creation || 'false' === $tag_created ) : ?>

			<div class="notice notice-info" id="convertkit-woocommerce-integration-integrate-div" >
				<h3><?php esc_html_e( 'Integrate Woocommerce to ConvertKit', 'enhanced-woocommerce-convertkit-integration' ); ?></h3>

				<p><?php esc_html_e( 'Requires to be setup for the first time for creating Custom Fields and your Store Tag in ConvertKit.', 'enhanced-woocommerce-convertkit-integration' ); ?></p>

				<div id="convertkit-woocommerce-integration-integrate-submit-wrapper">

					<p class="submit"><a href="#" id="convertkit-woocommerce-integration-integrate" class="button-primary"><?php esc_html_e( 'Integrate', 'enhanced-woocommerce-convertkit-integration' ); ?></a></p>

					<div id="convertkit-woocommerce-integration-integrate-loading">

						<img src="<?php echo 'images/spinner.gif'; ?>">

						<p><strong><?php esc_html_e( 'Please wait for a while...', 'enhanced-woocommerce-convertkit-integration' ); ?></strong></p>
					</div>
				</div>

			</div>

		<?php endif; ?>


		<div id="convertkit-woocommerce-integration-custom-field" class="notice">
			<p></p>
		</div>

		<div id="convertkit-woocommerce-integration-tag" class="notice">
			<p></p>
		</div>

				<?php

				if ( isset( $_GET['mwb_success_cwi'] ) && 'true' == $_GET['mwb_success_cwi'] ) {

					$csf_success_message = esc_html__( 'Custom Fields Created Sucessfully.', 'enhanced-woocommerce-convertkit-integration' );

					$tag_info = get_option( 'mwb_cwi_tag_info', array() );

					$tag_info_name = $tag_info['name'];

					$tag_success_message = $tag_info['name'] . esc_html__( ' Tag Created Sucessfully. Your Store Customers will be uploaded to this Tag in ConvertKit as Subscribers.', 'enhanced-woocommerce-convertkit-integration' );

					?>

			<div class="notice notice-success is-dismissible">
				<p><?php echo esc_html( $csf_success_message ); ?></p>
			</div>

			<div class="notice notice-success is-dismissible">
				<p><?php echo esc_html( $tag_success_message ); ?></p>
			</div>

			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'From now on, New Users, Users with updated information, Users with new Orders will be uploaded to ConvertKit and uploaded Users ', 'enhanced-woocommerce-convertkit-integration' ); ?>

					<br>

					<?php esc_html_e( 'will be updated as their information changes in Real Time.', 'enhanced-woocommerce-convertkit-integration' ); ?></p>

					<p><?php esc_html_e( 'It will usually take maximum 5 minutes to reflect in Convertkit.', 'enhanced-woocommerce-convertkit-integration' ); ?></p>
				</div>

					<?php
				}
			} else {

				?>

			<div class="notice notice-error">
				<p><?php esc_html_e( 'ConvertKit API Secret couldn\'t be validated, please enter a valid API Secret.', 'enhanced-woocommerce-convertkit-integration' ); ?></p>
			</div>

				<?php
			}
		}

		settings_errors();

		echo '<div id="convertkit-woocommerce-integration-ck-menu">';

		echo '<form action="options.php" method="post">';

		settings_fields( 'convertkit_woocommerce_integration_ck_menu' );

		do_settings_sections( 'convertkit_woocommerce_integration_ck_menu' );

		wp_nonce_field( 'mwb_cwi_creation_nonce', 'mwb_cwi_nonce_name' );

		submit_button( esc_html__( 'Save Options', 'enhanced-woocommerce-convertkit-integration' ), 'primary', 'mwb_cwi_submit' );

		echo '</form>';

		echo '</div>';

	} // endif ConvertKit Setup tab.

	elseif ( 'pro' == $active_tab ) {

		?>

		<div id="mwb-cwi-pro-content">
			
			<a target="_blank" href="https://makewebbetter.com/product/convertkit-woocommerce-integration-pro/"><h1><u><?php esc_html_e( 'Premium Plugin Additional Features', 'enhanced-woocommerce-convertkit-integration' ); ?></u></h1></a>	

			<div class="mwb-cwi-pro-logo mwb-cwi-pro-logo-feature" >

				<a target="_blank" href="https://makewebbetter.com/product/convertkit-woocommerce-integration-pro/"><img src="<?php echo esc_url( CONVERTKIT_WOOCOMMERCE_INTEGRATION_DIR_URL ) . 'admin/resources/converkit-woo-logo.jpg'; ?>"></a>

			</div>

			<div class="mwb-cwi-pro-features mwb-cwi-pro-logo-feature" >

				<h3><?php esc_html_e( 'Pro Features', 'enhanced-woocommerce-convertkit-integration' ); ?></h3>

				<ul>
					<li><?php esc_html_e( 'Creates additional custom fields with RFM data.', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
					<li><?php esc_html_e( 'RFM segmentation provides valuable information about your store customers.', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
					<li><?php esc_html_e( 'RFM helps in identifying loyal customers and customers who might be getting disengaged.', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
					<li><?php esc_html_e( 'Enables to create powerful Automations and Sequences in ConvertKit based on customer information from your store.', 'enhanced-woocommerce-convertkit-integration' ); ?></li>

				</ul>

				<div class="mwb-cwi-pro-purchase-now" >

					<a class="mwb-cwi-pro-view-product-page" target="_blank" href="https://makewebbetter.com/product/convertkit-woocommerce-integration-pro/?utm_source=MWB-convertkit-woocommerce-integration-ORG&utm_medium=Wordpress-ORG&utm_campaign=MWB-convertkit-woocommerce-integration-ORG"><?php echo esc_html__( 'View Product Page', 'enhanced-woocommerce-convertkit-integration' ) . '&#8594'; ?></a>
				
					<a class="mwb-cwi-pro-buy-now" target="_blank" href="https://makewebbetter.com/checkout/?add-to-cart=27729&utm_source=MWB-convertkit-woocommerce-integration-ORG&utm_medium=Wordpress-ORG&utm_campaign=MWB-convertkit-woocommerce-integration-ORG"><?php esc_html_e( 'Purchase Now', 'enhanced-woocommerce-convertkit-integration' ); ?></a>	
				</div>

			</div>

			<div class="mwb-cwi-pro-custom-fields" >
				
				<h3><?php esc_html_e( 'Additional Custom Fields', 'enhanced-woocommerce-convertkit-integration' ); ?></h3>

				<ul>
					<li><strong><?php esc_html_e( 'RFM ORDER RECENCY RATING', 'enhanced-woocommerce-convertkit-integration' ); ?></strong><?php esc_html_e( ' - A very valuable information regarding how recently a customer has purchased from you. Information about RFM is covered later.', 'enhanced-woocommerce-convertkit-integration' ); ?></li>

					<li><strong><?php esc_html_e( 'RFM ORDER FREQUENCY RATING', 'enhanced-woocommerce-convertkit-integration' ); ?></strong><?php esc_html_e( ' - A very valuable information regarding how many times a customer has purchased from you.', 'enhanced-woocommerce-convertkit-integration' ); ?></li>

					<li><strong><?php esc_html_e( 'RFM MONETARY RATING', 'enhanced-woocommerce-convertkit-integration' ); ?></strong><?php esc_html_e( ' - A very valuable information regarding how much a customer has spent with you.', 'enhanced-woocommerce-convertkit-integration' ); ?></li>

					<li><strong><?php esc_html_e( 'AVERAGE DAYS BETWEEN ORDERS', 'enhanced-woocommerce-convertkit-integration' ); ?></strong><?php esc_html_e( ' - Average number of days between Orders of a Customer.', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
				</ul>
			</div>

			<div class="mwb-cwi-pro-rfm" >

				<h3><?php esc_html_e( 'What is RFM?', 'enhanced-woocommerce-convertkit-integration' ); ?></h3>

				<p>
				<?php
				printf(
					'%s<b>%s</b>%s<b>%s</b>%s<b>%s</b>%s<b>%s</b>%s<b>%s</b>%s<b>%s</b>%s<b>%s</b>%s<b>%s</b>%s<b>%s</b>%s<b>%s</b>%s',
					esc_html__( 'RFM stands for ', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( 'Recency', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( ', ', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( 'Frequency', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( ', and ', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( 'Monetary', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( ' value, each corresponding to some key customer trait. These RFM metrics are important indicators of a ', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( 'customer’s behavior', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( ' because ', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( 'frequency', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( ' and ', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( 'monetary', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( ' value affect a ', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( 'customer’s lifetime value', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( ', and ', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( 'recency', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( ' effects ', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( 'retention', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( ', a measure of ', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( 'engagement', 'enhanced-woocommerce-convertkit-integration' ),
					esc_html__( '.', 'enhanced-woocommerce-convertkit-integration' )
				);
				?>
					</p>

				<div class="mwb-cwi-pro-rfm-ques" >
					
					<h4><?php esc_html_e( 'RFM segmentation helps us in answering these questions :', 'enhanced-woocommerce-convertkit-integration' ); ?></h4>

					<ul>
						<li><?php esc_html_e( 'Who are the customers spending the most?', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
						<li><?php esc_html_e( 'Who are the most loyal customers coming back and placing a second, third, fourth order?', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
						<li><?php esc_html_e( 'Who are the newest customers?', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
						<li><?php esc_html_e( 'Who are those customers I am about to lose?', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
						<li><?php esc_html_e( 'Who are those customers I\'ve already lost?', 'enhanced-woocommerce-convertkit-integration' ); ?></li>

					</ul>

				</div>


				<div class="mwb-cwi-pro-second-wrap">
					<h4><?php esc_html_e( 'RFM Custom fields contains value between 1 to 5 which are explained as follows:', 'enhanced-woocommerce-convertkit-integration' ); ?></h4>
					<div class="mwb-cwi-pro-rfm-recency mwb-cwi-pro-inner" >

						<h5><?php esc_html_e( 'Order Recency Rating', 'enhanced-woocommerce-convertkit-integration' ); ?></h5>

						<ul>

							<li><?php esc_html_e( 'Order Recency = 5 ( purchased within the last 30 days )', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
							<li><?php esc_html_e( 'Order Recency = 4 ( purchased within the last 31 to 90 days )', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
							<li><?php esc_html_e( 'Order Recency = 3 ( purchased within the last 91 to 180 days )', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
							<li><?php esc_html_e( 'Order Recency = 2 ( purchased within the last 181 to 365 days )', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
							<li><?php esc_html_e( 'Order Recency = 1 ( purchased more than 365 days ago )', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
						</ul>

					</div>


					<div class="mwb-cwi-pro-rfm-frequency mwb-cwi-pro-inner" >

						<h5><?php esc_html_e( 'Order Frequency Rating', 'enhanced-woocommerce-convertkit-integration' ); ?></h5>

						<ul>
							<li><?php esc_html_e( 'Order Frequency = 5 ( 20 or more orders )', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
							<li><?php esc_html_e( 'Order Frequency = 4 ( between 10 and 20 orders )', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
							<li><?php esc_html_e( 'Order Frequency = 3 ( between 5 and 10 orders )', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
							<li><?php esc_html_e( 'Order Frequency = 2 ( between 2 and 5 orders )', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
							<li><?php esc_html_e( 'Order Frequency = 1 ( 1 order )', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
						</ul>

					</div>

					<div class="mwb-cwi-pro-rfm-monetary mwb-cwi-pro-inner" >

						<h5><?php esc_html_e( 'Monetary Rating', 'enhanced-woocommerce-convertkit-integration' ); ?></h5>

						<ul>

							<li><?php esc_html_e( 'Monetary = 5 ( spent more than $1,000 )', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
							<li><?php esc_html_e( 'Monetary = 4 ( spent between $750 and $1,000 )', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
							<li><?php esc_html_e( 'Monetary = 3 ( spent between $500 and $750 )', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
							<li><?php esc_html_e( 'Monetary = 2 ( spent between $250 and $500 )', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
							<li><?php esc_html_e( 'Monetary = 1 ( spent less than $250 )', 'enhanced-woocommerce-convertkit-integration' ); ?></li>
						</ul>

					</div>
				</div>

			</div>

			<div class="mwb-cwi-pro-rfm-plus-convertkit" >

				<h3><?php esc_html_e( 'RFM + ConvertKit', 'enhanced-woocommerce-convertkit-integration' ); ?></h3>

				<div class="mwb-cwi-pro-rfm-plus-convertkit-content" >

					<p><?php esc_html_e( 'The true power of RFM Segmentation comes when you combine ', 'enhanced-woocommerce-convertkit-integration' ); ?><strong><?php esc_html_e( 'RFM metrics and use ConvertKit to deliver the right message at the right time.', 'enhanced-woocommerce-convertkit-integration' ); ?></strong></p>

					<p><?php esc_html_e( 'Consider you have a customer with an Order Recency Rating of 3 ( purchased within the last 91 to 180 days ).', 'enhanced-woocommerce-convertkit-integration' ); ?></p>

					<p><?php esc_html_e( 'The same customer has an Order Frequency Rating of 5 ( 20 or more orders ), while also having a Monetary Rating of 5 ( spent more than $1,000 ).', 'enhanced-woocommerce-convertkit-integration' ); ?></p>

					<p><?php esc_html_e( 'These RFM metrics tell a story – you have a highly valuable customer that may be getting disengaged. The customer needs some extra affection – perhaps a personalized email with a special coupon code!', 'enhanced-woocommerce-convertkit-integration' ); ?></p>

				</div>
				<div class="mwb-cwi-pro-rfm-video" >

					<iframe width="800" height="400" src="https://www.youtube.com/embed/pIai0KhAfmg?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>

				</div>
				
			</div>

		</div>

		<?php
	}

	?>

</div> <!-- convertkit-woocommerce-integration-wrap -->
