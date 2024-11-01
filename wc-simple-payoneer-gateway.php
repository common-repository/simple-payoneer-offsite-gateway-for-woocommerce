<?php
/*
 * Plugin Name: Simple Payoneer Offsite Gateway for WooCommerce
 * Plugin URI: https://profiles.wordpress.org/rynald0s/
 * Description: This is a simple Payoneer Offsite payments gateway plugin for WooCommerce. 
 * Author: rynald0s
 * Author URI: http:rynaldo.com
 * Version: 1.0
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
 
defined( 'ABSPATH' ) or exit;

// Make sure WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}

/**
 * Add the gateway to WC Available Gateways
 * 
 * @since 1.0.0
 * @param array $gateways all available WC gateways
 * @return array $gateways all WC gateways + offline gateway
 */
function wc_payoneer_add_to_gateways( $gateways ) {
	$gateways[] = 'WC_Payoneer_Offsite';
	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'wc_payoneer_add_to_gateways' );

/**
 * Adds plugin page links
 * 
 * @since 1.0.0
 * @param array $links all plugin links
 * @return array $links all plugin links + our custom links (i.e., "Settings")
 */
function wc_offsite_payoneer_plugin_links( $links ) {

	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=offline_gateway' ) . '">' . __( 'Configure', 'wc-gateway-offline' ) . '</a>'
	);

	return array_merge( $plugin_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_offsite_payoneer_plugin_links' );

/**
 * Payoneer offsite gateway
 *
 * Provides the offsite Payoneer payment method.
 * We load it later to ensure WC is loaded first since we're extending it.
 *
 * @class 		WC_Payoneer_Offsite
 * @extends		WC_Payment_Gateway
 * @version		1.0.0
 * @package		WooCommerce/Classes/Payment
 */
add_action( 'plugins_loaded', 'wc_payoneer_offline_gateway_init', 11 );

function wc_payoneer_offline_gateway_init() {

	class WC_Payoneer_Offsite extends WC_Payment_Gateway {

		/**
		 * Constructor for the gateway.
		 */
		public function __construct() {
	  
			$this->id = 'payoneer';
			$this->icon = plugin_dir_url( __FILE__ ) . 'assets/images/logo.png'; 
			$this->has_fields = true;
			$this->method_title = 'Payoneer Gateway';
			$this->link_direct = '<a target="_blank" href="https://myaccount.payoneer.com/MainPage/Widget.aspx?w=MakeAPayment#/pay/makeapayment">Pay to recipient Payoneer account</a>';
			$this->instructions = $this->get_option( 'instructions', $this->description );
			$this->method_description = 'Collect Payoneer payments on your WooCommerce store.';
			 
			$this->supports = array(
					'products'
				);
			 
			$this->init_form_fields();
			 
			$this->init_settings();
			$this->enabled = $this->get_option( 'enabled' );
			$this->title = $this->get_option( 'title' );
			$this->payoneer_email = $this->get_option( 'payoneer_email', true );
			 
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		  
			// Actions
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
		  
			// Customer Emails
			add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
		}
	
		public function init_form_fields() {
	  
	 			$this->form_fields = array(
					'enabled' => array(
						'title'       => esc_html__( 'Enable/Disable', "wc-payoneer-offsite" ),
						'label'       => esc_html__( 'Enable Payoneer', "wc-payoneer-offsite" ),
						'type'        => 'checkbox',
						'description' => '',
						'default'     => 'no'
					),
					'title' => array(
						'title'       => esc_html__( 'Gateway title', "wc-payoneer-offsite" ),
						'type'        => 'text',
						'description' => esc_html__( 'This is the title which the user will see during checkout.', "wc-payoneer-offsite" ),
						'default'     => esc_html__( 'Payoneer', "wc-payoneer-offsite" ),
					),
	                'payoneer_email'  => array(
	                    'title'       => esc_html__( 'Your Payoneer email address', "wc-payoneer-offsite" ),
	                    'description' => esc_html__( 'Add your payoneer email address for receiving payments. Without this added, you will NOT receive payment', "wc-payoneer-offsite" ),
	                    'type'        => 'text'
	                ),
				);
		 	}

		public function thankyou_page() {
			if ( $this->instructions ) {
				echo wpautop( wptexturize( $this->instructions ) );
			}
		}

		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		
			if ( $this->instructions && ! $sent_to_admin && $this->id === $order->payment_method && $order->has_status( 'on-hold' ) ) {
				echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
			}
		}

			public function payment_fields() { 

				if ( $this->payoneer_email ) {
					echo wpautop( wptexturize( "Please make your payment to the following recipient" ));
						echo '<br>';
						echo wpautop ("<font color=red>" . $this->payoneer_email . '</font>');
						echo '<br>';
				}

				echo "<p> Click on a Payoneer payment link below, complete the payment, then come back to this page and fill in the details</p>" . '<br>';

				if ( $this->link_direct ) {
					echo wpautop( wptexturize( $this->link_direct ) ); 
				}
					 ?>

				  <tr>
				    <td><br><label for="payoneer_email"><?php esc_html_e( 'Your Payoneer email address', "woocommerce"). '<br>' ; ?></label></td>
				    <td><input class="form-row-wide" type="email" name="payoneer_email" id="payoneer_email"></td><br>
				  </tr><br>
				  <tr>
				    <td><label for="payoneer_transaction_id"><?php esc_html_e( 'Transaction ID', "wc-gateway-offsite" );?></label></td><br>
				    <td><input class="form-row-wide" type="text" name="payoneer_transaction_id" id="payoneer_transaction_id"></td>
				  </tr>
				</table>

			<?php }
	
		public function process_payment( $order_id ) {
	
			$order = wc_get_order( $order_id );
			
			// Mark as on-hold (we're awaiting the payment)
			$order->update_status( 'on-hold', __( 'Awaiting offsite Payoneer payment', 'wc-gateway-offsite' ) );
			
			// Reduce stock levels
			$order->reduce_order_stock();
			
			// Remove cart
			WC()->cart->empty_cart();
			
			// Return thankyou redirect
			return array(
				'result' 	=> 'success',
				'redirect'	=> $this->get_return_url( $order )
			);
		}
  	}
}

    function payoneer_admin_order_data( $order ) {

        if( $order->get_payment_method() != 'payoneer' )
            return;

        $email = (get_post_meta($_GET['post'], '_payoneer_email', true)) ? get_post_meta($_GET['post'], '_payoneer_email', true) : '';
        $transaction = (get_post_meta($_GET['post'], '_payoneer_transaction_id', true)) ? get_post_meta($_GET['post'], '_payoneer_transaction_id', true) : '';

        ?>

        <div class="form-row-wide" >
                <tbody>
                    <tr>
                        <th><strong><?php esc_html_e('Payoneer Email:', 'wc-payoneer-offsite') ;?></strong></th>
                        <td><?php echo esc_attr( $email );?></td>
                    </tr>
                    <tr>
                        <th><strong><?php esc_html_e('Transaction ID :', 'wc-payoneer-offsite') ;?></strong></th>
                        <td><?php echo esc_attr( $transaction );?></td>
                    </tr>
                </tbody>
        </div>

        <?php

    }

    add_action('woocommerce_admin_order_data_after_billing_address', 'payoneer_admin_order_data' );

    function payoneer_additional_field_update( $order_id ) {

        if($_POST['payment_method'] != 'payoneer' )
            return;

        $payoneer_email = sanitize_text_field( $_POST['payoneer_email'] );
        $payoneer_transaction_id = sanitize_text_field( $_POST['payoneer_transaction_id'] );

        $email = isset($payoneer_email) ? $payoneer_email : '';
        $transaction = isset($payoneer_transaction_id) ? $payoneer_transaction_id : '';

        update_post_meta($order_id, '_payoneer_email', $email);
        update_post_meta($order_id, '_payoneer_transaction_id', $transaction);

    }

    add_action( 'woocommerce_checkout_update_order_meta', 'payoneer_additional_field_update' );

    function payoneer_order_details( $order ) {

        if( $order->get_payment_method() != 'payoneer' )
            return;

        global $wp;

        $order_id  = absint( $wp->query_vars['order-received'] );

        $email = (get_post_meta($order_id, '_payoneer_email', true)) ? get_post_meta($order_id, '_payoneer_email', true) : '';
        $transaction = (get_post_meta($order_id, '_payoneer_transaction_id', true)) ? get_post_meta($order_id, '_payoneer_transaction_id', true) : '';

        ?>

        <table>
            <tr>
                <th><?php esc_html_e('Payoneer Email:', 'wc-payoneer-offsite');?></th><br>
                <td><?php echo esc_attr( $email );?></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Transaction ID:', 'wc-payoneer-offsite');?></th>
                <td><?php echo esc_attr( $transaction );?></td>
            </tr>
        </table>
        <?php

    }

    add_action('woocommerce_order_details_after_customer_details', 'payoneer_order_details' );

    function payoneer_shop_order_columns($columns) {

        $new_columns = (is_array($columns)) ? $columns : array();
        unset( $new_columns['order_actions'] );
        $new_columns['column_email_address']     = esc_html__('Payoneer email:', 'wc-payoneer-offsite');
        $new_columns['column_transaction_id']     = esc_html__('Transaction ID:', 'wc-payoneer-offsite');

        $new_columns['order_actions'] = $columns['order_actions'];
        return $new_columns;

    }

    add_filter( 'manage_edit-shop_order_columns', 'payoneer_shop_order_columns' );

    function payoneer_order_posts_custom_column($column) {

        global $post;

        $column_email_address = (get_post_meta($post->ID, '_payoneer_email', true)) ? get_post_meta($post->ID, '_payoneer_email', true) : '';
        $column_transaction_id = (get_post_meta($post->ID, '_payoneer_transaction_id', true)) ? get_post_meta($post->ID, '_payoneer_transaction_id', true) : '';

        if ( $column == 'column_email_address' ) {
            echo esc_attr( $column_email_address );
        }
        if ( $column == 'column_transaction_id' ) {
            echo esc_attr( $column_transaction_id );
        }
    }

    add_action( 'manage_shop_order_posts_custom_column', 'payoneer_order_posts_custom_column', 2 );
