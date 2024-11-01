<?php
/**
 * Displays custom settings in WooCommerce backend for product
 *
 * @package PricePerUnit/Admin
 */

if ( ! defined( 'ABSPATH' ) ) :
	exit; // Exit if accessed directly.
endif;
$product_id      = get_the_ID();
$altered_product = wc_get_product( $product_id );
// Behavior settings of both pages.
$behav_settings = array(
	''    => esc_attr__( 'Default - according to general settings', 'woo-price-per-unit' ),
	'not' => esc_attr__( 'Do not recalculate', 'woo-price-per-unit' ),
);
// phpcs:disable Generic.WhiteSpace.ScopeIndent
echo '<div id="mcmp_ppu_options" class="panel woocommerce_options_panel">';
// mcmp_ppu fields will be created here.
	echo '<p class="admin-warn-no-weight mcmp-product-edit-warn">';
		echo '<span>' . esc_html__( 'Warning: This product has no weight set. The price per unit will not be shown unless the weight is set on Shipping tab. Please check it.', 'woo-price-per-unit' ) . '</span>';
	echo '</p>';
	echo '<p>';
		echo '<span class="section-heading">' . esc_html__( 'Local overrides for price recalculation', 'woo-price-per-unit' ) . '</span><br>';
		echo esc_html__( 'These settings will affect only this product despite global settings.', 'woo-price-per-unit' ) . '<br>';
		echo esc_html__( 'Global settings can be found at ', 'woo-price-per-unit' );
		echo '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=products&section=mcmp_price_pu' ) ) . '" target="_blank">';
		echo wp_kses_post( __( '<i>WooCommerce -> Settings -> Products -> Price Per Unit</i>', 'woo-price-per-unit' ) ) . '</a>';
	echo '</p>';
	echo '<div class="options_group">';
		woocommerce_wp_select(
			array(
				'id'          => '_mcmp_ppu_general_override',
				'label'       => esc_html__( 'Shop page price behavior', 'woo-price-per-unit' ),
				'description' => esc_html__( 'Behaviour of recalculated price on shop page', 'woo-price-per-unit' ),
				'default'     => '',
				'options'     => $behav_settings,
				'desc_tip'    => true,
			)
		);
		woocommerce_wp_select(
			array(
				'id'          => '_mcmp_ppu_single_page_override',
				'label'       => esc_html__( 'Single product page behavior', 'woo-price-per-unit' ),
				'description' => esc_html__( 'Behaviour of recalculated price on single product page', 'woo-price-per-unit' ),
				'default'     => '',
				'options'     => $behav_settings,
				'desc_tip'    => true,
			)
		);
	echo '</div>';
	echo '<p>';
		echo '<span class="section-heading">' . esc_html__( 'Settings for Price per unit plug-in', 'woo-price-per-unit' ) . '</span><br>';
		echo '<span class="mcmp-pro-advert">';
			echo esc_html__( 'In PRO version you will find here more settings for individual products', 'woo-price-per-unit' ) . '<br>';
			echo esc_html__( 'You can buy the plug-in here ', 'woo-price-per-unit' );
			echo '<a href="https://mechcomp.cz/price-per-unit-pro/" target="_blank">';
			echo esc_html__( 'Price per Unit PRO', 'woo-price-per-unit' ) . '</a>';
		echo '</span>';
		echo '<span class="mcmp-pro-advert">';
			echo '<span class="coupon">';
				echo esc_html__( 'PRO Version features:', 'woo-price-per-unit' ) . '<br>';
			echo '</span>';
			echo '<strong>' . esc_html__( '- New feature - Quick Editor settings: Easily adjust Price Per Unit (PPU) settings on the fly.', 'woo-price-per-unit' ) . '</strong><br>';
			echo '<strong>' . esc_html__( '- New feature - Bulk Editor settings: Save time by editing PPU settings in bulk.', 'woo-price-per-unit' ) . '</strong><br>';
			echo esc_html__( '- Enhanced display options: Customize how prices appear on product pages.', 'woo-price-per-unit' ) . '<br>';
			echo esc_html__( '- Shop Page display options: More ways to showcase prices on your shop page.', 'woo-price-per-unit' ) . '<br>';
			echo esc_html__( '- Custom text for recalculated prices: Tailor the recalculated price display text.', 'woo-price-per-unit' ) . '<br>';
			echo esc_html__( '- Flexible weight unit display: Show prices for different units (kg, g, lbs, oz) from your shop default unit. Example: shop in kg and some products in grams.', 'woo-price-per-unit' ) . '<br>';
			echo esc_html__( '- Alternative Weight Entry: Differentiate between shipping weight and net weight for accurate pricing. Example: use shipping weight as gross and recalculate per net weight.', 'woo-price-per-unit' ) . '<br>';
			echo esc_html__( '- Multiple unit Recalculation: Display prices per different quantities (e.g., price per 5kg).', 'woo-price-per-unit' ) . '<br>';
			echo esc_html__( '- Possibility to save prices in database for later use.', 'woo-price-per-unit' ) . '<br>';
			echo esc_html__( '- WPML Compatible: Seamless integration with WPML for multilingual support.', 'woo-price-per-unit' ) . '<br>';
		echo '</span>';
		echo '<span class="mcmp-pro-advert">';
			echo '<span class="coupon">';
				echo esc_html__( 'Another Must-Have Plugin: Sell by Weight PRO', 'woo-price-per-unit' ) . '<br>';
			echo '</span>';
			echo '<strong>' . esc_html__( 'Simplify product pricing with Sell by Weight PRO!', 'woo-price-per-unit' ) . '</strong><br>';
			echo esc_html__( 'Easily sell products at a constant price per kilogram with multiple weight options.', 'woo-price-per-unit' ) . '<br>';
			echo esc_html__( 'Enter the unit price once and let the plugin calculate options prices based on their weight.', 'woo-price-per-unit' ) . '<br>';
			echo esc_html__( 'It works similarly as Variable products, but it is easier to manage.', 'woo-price-per-unit' ) . '<br>';
			echo esc_html__( 'The plugin is compatible with all WooCommerce weight units and works with the one you\'ve chosen.', 'woo-price-per-unit' ) . '</br>';
			echo esc_html__( 'More information can be found here ', 'woo-price-per-unit' );
			echo '<a href="https://mechcomp.cz/sell-by-weight-pro/" target="_blank">';
			echo esc_html__( 'Sell by Weight PRO', 'woo-price-per-unit' ) . '</a>';
		echo '</span>';
		echo '<span class="mcmp-pro-advert">';
			echo '<span class="coupon">';
				echo esc_html__( 'New plug-in available - Unit Based Stock PRO', 'woo-price-per-unit' ) . '<br>';
			echo '</span>';
			echo '<strong>' . esc_html__( 'Manage your stock in Weight units!', 'woo-price-per-unit' ) . '</strong><br>';
			echo esc_html__( 'Handle inventory in weight units - a capability unprecedented in WooCommerce, which traditionally manages stock solely based on purchased quantity.', 'woo-price-per-unit' ) . '<br>';
			echo esc_html__( 'Override the product weight with custom stock value. This value will be used for inventory deduction.', 'woo-price-per-unit' ) . '<br>';
			echo esc_html__( 'Supports decimal values for stock inventory as well.', 'woo-price-per-unit' ) . '<br>';
			echo esc_html__( 'More information can be found here ', 'woo-price-per-unit' );
			echo '<a href="https://mechcomp.cz/unit-based-stock-pro/" target="_blank">';
			echo esc_html__( 'Unit Based Stock PRO', 'woo-price-per-unit' ) . '</a>';
		echo '</span>';
	echo '</p>';
	wp_nonce_field( 'mcmp_ppu_free_prod_nonce_action', '_mcmp_ppu_free_prod_nonce' );
echo '</div>';
// phpcs:enable Generic.WhiteSpace.ScopeIndent