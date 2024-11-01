<?php
/**
 * Displays custom settings in WooCommerce general settings under product - Price per unit
 *
 * @package PricePerUnit/Admin
 * @return array Settings for new tab in WooCommerce settings
 */

if ( ! defined( 'ABSPATH' ) ) :
	exit; // Exit if accessed directly.
endif;

$add_text_opts = get_option( '_mcmp_ppu_recalc_text_options' );
if ( ! empty( $add_text_opts ) ) {
	$add_text_opts_array = explode( '|', $add_text_opts );
	foreach ( $add_text_opts_array as $key => $value ) {
		$add_text_opts_array[ $key ] = esc_attr( $value );
	}
	// Adding texts as keys.
	$add_text_opts_array = array_combine( $add_text_opts_array, $add_text_opts_array );
}
$add_text_opts_array[' ']           = esc_attr__( 'No additional text', 'woo-price-per-unit' );
$add_text_opts_array['-automatic-'] = esc_attr__( 'Automatic text - takes unit settings from product', 'woo-price-per-unit' );
$add_text_curr                      = get_option( '_mcmp_ppu_recalc_text' );
$add_text_curr                      = ( empty( $add_text_curr ) ) ? ' ' : $add_text_curr;
if ( array_key_exists( $add_text_curr, $add_text_opts_array ) == false ) {
	$add_text_opts_array[ $add_text_curr ] = esc_attr( $add_text_curr );
}

ksort( $add_text_opts_array );
$custom_settings = array(
	array(
		'id'   => 'mcmp_general_options',
		'name' => esc_html__( 'General price options', 'woo-price-per-unit' ),
		'desc' => esc_html__( 'Settings which affects all products', 'woo-price-per-unit' ),
		'type' => 'title',
	),
	array(
		'id'          => '_mcmp_ppu_additional_text',
		'name'        => esc_html__( 'General price additional text', 'woo-price-per-unit' ),
		'desc'        => esc_html__( "This text will be shown after every price text. You can modify it's appearance through CSS class mcmp-general-price-suffix.", 'woo-price-per-unit' ),
		'placeholder' => esc_html__( 'Example "Without Vat"', 'woo-price-per-unit' ),
		'type'        => 'text',
		'default'     => '',
	),
	array(
		'id'       => '_mcmp_ppu_hide_sale_price',
		'name'     => esc_html__( 'Sale price - hide regular price', 'woo-price-per-unit' ),
		'desc'     => esc_html__( 'When product is on sale it shows regular price and sale price. This will hide the regular price for all products.', 'woo-price-per-unit' ),
		'type'     => 'checkbox',
		'default'  => 'no',
		'desc_tip' => false,
	),
	array(
		'type' => 'sectionend',
		'id'   => 'mcmp_general_options',
	),
	array(
		'id'   => 'mcmp_variable_options',
		'name' => esc_html__( 'Options for variable products', 'woo-price-per-unit' ),
		'desc' => esc_html__( 'These settings affect only variable products', 'woo-price-per-unit' ),
		'type' => 'title',
	),
	array(
		'id'          => '_mcmp_ppu_var_prefix_text',
		'name'        => esc_html__( 'Variations - prefix for variable price', 'woo-price-per-unit' ),
		'desc'        => esc_html__( "If the product is variable this text will be shown before the price. You can modify it's appearance through CSS class mcmp-variable-price-prefix.", 'woo-price-per-unit' ),
		'placeholder' => esc_html__( 'Example "From:"', 'woo-price-per-unit' ),
		'type'        => 'text',
		'default'     => '',
		'disabled'    => true,
	),
	array(
		'id'       => '_mcmp_ppu_var_hide_max_price',
		'name'     => esc_html__( 'Variations - Display only lower price', 'woo-price-per-unit' ),
		'desc'     => esc_html__( 'When displaying variation the price is displayed as "$10-$25". With this setting you will get just "$10"', 'woo-price-per-unit' ),
		'type'     => 'checkbox',
		'default'  => 'no',
		'desc_tip' => false,
	),
	array(
		'id'       => '_mcmp_ppu_var_show_sale_price',
		'name'     => esc_html__( 'Variations - Show regular price', 'woo-price-per-unit' ),
		'desc'     => esc_html__( 'Mimics pre WooCommerce 3 variable product price display - Shows how much was the cost before sale', 'woo-price-per-unit' ),
		'type'     => 'checkbox',
		'default'  => 'no',
		'desc_tip' => false,
	),
	array(
		'type' => 'sectionend',
		'id'   => 'mcmp_variable_options',
	),
	array(
		'id'   => 'mcmp_recalculation_options',
		'name' => esc_html__( 'Price recalculation', 'woo-price-per-unit' ),
		'desc' => esc_html__( 'General settings for price recalculation', 'woo-price-per-unit' ),
		'type' => 'title',
	),
	array(
		'id'       => '_mcmp_ppu_add_row_css',
		'name'     => esc_html__( 'New row different styling', 'woo-price-per-unit' ),
		'desc'     => esc_html__( 'When displaying price as new row, the new row will be displayed in italics with slightly smaller font size. For more styling you can use CSS class mcmp_recalc_price_row.', 'woo-price-per-unit' ),
		'type'     => 'checkbox',
		'default'  => 'no',
		'desc_tip' => false,
	),
	array(
		'id'       => '_mcmp_ppu_general',
		'name'     => esc_html__( 'Shop page price behavior', 'woo-price-per-unit' ),
		'desc'     => esc_html__( 'Behaviour of recalculated price on shop page', 'woo-price-per-unit' ),
		'css'      => '',
		'class'    => 'wc-enhanced-select',
		'type'     => 'select',
		'default'  => 'not',
		'options'  => array(
			'not'     => esc_attr__( 'Do not show recalculated price', 'woo-price-per-unit' ),
			'add'     => esc_attr__( 'Show recalculated price as new row', 'woo-price-per-unit' ),
			'replace' => esc_attr__( 'Replace price view with recalculated', 'woo-price-per-unit' ),
		),
		'desc_tip' => false,
	),
	array(
		'id'       => '_mcmp_ppu_single_page',
		'name'     => esc_html__( 'Single product page behavior', 'woo-price-per-unit' ),
		'desc'     => esc_html__( 'Behaviour of recalculated price on single product page', 'woo-price-per-unit' ),
		'css'      => '',
		'class'    => 'wc-enhanced-select',
		'type'     => 'select',
		'default'  => 'not',
		'options'  => array(
			'not'     => esc_attr__( 'Do not show recalculated price', 'woo-price-per-unit' ),
			'add'     => esc_attr__( 'Show recalculated price as new row', 'woo-price-per-unit' ),
			'replace' => esc_attr__( 'Replace price view with recalculated', 'woo-price-per-unit' ),
		),
		'desc_tip' => false,
	),
	array(
		'id'       => '_mcmp_ppu_cart_page',
		'name'     => esc_html__( 'Cart page behavior', 'woo-price-per-unit' ),
		'desc'     => esc_html__( 'Behaviour of recalculated price on cart page. Always displayed as new row.', 'woo-price-per-unit' ),
		'css'      => '',
		'class'    => 'wc-enhanced-select',
		'type'     => 'select',
		'default'  => 'single',
		'options'  => array(
			'not'    => esc_attr__( 'Do not show recalculated price', 'woo-price-per-unit' ),
			'add'    => esc_attr__( 'Show recalculated price always', 'woo-price-per-unit' ),
			'single' => esc_attr__( 'Show if displayed on Single product page', 'woo-price-per-unit' ),
			'shop'   => esc_attr__( 'Show if displayed on Shop page', 'woo-price-per-unit' ),

		),
		'desc_tip' => false,
	),
	array(
		'id'       => '_mcmp_ppu_cart_show_sale_price',
		'name'     => esc_html__( 'Cart - Sale products - show regular price', 'woo-price-per-unit' ),
		'desc'     => esc_html__( 'Show recalculated regular price in Cart. Up to version 2.0.7 it was displayed by default, but WooCommerce stopped displaying regular price, so do we.', 'woo-price-per-unit' ),
		'type'     => 'checkbox',
		'default'  => 'no',
		'desc_tip' => false,
	),
	array(
		'id'       => '_mcmp_ppu_recalc_text',
		'name'     => esc_html__( 'Recalculated price additional text', 'woo-price-per-unit' ),
		'desc'     => esc_html__( "Will be shown immediatelly after recalculated prices. Can be overriden in product editor. Will be shown ONLY when recalculation takes place. You can modify it's appearance through CSS class mcmp-recalc-price-suffix.", 'woo-price-per-unit' ),
		'css'      => '',
		'class'    => 'wc-enhanced-select',
		'type'     => 'select',
		'default'  => '',
		'options'  => $add_text_opts_array,
		'desc_tip' => false,
	),
	array(
		'id'          => '_mcmp_ppu_recalc_text_automatic_preposition',
		'name'        => esc_html__( 'Prepostion for weight unit when using automatic text', 'woo-price-per-unit' ),
		'desc'        => esc_html__( "Automatic text will create text like this '$10/kg' with this option you can replace '/' sign with what you want.", 'woo-price-per-unit' ) . ' ' . esc_html__( 'To add space replace it with "%" sign.', 'woo-price-per-unit' ),
		'type'        => 'text',
		'placeholder' => esc_html__( 'Example "/" or "per%".', 'woo-price-per-unit' ) . ' ' . esc_html__( 'To add space replace it with "%" sign.', 'woo-price-per-unit' ),
		'default'     => '/',
	),
	array(
		'id'      => '_mcmp_ppu_recalc_text_options',
		'name'    => esc_html__( 'Predefined additional text values', 'woo-price-per-unit' ),
		'desc'    => esc_html__( "Set of additional texts from which you will be able to choose on product page. Multiple values can be separated with '|' sign.", 'woo-price-per-unit' ),
		'type'    => 'text',
		'default' => '/kg|/oz|/g|/lbs',
	),
	array(
		'id'       => '_mcmp_ppu_recalc_text_separate',
		'name'     => esc_html__( 'Separate text from price with space', 'woo-price-per-unit' ),
		'desc'     => esc_html__( "The additional text is normally separated from the recalculated price with unbreakable space. If you don't want it uncheck this option.", 'woo-price-per-unit' ),
		'type'     => 'checkbox',
		'default'  => 'yes',
		'desc_tip' => false,
	),
	array(
		'id'          => '_mcmp_ppu_recalc_text_prefix',
		'name'        => esc_html__( 'Recalculated price prefix text', 'woo-price-per-unit' ),
		'desc'        => esc_html__( "Will be shown before the recalculated prices. Can be overriden in product editor. Will be shown ONLY when recalculation takes place. You can modify it's appearance through CSS class mcmp-recalc-price-prefix.", 'woo-price-per-unit' ),
		'type'        => 'text',
		'placeholder' => esc_html__( 'Example "Price per kilogram:"', 'woo-price-per-unit' ),
		'default'     => '',
	),
	array(
		'id'       => '_mcmp_ppu_disable_price_rounding',
		'name'     => __( 'Disable price rounding', 'woo-price-per-unit' ),
		'desc'     => __( 'Normally WooCommerce rounds displayed prices - if you want recalculated prices trimmed instead check this field.', 'woo-price-per-unit' ),
		'type'     => 'checkbox',
		'default'  => 'no',
		'desc_tip' => false,
	),
	array(
		'type' => 'sectionend',
		'id'   => 'mcmp_recalculation_options',
	),
	array(
		'id'   => 'mcmp_plugin_options',
		'name' => esc_html__( 'Maintenance', 'woo-price-per-unit' ),
		'desc' => esc_html__( 'These settings are related just to the plugin itself.', 'woo-price-per-unit' ),
		'type' => 'title',
	),
	array(
		'id'       => '_mcmp_ppu_delete_meta',
		'name'     => esc_html__( 'Delete settings on uninstall', 'woo-price-per-unit' ),
		'desc'     => esc_html__( 'With this setting you will delete all plugin settings on uninstallation.', 'woo-price-per-unit' ),
		'type'     => 'checkbox',
		'default'  => 'no',
		'desc_tip' => false,
	),
	array(
		'type' => 'sectionend',
		'id'   => 'mcmp_plugin_options',
	),
);
return $custom_settings;
