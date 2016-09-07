<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Payment Request API.
 */
class WC_Payment_Request_API {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'woocommerce_proceed_to_checkout', array( $this, 'enqueue_payment_request_api' ) );
	}

	/**
	 * Enqueue scripts for the payment request API.
	 */
	public function register_scripts() {
		wp_register_script( 'payment-request-api-shim', 'https://storage.googleapis.com/prshim/v1/payment-shim.js' );
		wp_register_script( 'wc-payment-request', WC()->plugin_url() . '/assets/js/frontend/wc-payment-request.js', array( 'jquery', 'payment-request-api-shim' ), WC_VERSION, true );
	}

	/**
	 * Add values for the payment request API when the cart is available.
	 */
	public function enqueue_payment_request_api() {
		$display_items = array();
		$totals        = array();

		foreach ( WC()->cart->get_cart() as $item ) {
			$product      = $item['data'];
			$display_item = array(
				'label'  => esc_html( $product->get_title() . ' x' . $item['quantity'] ),
				'amount' => array(
					'currency' => get_woocommerce_currency(),
					'value'    => wc_format_decimal( 'excl' === get_option( 'woocommerce_tax_display_cart' ) ? $product->get_price_excluding_tax() : $product->get_price_including_tax(), wc_get_price_decimals() ),
				)
			);
			$display_items[] = $display_item;
		}

		$total = array(
			'label'  => __( 'Subtotal', 'woocommerce' ),
			'amount' => array(
				'currency' => get_woocommerce_currency(),
				'value'    => WC()->cart->total,
			)
		);

		wp_enqueue_script( 'wc-payment-request' );
		wp_localize_script( 'wc-payment-request', 'wc_payment_request_params', array(
			'display_items' => $display_items,
			'total'         => $total,
		) );
	}
}

new WC_Payment_Request_API();
