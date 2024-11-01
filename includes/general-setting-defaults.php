<?php
/**
 * Adds default settings for general settings - Price per Unit
 *
 * @package PricePerUnit/Admin
 */

if ( ! defined( 'ABSPATH' ) ) :
	exit; // Exit if accessed directly.
endif;
// Is it new installation?
if ( false === get_option( '_mcmp_ppu_delete_meta' ) ) {
	// Do not process database upgrade for new installation.
	update_option( '_mcmp_ppu_free_db_version', MCMP_PPU_FREE_PLUGIN_VERSION );
}

add_option( '_mcmp_ppu_additional_text', '' );
add_option( '_mcmp_ppu_hide_sale_price', 'no' );
add_option( '_mcmp_ppu_var_prefix_text', '' );
add_option( '_mcmp_ppu_var_hide_max_price', 'no' );
add_option( '_mcmp_ppu_var_show_sale_price', 'no' );
add_option( '_mcmp_ppu_add_row_css', 'yes' );
add_option( '_mcmp_ppu_general', 'add' );
add_option( '_mcmp_ppu_single_page', 'add' );
add_option( '_mcmp_ppu_cart_page', 'single' );
add_option( '_mcmp_ppu_cart_show_sale_price', 'no' );
add_option( '_mcmp_ppu_recalc_text', '-automatic-' );
add_option( '_mcmp_ppu_recalc_text_automatic_preposition', '/' );
add_option( '_mcmp_ppu_recalc_text_options', '/kg|/oz|/g|/lbs' );
add_option( '_mcmp_ppu_recalc_text_separate', 'yes' );
add_option( '_mcmp_ppu_recalc_text_prefix', '' );
add_option( '_mcmp_ppu_disable_price_rounding', 'no' );
add_option( '_mcmp_ppu_delete_meta', false );
