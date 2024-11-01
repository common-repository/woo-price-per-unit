<?php
/**
 * Delete WooCommerce Unit Of Measure data if plugin is deleted.
 *
 * @package PricePerUnit
 * @since 1.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) :
	exit;
endif;
if ( get_option( '_mcmp_ppu_delete_meta' ) == 'yes' ) {
	delete_post_meta_by_key( '_mcmp_ppu_general_override' );
	delete_post_meta_by_key( '_mcmp_ppu_single_page_override' );
	delete_post_meta_by_key( '_mcmp_ppu_recalc_text_override' );
	delete_post_meta_by_key( '_mcmp_ppu_additional_text_override' );
	delete_post_meta_by_key( '_mcmp_ppu_ratio_unit_override' );
	delete_post_meta_by_key( '_mcmp_ppu_cust_num_of_units_override' );
	delete_post_meta_by_key( '_mcmp_ppu_var_prefix_text_override' );
	// Meta not used in free version anymore.
	delete_post_meta_by_key( '_mcmp_ppu_recalc_per_units_override' );
	delete_option( '_mcmp_ppu_additional_text' );
	delete_option( '_mcmp_ppu_hide_sale_price' );
	delete_option( '_mcmp_ppu_var_prefix_text' );
	delete_option( '_mcmp_ppu_var_hide_max_price' );
	delete_option( '_mcmp_ppu_var_show_sale_price' );
	delete_option( '_mcmp_ppu_add_row_css' );
	delete_option( '_mcmp_ppu_general' );
	delete_option( '_mcmp_ppu_single_page' );
	delete_option( '_mcmp_ppu_cart_page' );
	delete_option( '_mcmp_ppu_cart_show_sale_price' );
	delete_option( '_mcmp_ppu_recalc_text' );
	delete_option( '_mcmp_ppu_recalc_text_automatic_preposition' );
	delete_option( '_mcmp_ppu_recalc_text_options' );
	delete_option( '_mcmp_ppu_recalc_text_separate' );
	delete_option( '_mcmp_ppu_recalc_text_prefix' );
	delete_option( '_mcmp_ppu_disable_price_rounding' );
	delete_option( '_mcmp_ppu_delete_meta' );
	// Options not used in free version anymore.
	delete_option( '_mcmp_ppu_recalc_per_units' );
	delete_option( '_mcmp_ppu_var_display_option_recalc' );
	delete_option( '_mcmp_ppu_var_display_option_recalc_forced' );
}
