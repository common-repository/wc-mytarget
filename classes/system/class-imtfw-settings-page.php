<?php if (!defined('ABSPATH')) {exit;}
/**
* Plugin Settings Page
*
* @link			https://icopydoc.ru/
* @since		0.1.0
*/

class IMTFW_Settings_Page {
	private $feed_id;
	private $feedback;

	public function __construct() {
		$this->feedback = new IMTFW_Feedback();

		$this->init_hooks(); // подключим хуки
		$this->listen_submit();

		$this->get_html_form();	
	}

	public function get_html_form() { ?>
		<div class="wrap">
  			<h1>Integrate myTarget for WooCommerce</h1>
			<p>(<a href="https://icopydoc.ru/kak-nastroit-remarketing-mytarget-v-woocommerce/?utm_source=wc-mytarget&utm_medium=organic&utm_campaign=in-plugin-wc-mytarget&utm_content=settings&utm_term=main-instruction" target="_blank"><?php _e('Plugin documentation', 'wc-mytarget'); ?></a>)</p>
			<div id="poststuff">

				<div id="post-body" class="columns-2">

					<div id="postbox-container-1" class="postbox-container">
						<div class="meta-box-sortables">
							<?php $this->feedback->get_block_support_project(); ?>
						</div>

						<?php do_action('imtfw_between_container_1'); ?>	

						<?php $this->feedback->get_form(); ?>
					</div><!-- /postbox-container-1 -->

					<div id="postbox-container-2" class="postbox-container">
						<div class="meta-box-sortables"><?php 
							if (isset($_GET['tab'])) {$tab = sanitize_text_field($_GET['tab']);} else {$tab = 'main_tab';}
							echo $this->get_html_tabs($tab); ?>

							<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data">
								<?php do_action('imtfw_prepend_form_container_2'); ?>
								<?php switch ($tab) : 
									case 'main_tab' : ?>
										<?php $this->get_html_main_settings(); ?>
										<?php break; ?>
								<?php endswitch; ?>

								<?php do_action('imtfw_after_optional_elemet_block'); ?>
								<div class="postbox">
									<div class="inside">
										<table class="form-table"><tbody>
											<tr>
												<th scope="row"><label for="button-primary"></label></th>
												<td class="overalldesc"><?php wp_nonce_field('imtfw_nonce_action', 'imtfw_nonce_field'); ?><input id="button-primary" class="button-primary" type="submit" name="imtfw_submit_action" value="<?php _e('Save', 'wc-mytarget'); ?>"/><br />
												<span class="description"><small><?php _e('Click to save the settings', 'wc-mytarget'); ?></small></span></td>
											</tr>
										</tbody></table>
									</div>
								</div>
							</form>
						</div>
					</div><!-- /postbox-container-2 -->

				</div>
			</div><!-- /poststuff -->
			<?php $this->get_html_icp_banners(); ?>
			<?php $this->get_html_my_plugins_list(); ?>
		</div><?php // end get_html_form();
	}

	public function get_html_tabs($current = 'main_tab') {
		$tabs = array(
			'main_tab' 			=> __('Main settings', 'wc-mytarget')	
		);
		
		$html = '<div class="nav-tab-wrapper" style="margin-bottom: 10px;">';
			foreach ($tabs as $tab => $name) {
				if ($tab === $current) {
					$class = ' nav-tab-active';
				} else {
					$class = ''; 
				}
				if (isset($_GET['feed_id'])) {
					$nf = '&feed_id='.sanitize_text_field($_GET['feed_id']);
				} else {
					$nf = '';
				}
				$html .= sprintf('<a class="nav-tab%1$s" href="?page=imtfw-settings&tab=%2$s%3$s">%4$s</a>',$class, $tab, $nf, $name);
			}
		$html .= '</div>';

		return $html;
	} // end get_html_tabs();

	public function get_html_main_settings() { 	
		$imtfw_mytarget_id = imtfw_optionGET('imtfw_mytarget_id');
		$imtfw_feed_id = imtfw_optionGET('imtfw_feed_id');
		$imtfw_dynamic_remarketing = imtfw_optionGET('imtfw_dynamic_remarketing');
		$imtfw_code_location = imtfw_optionGET('imtfw_code_location');
		?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Main settings', 'wc-mytarget'); ?></h2>
			<div class="inside">
				<table class="form-table"><tbody>
					<tr class="imtfw_tr">
						<th scope="row"><label for="imtfw_mytarget_id">myTarget ID</label></th>
						<td class="overalldesc"><input type="text" name="imtfw_mytarget_id" id="imtfw_mytarget_id" value="<?php echo esc_html( $imtfw_mytarget_id ); ?>" /><br />
						<span class="description"><small><?php _e('From your myTarget account', 'wc-mytarget'); ?></small></span></td>
					</tr>
					<tr class="imtfw_tr">
						<th scope="row"><label for="imtfw_dynamic_remarketing"><?php _e('Enable dynamic remarketing', 'wc-mytarget'); ?></label></th>
						<td class="overalldesc">
							<select name="imtfw_dynamic_remarketing" id="imtfw_dynamic_remarketing">
								<option value="enabled" <?php selected($imtfw_dynamic_remarketing, 'enabled'); ?>><?php _e('Enabled', 'wc-mytarget'); ?></option>	
								<option value="disabled" <?php selected($imtfw_dynamic_remarketing, 'disabled'); ?>><?php _e('Disabled', 'wc-mytarget'); ?></option>
							</select><br />
							<span class="description"><small><?php _e('Default value', 'wc-mytarget'); ?>: "<?php _e('Enabled', 'wc-mytarget'); ?>"</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="imtfw_feed_id"><?php _e('Feed ID', 'wc-mytarget'); ?></label></th>
						<td class="overalldesc"><input type="text" name="imtfw_feed_id" id="imtfw_feed_id" value="<?php echo esc_html( $imtfw_feed_id ); ?>" placeholder="1234" /><br />
						<span class="description"><small><?php _e('From your myTarget account', 'wc-mytarget'); ?></small></span></td>
					</tr>
					<tr class="imtfw_tr">
						<th scope="row"><label for="imtfw_code_location"><?php _e('Location of the counter code', 'wc-mytarget'); ?></label></th>
						<td class="overalldesc">
							<select name="imtfw_code_location" id="imtfw_code_location">
								<option value="footer" <?php selected(esc_html( $imtfw_code_location ), 'footer'); ?>><?php _e('Footer', 'wc-mytarget'); ?> (wp_footer)</option>
								<option value="header" <?php selected(esc_html( $imtfw_code_location ), 'header'); ?>><?php _e('Header', 'wc-mytarget'); ?> (wp_head)</option>									
							</select><br />
							<span class="description"><small><?php _e('Default value', 'wc-mytarget'); ?>: "<?php _e('Footer', 'wc-mytarget'); ?>"</small></span>
						</td>
					</tr>
				</tbody></table>
			</div>
		</div><?php
	} // end get_html_main_settings();

	public function get_html_icp_banners() { ?>
		<div id="icp_slides" class="clear">
			<div class="icp_wrap">
				<input type="radio" name="icp_slides" id="icp_point1">
				<input type="radio" name="icp_slides" id="icp_point2">
				<input type="radio" name="icp_slides" id="icp_point3">
				<input type="radio" name="icp_slides" id="icp_point4">
				<input type="radio" name="icp_slides" id="icp_point5" checked>
				<input type="radio" name="icp_slides" id="icp_point6">
				<input type="radio" name="icp_slides" id="icp_point7">
				<div class="icp_slider">
					<div class="icp_slides icp_img1"><a href="//wordpress.org/plugins/yml-for-yandex-market/" target="_blank"></a></div>
					<div class="icp_slides icp_img2"><a href="//wordpress.org/plugins/import-products-to-ok-ru/" target="_blank"></a></div>
					<div class="icp_slides icp_img3"><a href="//wordpress.org/plugins/xml-for-google-merchant-center/" target="_blank"></a></div>
					<div class="icp_slides icp_img4"><a href="//wordpress.org/plugins/gift-upon-purchase-for-woocommerce/" target="_blank"></a></div>
					<div class="icp_slides icp_img5"><a href="//wordpress.org/plugins/xml-for-avito/" target="_blank"></a></div>
					<div class="icp_slides icp_img6"><a href="//wordpress.org/plugins/xml-for-o-yandex/" target="_blank"></a></div>
					<div class="icp_slides icp_img7"><a href="//wordpress.org/plugins/import-from-yml/" target="_blank"></a></div>
				</div>
				<div class="icp_control">
					<label for="icp_point1"></label>
					<label for="icp_point2"></label>
					<label for="icp_point3"></label>
					<label for="icp_point4"></label>
					<label for="icp_point5"></label>
					<label for="icp_point6"></label>
					<label for="icp_point7"></label>
				</div>
			</div> 
		</div><?php 
	} // end get_html_icp_banners()

	public function get_html_my_plugins_list() { ?>
		<div class="metabox-holder">
			<div class="postbox">
				<h2 class="hndle"><?php _e('My plugins that may interest you', 'wc-mytarget'); ?></h2>
				<div class="inside">
					<p><span class="imtfw_bold">XML for Google Merchant Center</span> - <?php _e('Сreates a XML-feed to upload to Google Merchant Center', 'wc-mytarget'); ?>. <a href="https://wordpress.org/plugins/xml-for-google-merchant-center/" target="_blank"><?php _e('Read more', 'wc-mytarget'); ?></a>.</p> 
					<p><span class="imtfw_bold">YML for Yandex Market</span> - <?php _e('Сreates a YML-feed for importing your products to Yandex Market', 'wc-mytarget'); ?>. <a href="https://wordpress.org/plugins/yml-for-yandex-market/" target="_blank"><?php _e('Read more', 'wc-mytarget'); ?></a>.</p>
					<p><span class="imtfw_bold">Import from YML</span> - <?php _e('Imports products from YML to your shop', 'wc-mytarget'); ?>. <a href="https://wordpress.org/plugins/import-from-yml/" target="_blank"><?php _e('Read more', 'wc-mytarget'); ?></a>.</p>
					<p><span class="imtfw_bold">XML for Hotline</span> - <?php _e('Сreates a XML-feed for importing your products to Hotline', 'wc-mytarget'); ?>. <a href="https://wordpress.org/plugins/xml-for-hotline/" target="_blank"><?php _e('Read more', 'wc-mytarget'); ?></a>.</p>
					<p><span class="imtfw_bold">Gift upon purchase for WooCommerce</span> - <?php _e('This plugin will add a marketing tool that will allow you to give gifts to the buyer upon purchase', 'wc-mytarget'); ?>. <a href="https://wordpress.org/plugins/gift-upon-purchase-for-woocommerce/" target="_blank"><?php _e('Read more', 'wc-mytarget'); ?></a>.</p>
					<p><span class="imtfw_bold">Import products to ok.ru</span> - <?php _e('With this plugin, you can import products to your group on ok.ru', 'wc-mytarget'); ?>. <a href="https://wordpress.org/plugins/import-products-to-ok-ru/" target="_blank"><?php _e('Read more', 'wc-mytarget'); ?></a>.</p>
					<p><span class="imtfw_bold">XML for Avito</span> - <?php _e('Сreates a XML-feed for importing your products to', 'wc-mytarget'); ?> Avito. <a href="https://wordpress.org/plugins/xml-for-avito/" target="_blank"><?php _e('Read more', 'wc-mytarget'); ?></a>.</p>
					<p><span class="imtfw_bold">XML for O.Yandex (Яндекс Объявления)</span> - <?php _e('Сreates a XML-feed for importing your products to', 'wc-mytarget'); ?> Яндекс.Объявления. <a href="https://wordpress.org/plugins/xml-for-o-yandex/" target="_blank"><?php _e('Read more', 'wc-mytarget'); ?></a>.</p>
				</div>
			</div>
		</div><?php
	} // end get_html_my_plugins_list()

	public function admin_head_css_func() {
		/* печатаем css в шапке админки */
		print '<style>/* Best Rating & Pageviews */
			.metabox-holder .postbox-container .empty-container {height: auto !important;}
			.icp_img1 {background-image: url('. IMTFW_PLUGIN_DIR_URL .'img/sl1.jpg);}
			.icp_img2 {background-image: url('. IMTFW_PLUGIN_DIR_URL .'img/sl2.jpg);}
			.icp_img3 {background-image: url('. IMTFW_PLUGIN_DIR_URL .'img/sl3.jpg);}
			.icp_img4 {background-image: url('. IMTFW_PLUGIN_DIR_URL .'img/sl4.jpg);}
			.icp_img5 {background-image: url('. IMTFW_PLUGIN_DIR_URL .'img/sl5.jpg);}
			.icp_img6 {background-image: url('. IMTFW_PLUGIN_DIR_URL .'img/sl6.jpg);}
			.icp_img7 {background-image: url('. IMTFW_PLUGIN_DIR_URL .'img/sl7.jpg);}
		</style>';
	}

	private function init_hooks() {
		// наш класс, вероятно, вызывается во время срабатывания хука admin_menu.
		// admin_init - следующий в очереди срабатывания, хуки раньше admin_menu нет смысла вешать
		add_action('admin_init', array($this, 'listen_submits'), 10);
		add_action('admin_print_footer_scripts', array($this, 'admin_head_css_func'));
	}

	private function listen_submit() { 
		if (isset($_REQUEST['imtfw_submit_action'])) {
			if (!empty($_POST) && check_admin_referer('imtfw_nonce_action', 'imtfw_nonce_field')) {
				do_action('imtfw_prepend_submit_action');
				
				if (!isset($_GET['tab']) || (sanitize_text_field($_GET['tab']) == 'main_tab')) {	
					if (isset($_POST['imtfw_mytarget_id'])) {
						imtfw_optionUPD('imtfw_mytarget_id', sanitize_text_field($_POST['imtfw_mytarget_id']));
					}
					if (isset($_POST['imtfw_dynamic_remarketing'])) {
						imtfw_optionUPD('imtfw_dynamic_remarketing', sanitize_text_field($_POST['imtfw_dynamic_remarketing']));
					}
					if (isset($_POST['imtfw_feed_id'])) {
						imtfw_optionUPD('imtfw_feed_id', sanitize_text_field($_POST['imtfw_feed_id']));
					}
					if (isset($_POST['imtfw_code_location'])) {
						imtfw_optionUPD('imtfw_code_location', sanitize_text_field($_POST['imtfw_code_location']));
					}				
				}

			}
		}
		return;
	}

}