<?php
/**
 * Plugin Name: Integrate myTarget for WooCommerce
 * Plugin URI: https://icopydoc.ru/category/documentation/wc-mytarget/
 * Description: This plugin helps setting up myTarget counter for dynamic remarketing for WooCommerce
 * Version: 0.1.3
 * Requires at least: 4.5
 * Requires PHP: 7.0.0
 * Author: Maxim Glazunov
 * Author URI: https://icopydoc.ru
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wc-mytarget
 * Domain Path: /languages
 * Tags: mytarget, product, woocommerce, target, remarketing
 * WC requires at least: 3.0.0
 * WC tested up to: 8.3.1
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * 
 * Copyright 2018-2023 (Author emails: djdiplomat@yandex.ru, support@icopydoc.ru)
 */
defined( 'ABSPATH' ) || exit;

$nr = false;
// Check php version
if ( version_compare( phpversion(), '7.0.0', '<' ) ) { // не совпали версии
	add_action( 'admin_notices', function () {
		warning_notice( 'notice notice-error',
			sprintf(
				'<strong style="font-weight: 700;">%1$s</strong> %2$s 7.0.0 %3$s %4$s',
				'Integrate myTarget for WooCommerce',
				__( 'plugin requires a php version of at least', 'wc-mytarget' ),
				__( 'You have the version installed', 'wc-mytarget' ),
				phpversion()
			)
		);
	} );
	$nr = true;
}

// Check if WooCommerce is active
$plugin = 'woocommerce/woocommerce.php';
if ( ! in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) ) )
	&& ! ( is_multisite()
		&& array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', [] ) ) )
) {
	add_action( 'admin_notices', function () {
		warning_notice(
			'notice notice-error',
			sprintf(
				'<strong style="font-weight: 700;">Integrate myTarget for WooCommerce</strong> %1$s',
				__( 'requires WooCommerce installed and activated', 'wc-mytarget' )
			)
		);
	} );
	$nr = true;
} else {
	// поддержка HPOS
	add_action( 'before_woocommerce_init', function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	} );
}

if ( ! function_exists( 'warning_notice' ) ) {
	/**
	 * Display a notice in the admin Plugins page. Usually used in a @hook 'admin_notices'
	 * 
	 * @since	0.1.0
	 * 
	 * @param	string		$class - Optional
	 * @param	string 		$message - Optional
	 * 
	 * @return	string|void
	 */
	function warning_notice( $class = 'notice', $message = '' ) {
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	}
}

// Define constants
define( 'IMTFW_PLUGIN_VERSION', '0.1.3' ); // 0.1.0

$upload_dir = wp_get_upload_dir();
// http://site.ru/wp-content/uploads
define( 'IMTFW_SITE_UPLOADS_URL', $upload_dir['baseurl'] ); 

// /home/site.ru/public_html/wp-content/uploads
define( 'IMTFW_SITE_UPLOADS_DIR_PATH', $upload_dir['basedir'] ); 

// http://site.ru/wp-content/uploads/wc-mytarget
define( 'IMTFW_PLUGIN_UPLOADS_DIR_URL', $upload_dir['baseurl'] . '/wc-mytarget' ); 

// /home/site.ru/public_html/wp-content/uploads/wc-mytarget
define( 'IMTFW_PLUGIN_UPLOADS_DIR_PATH', $upload_dir['basedir'] . '/wc-mytarget' ); 
unset( $upload_dir );

// http://site.ru/wp-content/plugins/wc-mytarget/
define( 'IMTFW_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) ); 

// /home/p135/www/site.ru/wp-content/plugins/wc-mytarget/
define( 'IMTFW_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) ); 

// /home/p135/www/site.ru/wp-content/plugins/wc-mytarget/wc-mytarget.php
define( 'IMTFW_PLUGIN_MAIN_FILE_PATH', __FILE__ ); 

// wc-mytarget - псевдоним плагина
define( 'IMTFW_PLUGIN_SLUG', wp_basename( dirname( __FILE__ ) ) ); 

// wc-mytarget/wc-mytarget.php - полный псевдоним плагина (папка плагина + имя главного файла)
define( 'IMTFW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); 

// $nr = apply_filters('imtfw_f_nr', $nr);

/* load translation
add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'yml-for-yandex-market', false, dirname( IMTFW_PLUGIN_BASENAME ) . '/languages/' );
} );

if ( false === $nr ) {
	unset( $nr );
	require_once IMTFW_PLUGIN_DIR_PATH . '/packages.php';
	register_activation_hook( __FILE__, [ 'IMTFW', 'on_activation' ] );
	register_deactivation_hook( __FILE__, [ 'IMTFW', 'on_deactivation' ] );
	add_action( 'plugins_loaded', [ 'IMTFW', 'init' ], 10 ); // активируем плагин
	define( 'IMTFW_ACTIVE', true );
}
*/
require_once plugin_dir_path( __FILE__ ) . '/packages.php'; // Подключаем файл функций
register_activation_hook( __FILE__, [ 'IMTFW', 'on_activation' ] );
register_deactivation_hook( __FILE__, [ 'IMTFW', 'on_deactivation' ] );
add_action( 'plugins_loaded', [ 'IMTFW', 'init' ] );

final class IMTFW {
	private $site_uploads_url = IMTFW_SITE_UPLOADS_URL; // http://site.ru/wp-content/uploads
	private $site_uploads_dir_path = IMTFW_SITE_UPLOADS_DIR_PATH; // /home/site.ru/public_html/wp-content/uploads
	private $plugin_version = IMTFW_PLUGIN_VERSION; // 0.1.0
	private $plugin_upload_dir_url = IMTFW_PLUGIN_UPLOADS_DIR_URL; // http://site.ru/wp-content/uploads/wc-mytarget/
	private $plugin_upload_dir_path = IMTFW_PLUGIN_UPLOADS_DIR_PATH; // /home/site.ru/public_html/wp-content/uploads/wc-mytarget/
	private $plugin_dir_url = IMTFW_PLUGIN_DIR_URL; // http://site.ru/wp-content/plugins/wc-mytarget/
	private $plugin_dir_path = IMTFW_PLUGIN_DIR_PATH; // /home/p135/www/site.ru/wp-content/plugins/wc-mytarget/
	private $plugin_main_file_path = IMTFW_PLUGIN_MAIN_FILE_PATH; // /home/p135/www/site.ru/wp-content/plugins/wc-mytarget/wc-mytarget.php
	private $plugin_slug = IMTFW_PLUGIN_SLUG; // wc-mytarget - псевдоним плагина
	private $plugin_basename = IMTFW_PLUGIN_BASENAME; // wc-mytarget/wc-mytarget.php - полный псевдоним плагина (папка плагина + имя главного файла)

	protected static $instance;
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	// Срабатывает при активации плагина (вызывается единожды)
	public static function on_activation() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$name_dir = IMTFW_SITE_UPLOADS_DIR_PATH . '/wc-mytarget';
		if ( ! is_dir( $name_dir ) ) {
			if ( ! mkdir( $name_dir ) ) {
				error_log( 'ERROR: Ошибка создания папки ' . $name_dir . '; Файл: wc-mytarget.php; Строка: ' . __LINE__, 0 );
			}
		}

		if ( is_multisite() ) {
			add_blog_option( get_current_blog_id(), 'imtfw_version', '0.1.1' );
			add_blog_option( get_current_blog_id(), 'imtfw_keeplogs', '0' );
			add_blog_option( get_current_blog_id(), 'imtfw_disable_notices', '0' );
			add_blog_option( get_current_blog_id(), 'imtfw_mytarget_id', '' );
			add_blog_option( get_current_blog_id(), 'imtfw_dynamic_remarketing', 'enabled' );
			add_blog_option( get_current_blog_id(), 'imtfw_feed_id', '' );
			add_blog_option( get_current_blog_id(), 'imtfw_code_location', 'footer' );
		} else {
			add_option( 'imtfw_version', '0.1.1', '', 'no' );
			add_option( 'imtfw_keeplogs', '0', '' );
			add_option( 'imtfw_disable_notices', '0', '', 'no' );
			add_option( 'imtfw_mytarget_id', '' );
			add_option( 'imtfw_dynamic_remarketing', 'enabled' );
			add_option( 'imtfw_feed_id', '' );
			add_option( 'imtfw_code_location', 'footer' );
		}
	}

	// Срабатывает при отключении плагина (вызывается единожды)
	public static function on_deactivation() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

	}

	public function __construct() {
		load_plugin_textdomain( 'wc-mytarget', false, $this->plugin_slug . '/languages/' ); // load translation
		$this->check_options_upd(); // проверим, нужны ли обновления опций плагина 
		$this->init_classes();
		$this->init_hooks(); // подключим хуки
	}

	public function check_options_upd() {
		$plugin_version = $this->get_plugin_version();
		if ( $plugin_version == false ) { // вероятно, у нас первичная установка плагина
			if ( is_multisite() ) {
				update_blog_option( get_current_blog_id(), 'imtfw_version', IMTFW_PLUGIN_VERSION );
			} else {
				update_option( 'imtfw_version', IMTFW_PLUGIN_VERSION );
			}
		} else if ( $plugin_version !== $this->plugin_version ) {
			add_action( 'init', array( $this, 'set_new_options' ), 10 ); // автообновим настройки, если нужно
		}
	}

	public function get_plugin_version() {
		if ( is_multisite() ) {
			$v = get_blog_option( get_current_blog_id(), 'imtfw_version' );
		} else {
			$v = get_option( 'imtfw_version' );
		}
		return $v;
	}

	public function set_new_options() {
		// удаление старых опций
		// if (imtfw_optionGET('imtfw_debug') !== false) {imtfw_optionDEL('imtfw_debug');}

		// добавление новых опций
		// if (imtfw_optionGET('imtfw_tgfp_in_cart_status') === false) {imtfw_optionUPD('imtfw_tgfp_in_cart_status', 'show', '', 'no');}

		do_action( 'imtfw_after_set_new_options' );

		if ( is_multisite() ) {
			update_blog_option( get_current_blog_id(), 'imtfw_version', IMTFW_PLUGIN_VERSION );
		} else {
			update_option( 'imtfw_version', IMTFW_PLUGIN_VERSION );
		}
		return;
	}

	public function init_classes() {
		return;
	}

	public function init_hooks() {
		add_action( 'admin_init', array( $this, 'listen_submits_func' ), 10 ); // ещё можно слушать чуть раньше на wp_loaded

		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_notices', array( $this, 'print_admin_notices_func' ) );
		add_filter( 'plugin_action_links', array( $this, 'imtfw_plugin_action_links' ), 10, 2 );

		/* Регаем стили только для страницы настроек плагина */
		add_action( 'admin_init', function () {
			wp_register_style( 'imtfw-admin-css', plugins_url( 'css/wc-mytarget.css', __FILE__ ) );
		}, 9999 );

		$imtfw_code_location = imtfw_optionGET( 'imtfw_code_location' );
		switch ( $imtfw_code_location ) {
			case 'header':
				add_action( 'wp_head', array( $this, 'get_rating_mail_counter' ), 2 );
				break;
			case 'footer':
				add_action( 'wp_footer', array( $this, 'get_rating_mail_counter' ), 2 );
				break;
			default:
		}

		add_action( 'woocommerce_order_status_completed', array( $this, 'listen_change_order_status' ), 1 );
		add_action( 'woocommerce_order_status_processing', array( $this, 'listen_change_order_status' ), 1 );
	}

	public function listen_submits_func() {
		do_action( 'imtfw_listen_submits' );

		if ( isset( $_REQUEST['imtfw_submit_action'] ) ) {
			$message = __( 'Updated', 'wc-mytarget' );
			$class = 'notice-success';

			add_action( 'admin_notices', function () use ($message, $class) {
				$this->admin_notices_func( $message, $class );
			}, 10, 2 );
		}
	}

	// Добавляем пункты меню
	public function add_admin_menu() {
		$page_suffix = add_menu_page( null, __( 'myTarget settings', 'wc-mytarget' ), 'unfiltered_html', 'imtfw-settings', array( $this, 'get_settings_page_func' ), 'dashicons-buddicons-forums', 51 );
		add_action( 'admin_print_styles-' . $page_suffix, array( $this, 'enqueue_style_admin_css_func' ) ); // создаём хук, чтобы стили выводились только на странице настроек

		$page_suffix = add_submenu_page( 'imtfw-settings', __( 'Debug', 'wc-mytarget' ), __( 'Debug page', 'wc-mytarget' ), 'unfiltered_html', 'imtfwdebug', array( $this, 'get_debug_page_func' ) );
		add_action( 'admin_print_styles-' . $page_suffix, array( $this, 'enqueue_style_admin_css_func' ) );
	}

	// вывод страницы настроек плагина
	public function get_settings_page_func() {
		new IMTFW_Settings_Page();
		return;
	}

	// вывод страницы настроек плагина
	public function get_debug_page_func() {
		new IMTFW_Debug_Page();
		return;
	}

	public function enqueue_style_admin_css_func() {
		/* Ставим css-файл в очередь на вывод */
		wp_enqueue_style( 'imtfw-admin-css' );
	}

	// Вывод различных notices
	public function print_admin_notices_func() {

	}

	public static function imtfw_plugin_action_links( $actions, $plugin_file ) {
		if ( false === strpos( $plugin_file, basename( __FILE__ ) ) ) {
			// проверка, что у нас текущий плагин
			return $actions;
		}
		$settings_link = '<a href="/wp-admin/admin.php?page=imtfw-settings">' . __( 'Settings', 'wc-mytarget' ) . '</a>';
		array_unshift( $actions, $settings_link );
		return $actions;
	}

	private function admin_notices_func( $message, $class ) {
		printf( '<div class="notice %1$s"><p>%2$s</p></div>', $class, $message );
		return;
	}

	// вывод счётчика

	public function get_rating_mail_counter() {
		global $woocommerce;
		$imtfw_mytarget_id = imtfw_optionGET( 'imtfw_mytarget_id' );
		if ( $imtfw_mytarget_id == '' ) {
			return;
		} // не можем вывести счётчик
		printf( '
			<!-- Rating@Mail.ru counter -->
			<script type="text/javascript">
			var _tmr = window._tmr || (window._tmr = []);
			_tmr.push({id: "%1$s", type: "pageView", start: (new Date()).getTime(), pid: "USER_ID"});
			(function (d, w, id) {
				if (d.getElementById(id)) return;
				var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
				ts.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//top-fwz1.mail.ru/js/code.js";
				var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
				if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
			})(document, window, "topmailru-code");
			</script>
			<noscript><div>
			<img src="//top-fwz1.mail.ru/counter?id=%1$s;js=na" style="border:0;position:absolute;left:-9999px;" alt="" />
			</div></noscript>
			<!-- //Rating@Mail.ru counter -->' . PHP_EOL,
			esc_js( $imtfw_mytarget_id )
		);

		$imtfw_dynamic_remarketing = imtfw_optionGET( 'imtfw_dynamic_remarketing' );
		if ( $imtfw_dynamic_remarketing === 'enabled' ) {
			$this->get_counter_dynamic_remarketing();
		}
	}

	public function get_counter_dynamic_remarketing() {
		// https://target.my.com/help/advertisers/webdynrem/ru
		// https://161-shop.ru/checkout/order-received/6210/?key=wc_order_7sjMLlCwZpJHj
		// если у нас не страница товара, корзины или заказа, то не выводим код
		if ( ! is_product() && ! is_shop() && ! is_product_category() && ! is_cart() && ! is_order_received_page() ) {
			return;
		}

		$flag = true;
		if ( is_product() ) {
			$pagetype_str = 'product';
			$product = wc_get_product( get_the_id() );
			$product_id_str = (string) $product->get_id();
			$price_str = (string) $product->get_price();
		} else if ( is_cart() ) {
			// $cart_totals_arr = WC()->cart->get_totals();
			$pagetype_str = 'cart';
			$products_in_cart_arr = WC()->cart->get_cart(); // товары в корзине
			$cart_product_price_total = 0;
			$product_id_str = [];
			foreach ( $products_in_cart_arr as $hash => $cart_item ) {
				$product_id_str[] = (string) $cart_item["product_id"];
				$cart_product_price_total = $cart_product_price_total + $cart_item['data']->get_price();
			}
			// если корзина пуста, то не выводим код ремаркетинга
			if ( $cart_product_price_total === 0 ) {
				return;
			}
			$price_str = (string) $cart_product_price_total;
		} else if ( is_order_received_page() ) {
			// https://161-shop.ru/checkout/order-received/6211/?key=wc_order_Rr0c1SpsJjr7F
			$pagetype_str = 'purchase';
			global $wp;
			$order_id = absint( $wp->query_vars['order-received'] ); // Get the order ID
			if ( empty( $order_id ) || $order_id == 0 ) {
				return;
			} else {
				$product_id_str = [];
				$cart_product_price_total = 0;
				$order = wc_get_order( $order_id );
				$order_items = $order->get_items();
				foreach ( $order_items as $item_id => $item ) {
					// методы класса WC_Order_Item
					// ID элемента можно получить из ключа массива или так:
					// $item_id = $item->get_id();
					// методы класса WC_Order_Item_Product
					// $product_id = $item->get_product_id(); // the Product id
					// $wc_product = $item->get_product(); // the WC_Product object
					// $sku = $wc_product->get_sku();
					$product_id_str[] = (string) $item->get_product_id();
					$wc_product = $item->get_product();
					$cart_product_price_total = $cart_product_price_total + $wc_product->get_price();
				}
				$price_str = (string) $cart_product_price_total;
			}
			// echo '<script type="text/javascript">alert('.$cart_product_price_total.');</script>';
		} else if ( is_product_category() || is_shop() ) {
			$flag = false;
		} else {
			return;
		}

		$imtfw_feed_id = imtfw_optionGET( 'imtfw_feed_id' );
		if ( $imtfw_feed_id == '' ) {
			$imtfw_feed_id_str = '';
		} else {
			$imtfw_feed_id_str = sprintf( ', list: %1$s',
				json_encode( $imtfw_feed_id )
			);
		}
		if ( true === $flag ) {
			printf( '
				<!-- Rating@Mail.ru counter dynamic remarketing appendix -->
				<script type="text/javascript">
					var _tmr = _tmr || [];
					_tmr.push({
						type: "itemView",
						productid: %1$s,
						pagetype: %2$s, 
						totalvalue: %3$s%4$s
					});
				</script>
				<!-- // Rating@Mail.ru counter dynamic remarketing appendix -->' . PHP_EOL,
				json_encode( $product_id_str ),
				json_encode( $pagetype_str ),
				json_encode( $price_str ),
				$imtfw_feed_id_str
			);
		}

		if ( is_product() ) {
			printf( '
				<script type="text/javascript">
				document.addEventListener("DOMContentLoaded", () => {

				jQuery(function($){$(document).ready( function() {
					// клик внутри простого товара
					$(\'%1$s\').on("click", function() {
						console.log(\'сработало условие click по %1$s\');
						var product_id = $(this).attr("value");

						var _tmr = _tmr || [];
						_tmr.push({
							type: "itemView",
							productid: product_id,
							pagetype: \'cart\'%2$s
						});					
					});

					// клик внутри вариативного товара
					$(".single_add_to_cart_button").on("click", function() {
						console.log("сработало условие click по .single_add_to_cart_button");

						var product_id = $(\'input[type=\"hidden\"][name=\"product_id\"][value]\').attr("value");
						var variation_id = $(\'input.variation_id[type=\"hidden\"][name=\"variation_id\"][value]\').attr("value");
						
						console.log("product_id = " + product_id + "; variation_id = " + variation_id);
			
						var _tmr = _tmr || [];
						_tmr.push({
							type: "itemView",
							productid: product_id,
							pagetype: \'cart\'%2$s
						});
						return true;
			
					});

				})}); // end jQuery

				});
				</script>' . PHP_EOL,
				'[name=\"add-to-cart\"]',
				$imtfw_feed_id_str
			);
		}

		if ( is_product_category() || is_shop() ) {
			printf( '
				<script type="text/javascript">
				document.addEventListener("DOMContentLoaded", () => {

					jQuery(function($){$(document).ready( function() {
						// клик по добавить в корзину в категории товаров
						$(\'%1$s\').on("click", function() {
							console.log(\'сработало условие click по %1$s\');
							var product_id = jQuery(this).attr("data-product_id");
							console.log("product_id = " + product_id);

							var _tmr = _tmr || [];
							_tmr.push({
								type: "itemView",
								productid: product_id,
								pagetype: \'cart\'%2$s
							});
							return true;
						});
					})}); // end jQuery

				});
				</script>' . PHP_EOL,
				'a.ajax_add_to_cart',
				$imtfw_feed_id_str
			);
		}
	}

	public function listen_change_order_status( $order_id ) {

		return;
	}

	public function listen_change_order_status_thankyou( $order_id ) {
		// $order->update_status('wc-on-hold');
		if ( ! $order_id ) {
			return;
		}
		if ( $order_id > 0 ) {
			$order = wc_get_order( $order_id );
		}
		if ( 'failed' === $order->get_status() || 'wc-on-hold' === $order->get_status() ) {
			return;
		} // processing, failed, wc-on-hold

		if ( $order instanceof WC_Order ) {
			// $woo = WC();
			// $order_total_value = (float) $order->get_total();
			$order_items = $order->get_items();

			if ( $order_items ) {
				$product_id_arr = [];
				foreach ( $order_items as $item ) {
					$product = $item->get_product();
					$product_id_arr[] = $product->get_id();
				}
			}
		}
		return;
	}
} /* end class GiftUponPurchaseForWooCommerce */