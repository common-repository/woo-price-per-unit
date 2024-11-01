<?php
/**
 * Saves custom product settings
 *
 * @var integer $post_id Id of the post where setting will be saved
 * @package PricePerUnit/Admin
 */

if ( ! defined( 'ABSPATH' ) ) :
	exit; // Exit if accessed directly.
endif;

if ( isset( $_POST['_mcmp_ppu_free_prod_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_mcmp_ppu_free_prod_nonce'] ) ), 'mcmp_ppu_free_prod_nonce_action' ) ) {
	$option = isset( $_POST['_mcmp_ppu_general_override'] ) ? sanitize_text_field( wp_unslash( $_POST['_mcmp_ppu_general_override'] ) ) : '';
	update_post_meta( $post_id, '_mcmp_ppu_general_override', $option );
	$option = isset( $_POST['_mcmp_ppu_single_page_override'] ) ? sanitize_text_field( wp_unslash( $_POST['_mcmp_ppu_single_page_override'] ) ) : '';
	update_post_meta( $post_id, '_mcmp_ppu_single_page_override', $option );
}
