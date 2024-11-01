<?php
/**
 * Plugin Name: WooCommerce Price Per Unit
 * Plugin URI: https://mechcomp.cz/price-per-unit-pro/
 * Description: WooCommerce Price Per Unit allows the user to show prices recalculated per units(weight) and do some customization to the look of the prices
 * Version: 2.2.4
 * Author: Martin Mechura
 * Author URI: http://mechcomp.cz
 * Text Domain: woo-price-per-unit
 * WC tested up to: 8.8.3
 * WC requires at least: 3.0
 *
 * @package PricePerUnit
 *
 * WooCommerce Price Per Unit. A Plugin that works with the WooCommerce plugin for WordPress.
 * Copyright (C) 2017 Martin Mechura
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.html.
 */

if ( ! defined( 'ABSPATH' ) ) :
	exit; // Exit if accessed directly.
endif;
define( 'MCMP_PPU_FREE_PLUGIN_VERSION', '2.2.4' );
define( 'MCMP_PPU_FREE_PLUGIN_FILE', __FILE__ );
define( 'MCMP_PPU_FREE_PLUGIN_DIR', plugin_dir_path( MCMP_PPU_FREE_PLUGIN_FILE ) );

/**
 * MCMP PPU Pro Class
 */
class MCMP_PPU {

	/**
	 * Instance of the class
	 *
	 * @var mixed
	 */
	private static $instance = null;

	/**
	 * Product ID for single page
	 *
	 * @var integer
	 */
	private $single_pr_id = 0;

	/**
	 * WooCommerce version
	 *
	 * @var string
	 */
	public $woo_version = '';

	/**
	 * Dependent on
	 *
	 * @var mixed dependency
	 */
	public $dependency = null;

	/**
	 * Default hook priority
	 *
	 * @var integer
	 */
	public $hook_priority = 1010;

	/**
	 * Returns Class instance
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * The Constructor!
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_activation_check' ) );
		if ( $this->dependency_checks() !== true ) {
			return;
		}
		// Loading translation.
		add_action( 'init', array( $this, 'plugin_init' ) );
		// Action on plugin activation.
		register_activation_hook( MCMP_PPU_FREE_PLUGIN_FILE, array( $this, 'plugin_activated' ) );
		// Claim Woo HPOS compatibility.
		add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
		// Database upgrades.
		add_action( 'current_screen', array( $this, 'perform_db_upgrade' ) );
		// Loading style-sheet.
		add_action( 'wp_enqueue_scripts', array( $this, 'load_style' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_style' ) );
		// Display admin messages.
		add_action( 'admin_notices', array( $this, 'display_stored_messages' ), 50, 0 );
		add_filter( 'woocommerce_get_sections_products', array( $this, 'add_general_options_section' ) );
		add_filter( 'woocommerce_get_settings_products', array( $this, 'general_options' ), 10, 2 );
		// Loading hooks on frontend.
		add_action( 'after_setup_theme', array( $this, 'add_frontend_hooks' ), $this->hook_priority );
		// Adding single product options tab.
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_custom_product_options_tab' ), 99, 1 );
		// Adding single product options.
		add_action( 'woocommerce_product_data_panels', array( $this, 'product_options' ) );
		// Saving single product options.
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_options' ), 30 );
		// Extending plugin actions.
		add_filter( 'plugin_action_links_' . plugin_basename( MCMP_PPU_FREE_PLUGIN_FILE ), array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Hooks all frontend code
	 *
	 * @since 2.2.4
	 * @return void
	 */
	public function add_frontend_hooks() {
		/** Front proccessing allowed
		 *
		 * @since 2.2.4
		 */
		if ( true == apply_filters( 'mcmp_ppu_process_front_end', true ) ) {
			// Render the ppu field output on the front-end.
			add_filter( 'woocommerce_get_price_html', array( $this, 'custom_price' ), $this->hook_priority, 2 );
			add_filter( 'woocommerce_cart_item_price', array( $this, 'custom_cart_price' ), $this->hook_priority, 3 );
			// helper for getting single product ID.
			add_action( 'woocommerce_before_single_product', array( $this, 'get_single_id' ) );
		}
	}

	/**
	 * Checks woocommerce version and remembers for later purpose
	 *
	 * @param string $compare_version Version to check against, default is 3.0.
	 * @param string $compare_operator Operator used for comparison..
	 * @return boolean is the WooCommerce version greater than $compare_version
	 * @since 1.5
	 */
	public function check_woo_version( $compare_version = '3.0', $compare_operator = '>=' ) {
		$version = $this->woo_version;
		if ( empty( $version ) ) {
			$version           = (string) get_option( 'woocommerce_version' );
			$this->woo_version = $version;
		}
		/**
		 * Psalm suppression
		 *
		 * @psalm-suppress ArgumentTypeCoercion */
		// phpcs:ignore Generic.Commenting.DocComment.MissingShort
		if ( version_compare( $version, $compare_version, $compare_operator ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Displays admin notice - when in WooCommerce 4 it handles the display through transients and function "display_stored_messages"
	 *
	 * @param mixed  $message Message to output.
	 * @param string $type Type of the message.
	 * @param string $transient Name of transient to store the message.
	 * @return void
	 * @since 1.8
	 * @see display_stored_messages()
	 */
	public function mcmp_add_message( $message, $type, $transient = '' ) {
		if ( empty( $message ) || ! is_string( $message ) ) {
			return;
		}
		$class = 'notice is-dismissible ';
		switch ( $type ) {
			case 'success':
			case 'warning':
			case 'error':
			case 'info':
				$class .= 'notice-' . $type;
				break;
			default:
				$class .= 'notice-info';
				break;
		}
		$output = '<div class="' . $class . '"><p>' . wp_kses_post( $message ) . '</p></div>';
		if ( ! empty( $transient ) ) {
			$notice_num = 0;
			$trans_num  = $transient . $notice_num;
			while ( get_transient( $trans_num ) != false ) {
				$trans_num = $transient . ( ++$notice_num );
			}
			set_transient( $trans_num, $output, 60 );
		} else {
			echo wp_kses_post( $output );
		}
	}

	/**
	 * Displays message stored in transient
	 *
	 * @param string $transient - Name of the transient holding the message.
	 * @return void
	 * @since 2.0.1
	 */
	public function display_stored_messages( $transient = 'ppu-free-notice' ) {
		$notice_num = 0;
		$trans_num  = $transient . $notice_num;
		$message    = get_transient( $trans_num );
		while ( false != $message ) {
			delete_transient( $trans_num );
			echo wp_kses_post( $message );
			$trans_num = $transient . ( ++$notice_num );
			$message   = get_transient( $trans_num );
		}
	}

	/**
	 * Truncates number to $precision decimal points
	 *
	 * @param string|float $number Number to truncate.
	 * @param integer      $precision Number of digits after decimal point.
	 * @return float trimmed number
	 * @since 1.9.5
	 */
	public function truncate_number( $number, $precision = 0 ) {
		if ( function_exists( 'bcadd' ) ) {
			/**
			 * Psalm suppression
			 *
			 * @psalm-suppress ArgumentTypeCoercion */
			// phpcs:ignore Generic.Commenting.DocComment.MissingShort
			return floatval( bcadd( strval( $number ), '0', $precision ) );
		} else {
			return floatval( preg_replace( '/\.(\d{' . intval( $precision ) . '}).*/', '.$1', $number ) );
		}
	}

	/**
	 * Load plugin's textdomain
	 *
	 * @since 1.0
	 */
	public function plugin_init() {
		load_plugin_textdomain( 'woo-price-per-unit', false, dirname( plugin_basename( MCMP_PPU_FREE_PLUGIN_FILE ) ) . '/languages/' );
	}

	/**
	 * Checks if requirements for activation are met
	 * Now it tests if free version is active
	 *
	 * @return mixed String with error message or true when dependency checks are all-right
	 * @since 1.9
	 */
	public function dependency_checks() {
		if ( ! is_null( $this->dependency ) ) {
			return $this->dependency;
		}
		if ( in_array( 'woo-price-per-unit-pro/woo-price-per-unit.php', (array) get_option( 'active_plugins' ), true )
			|| ( is_multisite() && array_key_exists( 'woo-price-per-unit-pro/woo-price-per-unit.php', (array) get_site_option( 'active_sitewide_plugins' ) ) ) ) {
				$this->dependency = esc_html__( 'Price per unit plugin - PRO version is active. Please deactivate it first.', 'woo-price-per-unit' );
				return $this->dependency;
		}
		$this->dependency = true;
		return $this->dependency;
	}

	/**
	 * Prevents activation of the plugin when dependency checks fail
	 *
	 * @return void
	 * @since 1.9
	 */
	public function admin_activation_check() {
		$dependency_info = $this->dependency_checks();
		if ( true !== $dependency_info ) {
			if ( is_plugin_active( plugin_basename( MCMP_PPU_FREE_PLUGIN_FILE ) ) ) {
				deactivate_plugins( plugin_basename( MCMP_PPU_FREE_PLUGIN_FILE ) );
				$type = 'error';
				// Passing arguments into add_action by using PHP lambda - nice workaround.
				add_action(
					'admin_notices',
					function () use ( $dependency_info, $type ) {
						$this->mcmp_add_message( $dependency_info, $type );
					}
				);
				// phpcs:disable WordPress.Security.NonceVerification.Recommended
				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
				// phpcs:enable WordPress.Security.NonceVerification.Recommended
			}
		}
	}

	/**
	 * Action on plugin activation - currently setting defaults
	 * Checks also the conditions for plugin activation - if not it prevents the activation
	 *
	 * @return void
	 * @since 1.5
	 */
	public function plugin_activated() {
		$dependency_info = $this->dependency_checks();
		if ( true !== $dependency_info ) {
			deactivate_plugins( plugin_basename( MCMP_PPU_FREE_PLUGIN_FILE ) );
			wp_die( esc_html__( 'Plugin NOT activated: ', 'woo-price-per-unit' ) . wp_kses_post( $dependency_info ) );
		}
		include MCMP_PPU_FREE_PLUGIN_DIR . 'includes/general-setting-defaults.php';
	}

	/**
	 * Declares compatibility with WooCommerce HPOS feature
	 *
	 * @since 2.2.2
	 *
	 * @return void
	 */
	public function declare_hpos_compatibility() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', MCMP_PPU_FREE_PLUGIN_FILE, true );
		}
	}

	/**
	 * Performs db upgrade on plugin update
	 *
	 * @return void
	 * @since 2.0.8
	 */
	public function perform_db_upgrade() {
		$used_db_version = get_option( '_mcmp_ppu_free_db_version', '1.0.0' );
		if ( MCMP_PPU_FREE_PLUGIN_VERSION != $used_db_version ) {
			include MCMP_PPU_FREE_PLUGIN_DIR . 'includes/database-upgrade.php';
		}
	}

	/**
	 * Load plugin's CSS style
	 *
	 * @return void
	 * @since 1.3
	 */
	public function load_style() {
		wp_enqueue_style( 'mcmp_price_per_unit_style', plugins_url( '/assets/CSS/woo-ppu-default-style.css', MCMP_PPU_FREE_PLUGIN_FILE ), array(), MCMP_PPU_FREE_PLUGIN_VERSION );
		if ( get_option( '_mcmp_ppu_add_row_css' ) == 'yes' ) {
			wp_enqueue_style( 'mcmp_price_per_altered_style', plugins_url( '/assets/CSS/woo-ppu.css', MCMP_PPU_FREE_PLUGIN_FILE ), array(), MCMP_PPU_FREE_PLUGIN_VERSION );
		}
	}

	/**
	 * Load plugin's admin styles and scripts
	 *
	 * @param string $hook - Where is action executed.
	 * @return void
	 * @since 1.6
	 */
	public function load_admin_style( $hook ) {
		wp_register_script( 'mcmp-ppu-admin', plugins_url( '/assets/JS/woo-ppu-admin.js', MCMP_PPU_FREE_PLUGIN_FILE ), array( 'jquery' ), MCMP_PPU_FREE_PLUGIN_VERSION, false );
		wp_enqueue_script( 'mcmp-ppu-admin' );
		wp_enqueue_style( 'mcmp_price_per_unit_admin_style', plugins_url( '/assets/CSS/woo-ppu-admin.css', MCMP_PPU_FREE_PLUGIN_FILE ), array(), MCMP_PPU_FREE_PLUGIN_VERSION );
	}

	/**
	 * Add settings link
	 *
	 * @param array $links - Action links.
	 * @return array HTML output into plugin info
	 * @since 1.0
	 */
	public function plugin_action_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=products&section=mcmp_price_pu' ) . '">' . esc_html__( 'Settings', 'woo-price-per-unit' ) . '</a>',
		);
		return array_merge( $plugin_links, $links );
	}

	/**
	 * Adds tab to product options
	 *
	 * @param array $product_opt_tabs Newly created tab.
	 * @return array
	 * @since 1.0
	 */
	public function add_custom_product_options_tab( $product_opt_tabs ) {
		$product_opt_tabs['price-per-unit'] = array(
			'label'  => esc_html__( 'Price Per Unit', 'woo-price-per-unit' ), // translatable.
			'target' => 'mcmp_ppu_options', // translatable.
		);
		return $product_opt_tabs;
	}

	/**
	 * Outputs product options.
	 *
	 * @param array $atts Product settings.
	 * @return void
	 * @since 1.0
	 */
	public function product_options( $atts ) {
		include MCMP_PPU_FREE_PLUGIN_DIR . 'includes/product-settings.php';
	}

	/**
	 * Update the database with the new options
	 *
	 * @param integer $post_id Id of the post where setting will be saved.
	 * @return void
	 * @since 2.2.4
	 */
	public function save_product_options( $post_id ) {
		include MCMP_PPU_FREE_PLUGIN_DIR . 'includes/product-settings-save.php';
	}

	/**
	 * Add Price per Unit settings section under the Products tab.
	 *
	 * @param array $sections List of sections.
	 * @return array
	 * @since 1.0
	 */
	public function add_general_options_section( $sections ) {
		$sections['mcmp_price_pu'] = esc_html__( 'Price Per Unit - free version', 'woo-price-per-unit' );
		return $sections;
	}

	/**
	 * Populates Price per Unit settings section under the Products tab.
	 *
	 * @param array  $settings Settings of WooCommerce.
	 * @param string $current_section Which section is populated right now.
	 * @return array
	 * @since 1.0
	 */
	public function general_options( $settings, $current_section ) {
		if ( 'mcmp_price_pu' == $current_section ) {
			$cust_settings = include MCMP_PPU_FREE_PLUGIN_DIR . 'includes/general-settings.php';
			return $cust_settings;
			// If not, return the standard settings.
		} else {
			return $settings;
		}
	}

	/**
	 * Saves product id from single product view
	 * needed for determining if not running from widget on single page
	 *
	 * @return void
	 * @since 1.0
	 */
	public function get_single_id() {
		global $product;
		if ( is_object( $product ) ) {
			$this->single_pr_id = $product->get_id();
		}
	}

	/**
	 * Checks if the product is valid and then returns it's ID
	 *
	 * @since 2.0
	 * @param mixed   $product_to_inspect which product to check.
	 * @param boolean $get_parent whether to get parent product ID.
	 * @return mixed ID of the verified product of false when wrong Product
	 */
	public function check_product_id( $product_to_inspect, $get_parent = false ) {
		$product_id = false;
		if ( ! empty( $product_to_inspect ) ) {
			if ( is_numeric( $product_to_inspect ) ) {
				$product_to_inspect = wc_get_product( $product_to_inspect );
			}
			if ( is_object( $product_to_inspect ) ) {
				$product_id = $product_to_inspect->get_id();
				if ( true == $get_parent && 'variation' === $product_to_inspect->get_type() ) {
					$product_id = $product_to_inspect->get_parent_id();
				}
			}
		}
		return $product_id;
	}

	/**
	 * Gets option with product option overrides
	 * Works with product meta to check for option, if value is empty proceed to general options
	 *
	 * @since 1.6
	 * @param string  $option which option to check.
	 * @param integer $product_id_to_inspect which product to check - supposed to be checked first.
	 * @param mixed   $def value which should be passed if nothing is found.
	 * @return string
	 */
	public function get_option_override( $option, $product_id_to_inspect, $def = '' ) {
		$product_override = $option . '_override';
		$option_val       = '';
		// Getting parent product ID of $product_id_to_inspect.
		$product_id = $this->check_product_id( $product_id_to_inspect, true );
		// Product options.
		if ( is_numeric( $product_id ) ) {
			$option_val = get_post_meta( $product_id, $product_override, true );
		}
		// General options.
		if ( empty( $option_val ) ) {
			$option_val = get_option( $option );
		}
		// Everything failed and we have default? Place it there. The ifs order is this because default is rarely used.
		if ( ! empty( $def ) && empty( $option_val ) ) {
			$option_val = $def;
		}
		return $option_val;
	}

	/**
	 * Gets behaviour of recalculation
	 * The product is checked - on problems returns false
	 *
	 * @since 1.6
	 * @param mixed  $product_to_inspect product id of product object.
	 * @param string $special_case Alter the behaviour on special occasion.
	 * @return mixed false if should not recalculate or string type of behaviour for recalculation
	 */
	public function get_recalc_behaviour( $product_to_inspect, $special_case = false ) {
		$behav      = '';
		$product_id = $this->check_product_id( $product_to_inspect, true );
		if ( empty( $product_id ) ) {
			return false;
		}
		switch ( $special_case ) {
			case 'cart':
				$option = get_option( '_mcmp_ppu_cart_page' );
				switch ( $option ) {
					case 'single':
						$option = '_mcmp_ppu_single_page';
						break;
					case 'shop':
						$option = '_mcmp_ppu_general';
						break;
					default:
						$behav = $option;
						break;
				}
				break;

			default:
				$option = '_mcmp_ppu_general';
				// Determine whether to recalculate or not - depending also on override.
				// Single product page.
				if ( is_product() ) {
					// Limit the archive filter.
					if ( ! doing_filter( 'woocommerce_after_shop_loop_item_title' ) ) {
						// Do we have memorized the id? If not ignore it - might also be missing hook in the theme.
						// Is it That product or some widget product?
						if ( empty( $this->single_pr_id ) || $product_id === $this->single_pr_id ) {
								$option = '_mcmp_ppu_single_page';
						}
					}
				}
				break;
		}
		$behav = empty( $behav ) ? $this->get_option_override( $option, $product_id ) : $behav;
		$behav = ( 'not' === $behav ) ? false : $behav;
		return $behav;
	}

	/**
	 * Render additional recalculated text and wrap it around the original price
	 *
	 * @param integer $product_id_to_inspect Product which will receive the additional text - supposed to be checked first.
	 * @param mixed   $price_text Recalculated price.
	 * @param boolean $row_wrapper Should be a recalc row wrapper?.
	 * @return string Automatic additional text
	 * @since 1.9.3
	 */
	public function render_recalc_text( $product_id_to_inspect, $price_text = '', $row_wrapper = false ) {

		$pre_text = $this->get_option_override( '_mcmp_ppu_recalc_text_prefix', $product_id_to_inspect );
		// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
		$pre_text = empty( $pre_text ) ? '' : esc_html_x( $pre_text, 'Recalculated price prefix text', 'woo-price-per-unit' );
		$suf_text = $this->get_option_override( '_mcmp_ppu_recalc_text', $product_id_to_inspect );
		$suf_text = ( empty( $suf_text ) || ' ' == $suf_text ) ? '' : $suf_text;
		if ( '-automatic-' == $suf_text ) {
			$suf_text = get_option( '_mcmp_ppu_recalc_text_automatic_preposition', '/' );
			// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
			$suf_text         = _x( $suf_text, 'Preposition for weight unit when using automatic text', 'woo-price-per-unit' );
			$suf_text         = ! empty( $suf_text ) ? '<span class="mcmp-recalc-price-suffix-preposition">' . esc_html( str_replace( '%', ' ', $suf_text ) ) . '</span>' : '';
			$recalc_per_units = $this->get_option_override( '_mcmp_ppu_recalc_per_units', $product_id_to_inspect, 1 );
			if ( 1 != $recalc_per_units ) {
				$suf_text .= $recalc_per_units . ' ';
			}
			$ratio_unit = $this->get_option_override( '_mcmp_ppu_ratio_unit', $product_id_to_inspect );
			if ( empty( $ratio_unit ) ) {
				$ratio_unit = get_option( 'woocommerce_weight_unit' );
			}
			switch ( $ratio_unit ) {
				case 'kg':
					$ratio_unit = esc_html( _nx( 'kg', 'kg', $recalc_per_units, 'weight unit', 'woo-price-per-unit' ) );
					break;
				case 'g':
					$ratio_unit = esc_html( _nx( 'g', 'g', $recalc_per_units, 'weight unit', 'woo-price-per-unit' ) );
					break;
				case 'oz':
					$ratio_unit = esc_html( _nx( 'oz', 'oz', $recalc_per_units, 'weight unit', 'woo-price-per-unit' ) );
					break;
				case 'lbs':
					$ratio_unit = esc_html( _nx( 'lb', 'lbs', $recalc_per_units, 'weight unit', 'woo-price-per-unit' ) );
					break;
				default:
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					$ratio_unit = esc_html_x( $ratio_unit, 'Custom unit name', 'woo-price-per-unit' );
			}
			$suf_text .= $ratio_unit;
		} elseif ( ! empty( $suf_text ) ) {
			// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
			$suf_text = esc_html_x( $suf_text, 'Recalculated price suffix text', 'woo-price-per-unit' );
		}
		if ( ! empty( $pre_text ) ) {
			$separator = '&nbsp;';
			$pre_text  = '<span class="woocommerce-Price-currencySymbol amount mcmp-recalc-price-prefix">' . $pre_text . '</span>' . $separator;
		}
		if ( ! empty( $suf_text ) ) {
			$separator = get_option( '_mcmp_ppu_recalc_text_separate' );
			$separator = ( 'no' == $separator ) ? '' : '&nbsp;';
			$suf_text  = $separator . '<span class="woocommerce-Price-currencySymbol amount mcmp-recalc-price-suffix">' . $suf_text . '</span>';
		}
		if ( true == $row_wrapper ) {
			$pre_text  = '<br><span class="mcmp_recalc_price_row">' . $pre_text;
			$suf_text .= '</span>';
		}

		return $pre_text . $price_text . $suf_text;
	}

	/**
	 * Render cart and mini-cart price.
	 * Filter woocommerce_cart_item_price
	 *
	 * @param string  $price_text Original text.
	 * @param array   $product_data Information about the product in the cart.
	 * @param integer $cart_key Id of the cart.
	 * @since 1.6
	 * @return string recalculated $price + custom string
	 */
	public function custom_cart_price( $price_text, $product_data, $cart_key ) {
		global $woocommerce;

		if ( is_null( $product_data ) ) {
			return $price_text;
		}
		$product_id = ( empty( $product_data['variation_id'] ) ) ? $product_data['product_id'] : $product_data['variation_id'];
		// get_recalc_behaviour also checks the product ID for validity - no need to check it again.
		$behav = $this->get_recalc_behaviour( $product_id, 'cart' );
		if ( empty( $behav ) ) {
			return $price_text;
		}
		$product = $product_data['data'];
		$weight  = $this->get_option_override( '_mcmp_ppu_cust_num_of_units', $product_id );
		if ( empty( $weight ) ) {
			$weight = $product->get_weight();
		}
		$weight       = floatval( $weight );
		$normal_price = floatval( $product->get_price() );
		if ( empty( $weight ) || empty( $normal_price ) ) {
			return $price_text;
		}
		$round_prices = get_option( '_mcmp_ppu_disable_price_rounding' ) == 'yes' ? false : true;
		// Pre Woo 3.3.0 compatibility - display_prices_including_tax is declared there.
		if ( method_exists( $woocommerce->cart, 'display_prices_including_tax' ) ) {
			$incl_tax = $woocommerce->cart->display_prices_including_tax();
		} else {
			$incl_tax = 'excl' !== $woocommerce->cart->tax_display_cart;
		}
		if ( $incl_tax ) {
			$normal_price = wc_get_price_including_tax( $product, array( 'price' => $normal_price ) );
		} else {
			$normal_price = wc_get_price_excluding_tax( $product, array( 'price' => $normal_price ) );
		}
		$normal_price = $normal_price / $weight;
		$normal_price = $this->price_ratio_calc( $normal_price, $product_id );
		if ( false == $round_prices ) {
			$wc_decimals  = wc_get_price_decimals();
			$normal_price = $this->truncate_number( $normal_price, $wc_decimals );
		}
		$recalc = wc_price( $normal_price );
		if ( 'yes' == get_option( '_mcmp_ppu_cart_show_sale_price' ) ) {
			if ( $product->is_on_sale() ) {
				$regular_price = floatval( $product->get_regular_price() );
				if ( $incl_tax ) {
					$regular_price = wc_get_price_including_tax( $product, array( 'price' => $regular_price ) );
				} else {
					$regular_price = wc_get_price_excluding_tax( $product, array( 'price' => $regular_price ) );
				}
				$regular_price = $regular_price / $weight;
				$regular_price = $this->price_ratio_calc( $regular_price, $product_id );
				if ( false == $round_prices ) {
					$regular_price = $this->truncate_number( $regular_price, $wc_decimals );
				}
				$recalc = '<del>' . wc_price( $regular_price ) . '</del>&nbsp;<ins>' . $recalc . '</ins>';
			}
		}
		$recalc = $this->render_recalc_text( $product_id, $recalc, true );
		return $price_text . $recalc;
	}

	/**
	 * Price ratio calculation - product id is not verified, supposedly it should be already verified
	 * takes custom ratio and calculate it in - the price should be already divided by weight(cannot be here because of variable products calculation)
	 *
	 * @since 1.7
	 * @param float   $price - price which should be recalculated.
	 * @param integer $product_id_to_inspect - id of the product - supposed to be checked first.
	 * @return float recalculated price
	 */
	public function price_ratio_calc( $price, $product_id_to_inspect ) {
		$cust_ratio       = 1;
		$recalc_per_units = $this->get_option_override( '_mcmp_ppu_recalc_per_units', $product_id_to_inspect, 1 );
		$recalc_per_units = floatval( $recalc_per_units );
		$ratio_unit       = $this->get_option_override( '_mcmp_ppu_ratio_unit', $product_id_to_inspect );
		if ( ! empty( $ratio_unit ) ) {
			$current_unit = get_option( 'woocommerce_weight_unit' );
			$cust_ratio   = wc_get_weight( 1, $current_unit, $ratio_unit );
		}
		$price *= $cust_ratio * $recalc_per_units;
		return $price;
	}

	/**
	 * Modifies the general price text
	 *
	 * @param integer $product_id_to_inspect Product for which the text will be altered - supposed to be checked first.
	 * @param mixed   $price_text Recalculated price.
	 * @return string Altered general price text
	 * @since 2.0.0
	 */
	public function general_price_manipulation( $product_id_to_inspect, $price_text = '' ) {
		$product         = wc_get_product( $product_id_to_inspect );
		$product_type    = $product->get_type();
		$var_prefix_text = '';
		switch ( $product_type ) {
			case 'simple':
				$hide_sale = get_option( '_mcmp_ppu_hide_sale_price' ) == 'yes' ? true : false;
				if ( true == $hide_sale && $product->is_on_sale() ) {
					$price_text = floatval( $product->get_price() );
					if ( ! empty( $price_text ) ) {
						$price_text = wc_get_price_to_display( $product, array( 'price' => $price_text ) );
					}
					$price_text = wc_price( $price_text );
				}
				break;
			case 'variable':
				// hide variable max price?
				$hide_max = get_option( '_mcmp_ppu_var_hide_max_price' ) == 'yes' ? true : false;
				if ( true == $hide_max ) {
					// needles to remake the price?
					$variable_price_min = floatval( $product->get_variation_price( 'min', true ) );
					$price_text         = wc_price( $variable_price_min );
				}
				if ( $product->is_on_sale() ) {
					$show_sale_price = get_option( '_mcmp_ppu_var_show_sale_price' ) == 'yes' ? true : false;
					if ( true == $show_sale_price ) {
						$sale_price_present = strpos( $price_text, '<del' );
						// is sale price already present?
						if ( false !== $sale_price_present ) {
							$show_sale_price = false;
						}
					}
					if ( true == $show_sale_price ) {
						$price_text                 = '<ins>' . $price_text . '</ins>';
						$variable_regular_price_min = floatval( $product->get_variation_regular_price( 'min', true ) );
						$price_regular              = '<del>' . wc_price( $variable_regular_price_min );
						if ( false == $hide_max ) {
							$variable_regular_price_max = floatval( $product->get_variation_regular_price( 'max', true ) );
							if ( $variable_regular_price_min !== $variable_regular_price_max ) {
								$price_regular .= '–' . wc_price( $variable_regular_price_max );
							}
						}
						$price_regular .= '</del>';
						$price_text     = $price_regular . $price_text;
					}
				}
				// fill prefix text for variables.
				$var_prefix_text = get_option( '_mcmp_ppu_var_prefix_text' );
				if ( ! empty( $var_prefix_text ) ) {
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					$var_prefix_text = esc_html_x( $var_prefix_text, 'Variations - variable price prefix', 'woo-price-per-unit' );
					$var_prefix_text = '<span class="woocommerce-Price-currencySymbol amount mcmp-variable-price-prefix">' . $var_prefix_text . ' </span>';
					$price_text      = $var_prefix_text . $price_text;
				}
				break;
		}
		$add_text = $this->get_option_override( '_mcmp_ppu_additional_text', $product_id_to_inspect );
		if ( ! empty( $add_text ) ) {
			// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
			$add_text  = esc_html_x( $add_text, 'General price suffix text', 'woo-price-per-unit' );
			$separator = get_option( '_mcmp_ppu_recalc_text_separate' ) == 'no' ? '' : ' ';
			$add_text  = '<span class="woocommerce-Price-currencySymbol amount mcmp-general-price-suffix">' . $separator . $add_text . '</span>';
		} else {
			$add_text = '';
		}
		return $price_text . $add_text;
	}

	/**
	 * Render the output - called from filter woocommerce_get_price_html
	 *
	 * @param string $html_price Original text.
	 * @param object $instance Product for which to recalculate.
	 * @since 1.0
	 * @return string|bool recalculated $html_price + custom string
	 */
	public function custom_price( $html_price, $instance ) {
		if ( is_null( $instance ) ) {
			global $product;
		} else {
			$product = $instance;
		}
		// Product validity check.
		$prod_id = $this->check_product_id( $product );
		if ( false == $prod_id ) {
			return false;
		}
		$product_type = $product->get_type();
		// Do not recalculate single variation - it's not displayed anywhere.
		if ( 'variation' == $product_type ) {
			return $html_price;
		}
		$behav = $this->get_recalc_behaviour( $prod_id );
		// Recalculate price.
		if ( ! empty( $behav ) ) {
			// Price recalculation.
			$round_prices = get_option( '_mcmp_ppu_disable_price_rounding' ) == 'yes' ? false : true;
			$wc_decimals  = wc_get_price_decimals();
			$recalc_price = '';
			switch ( $product_type ) {
				case 'simple':
					$units = floatval( $this->get_option_override( '_mcmp_ppu_cust_num_of_units', $prod_id ) );
					if ( empty( $units ) && $product->has_weight() ) {
						$units = $product->get_weight();
					}
					$units        = floatval( $units );
					$normal_price = floatval( $product->get_price() );
					if ( $units > 0 && ! empty( $normal_price ) ) {
						$hide_sale    = get_option( '_mcmp_ppu_hide_sale_price' ) == 'yes' ? true : false;
						$normal_price = wc_get_price_to_display( $product, array( 'price' => $normal_price ) );
						$normal_price = $normal_price / $units;
						$normal_price = $this->price_ratio_calc( $normal_price, $prod_id );
						if ( false == $round_prices ) {
							$normal_price = $this->truncate_number( $normal_price, $wc_decimals );
						}
						if ( $product->is_on_sale() && false == $hide_sale ) {
							$regular_price = floatval( $product->get_regular_price() );
							$regular_price = wc_get_price_to_display( $product, array( 'price' => $regular_price ) );
							$regular_price = $regular_price / $units;
							$regular_price = $this->price_ratio_calc( $regular_price, $prod_id );
							if ( false == $round_prices ) {
								$regular_price = $this->truncate_number( $regular_price, $wc_decimals );
							}
							$recalc_price = '<del>' . wc_price( $regular_price ) . '</del><ins>' . wc_price( $normal_price ) . '</ins>';
						} else {
							$recalc_price = wc_price( $normal_price );
						}
					}
					break;
				case 'variable':
					// When getting variants the filter will fire this again - this is to speed up the process.
					$had_filter      = remove_filter( 'woocommerce_get_price_html', array( $this, 'custom_price' ), $this->hook_priority );
					$variations      = $product->get_available_variations( 'objects' );
					$num_of_variants = count( $variations );
					if ( $num_of_variants > 0 ) {
						$parent_prod_weight = $product->get_weight();
						foreach ( $variations as $variation ) {
							$var_id = $variation->get_id();
							$units  = $this->get_option_override( '_mcmp_ppu_cust_num_of_units', $var_id );
							if ( empty( $units ) ) {
								$units = $variation->get_weight();
								$units = ! empty( $units ) ? $units : $parent_prod_weight;
							}
							$var_price = wc_get_price_to_display( $variation );
							if ( ! empty( $units ) && ! empty( $var_price ) ) {
								$var_recalc_prices[] = $var_price / floatval( $units );
							}
						}
						if ( isset( $var_recalc_prices ) && ! empty( $var_recalc_prices ) ) {
							$hide_max = get_option( '_mcmp_ppu_var_hide_max_price' ) == 'yes' ? true : false;
							asort( $var_recalc_prices );
							$variable_price_min = reset( $var_recalc_prices );
							$variable_price_min = $this->price_ratio_calc( $variable_price_min, $prod_id );
							if ( true == $round_prices ) {
								$variable_price_min = round( $variable_price_min, $wc_decimals );
							} else {
								$variable_price_min = $this->truncate_number( $variable_price_min, $wc_decimals );
							}
							$recalc_price = wc_price( $variable_price_min );
							if ( false == $hide_max ) {
								$variable_price_max = end( $var_recalc_prices );
								$variable_price_max = $this->price_ratio_calc( $variable_price_max, $prod_id );
								if ( true == $round_prices ) {
									$variable_price_max = round( $variable_price_max, $wc_decimals );
								} else {
									$variable_price_max = $this->truncate_number( $variable_price_max, $wc_decimals );
								}
								if ( $variable_price_min !== $variable_price_max ) {
									$recalc_price .= '–' . wc_price( $variable_price_max );
								}
							}
						}
					}
					if ( $had_filter ) {
						add_filter( 'woocommerce_get_price_html', array( $this, 'custom_price' ), $this->hook_priority, 2 );
					}
					break;
			}
		}
		switch ( $behav ) {
			case 'replace':
				// Recalc happened - let's replace, otherwise render normal text.
				if ( ! empty( $recalc_price ) ) {
					$html_price = $this->render_recalc_text( $prod_id, $recalc_price );
					if ( 'variable' == $product_type ) {
						// The _mcmp_ppu_var_prefix_text needs to be displayed even for the replaced price text.
						$var_prefix_text = get_option( '_mcmp_ppu_var_prefix_text' );
						if ( ! empty( $var_prefix_text ) ) {
							// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
							$var_prefix_text = esc_html_x( $var_prefix_text, 'Variations - variable price prefix', 'woo-price-per-unit' );
							$var_prefix_text = '<span class="woocommerce-Price-currencySymbol amount mcmp-variable-price-prefix">' . $var_prefix_text . ' </span>';
						} else {
							$var_prefix_text = '';
						}
						$html_price = $var_prefix_text . $html_price;
					}
				} else {
					$html_price = $this->general_price_manipulation( $prod_id, $html_price );
				}
				break;
			case 'add':
				if ( ! empty( $html_price ) ) {
					$html_price = $this->general_price_manipulation( $prod_id, $html_price );
				}
				if ( ! empty( $recalc_price ) ) {
					$html_price .= $this->render_recalc_text( $prod_id, $recalc_price, true );
				}
				break;
			default:
				if ( ! empty( $html_price ) ) {
					$html_price = $this->general_price_manipulation( $prod_id, $html_price );
				}
				break;
		}
		return $html_price;
	}
}
// END class mcmp_ppu.

if ( in_array( 'woocommerce/woocommerce.php', (array) get_option( 'active_plugins' ), true ) || ( is_multisite() && array_key_exists( 'woocommerce/woocommerce.php', (array) get_site_option( 'active_sitewide_plugins' ) ) ) ) {
	// Instantiate the class.
	$mcmp_ppu_obj = MCMP_PPU::get_instance();
} else {
	add_action(
		'admin_notices',
		function () {
			/* translators: 1. URL link. */
			echo '<div class="notice notice-error is-dismissible"><p><strong>' . sprintf( esc_html__( 'Price per Unit requires WooCommerce to be installed and active. You can download %s here.', 'woo-price-per-unit' ), '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
		}
	);
}
