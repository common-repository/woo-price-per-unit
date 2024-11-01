<?php
/**
 * Upgrades plugin database to the latest version
 *
 * @package PricePerUnit/Admin
 */

$used_db_version  = get_option( '_mcmp_ppu_free_db_version', '1.0.0' );
$require_approval = false;
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$update_approved = isset( $_REQUEST['mcmp-ppu-free-upgrade-db'] );
// General query posts quantity setting.
$posts_per_page = 200;
if ( version_compare( $used_db_version, '1.6', '<' ) ) {
	$old_opt = get_option( '_mcmp_ppu_general_behaviour' );
	if ( false !== $old_opt ) {
		$old_opt = ( empty( $old_opt ) ) ? 'not' : $old_opt;
		update_option( '_mcmp_ppu_general', $old_opt );
		delete_option( '_mcmp_ppu_general_behaviour' );
	}
	$old_opt = get_option( '_mcmp_ppu_single_page_behaviour' );
	if ( false !== $old_opt ) {
		$old_opt = ( empty( $old_opt ) ) ? 'not' : $old_opt;
		update_option( '_mcmp_ppu_single_page', $old_opt );
		delete_option( '_mcmp_ppu_single_page_behaviour' );
	}
}
if ( version_compare( $used_db_version, '2.0.8', '<' ) ) {
	add_option( '_mcmp_ppu_cart_show_sale_price', 'no' );
}
/**
 * General update manipulation
 */

// Approval required - create a message with link to approve on it.
if ( true == $require_approval ) {
	if ( false == $update_approved ) {
		$message = esc_html__( 'WooCommerce Price per Unit - database needs an update. Please back up your database first!', 'woo-price-per-unit' ) .
			'<p><a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=products&section=mcmp_price_pu&mcmp-ppu-free-upgrade-db' ) ) . '">' . esc_html__( 'Upgrade database', 'woo-price-per-unit' ) . '</a></p>';
		$this->mcmp_add_message( $message, 'warning', 'ppu-free-notice' );
	} else {
		$message = esc_html__( 'WooCommerce Price per Unit - database updated successfully.', 'woo-price-per-unit' );
		$this->mcmp_add_message( $message, 'success', 'ppu-free-notice' );
	}
}

// When approval not required or update already approved.
if ( false == $require_approval || true == $update_approved ) {
	update_option( '_mcmp_ppu_free_db_version', MCMP_PPU_FREE_PLUGIN_VERSION );
}

// Remove the approval after update.
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( isset( $_REQUEST['mcmp-ppu-free-upgrade-db'] ) ) {
	$url = home_url( remove_query_arg( 'mcmp-ppu-free-upgrade-db', false ) );
	wp_safe_redirect( $url );
	exit;
}
