<?php if (!defined('ABSPATH')) {exit;}
/**
* Sends feedback about the plugin
*
* @link			https://icopydoc.ru/
* @since		1.0.0
*/

final class IMTFW_Feedback {
	private $pref = 'imtfw';	

	public function __construct($pref = null) {
		if ($pref) {$this->pref = $pref;}

		$this->listen_submits_func();
	}

	public function get_form() { ?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Send data about the work of the plugin', 'wc-mytarget'); ?></h2>
			<div class="inside">
				<p><?php _e('Sending statistics you help make the plugin even better', 'wc-mytarget'); ?>! <?php _e('The following data will be transferred', 'wc-mytarget'); ?>:</p>
				<ul class="imtfw_ul">
					<li><?php _e('PHP version information', 'wc-mytarget'); ?></li>
					<li><?php _e('Multisite mode status', 'wc-mytarget'); ?></li>
					<li><?php _e('Technical information and plugin logs', 'wc-mytarget'); ?> Integrate myTarget for WooCommerce</li>
				</ul>
				<p><?php _e('Did my plugin help you', 'wc-mytarget'); ?>?</p>
				<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data">
					<p>
						<input type="radio" name="<?php echo esc_html($this->get_radio_name()); ?>" value="yes"><?php _e('Yes', 'wc-mytarget'); ?><br />
						<input type="radio" name="<?php echo esc_html($this->get_radio_name()); ?>" value="no"><?php _e('No', 'wc-mytarget'); ?><br />
					</p>
					<p><?php _e("If you don't mind to be contacted in case of problems, please enter your email address", "wc-mytarget"); ?>.</p>
					<p><input type="email" name="<?php echo esc_html($this->get_input_name()); ?>"></p>
					<p><?php _e('Your message', 'wc-mytarget'); ?>:</p>
					<p><textarea rows="6" cols="32" name="<?php echo esc_html($this->get_textarea_name()); ?>" placeholder="<?php _e('Enter your text to send me a message (You can write me in Russian or English). I check my email several times a day', 'wc-mytarget'); ?>"></textarea></p>
					<?php wp_nonce_field($this->get_nonce_action(), $this->get_nonce_field()); ?>
					<input class="button-primary" type="submit" name="<?php echo esc_html($this->get_submit_name()); ?>" value="<?php _e('Send data', 'wc-mytarget'); ?>" />
				</form>	
			</div>
		</div><?php
	}

	public function get_block_support_project() { ?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Please support the project', 'wc-mytarget'); ?>!</h2>
			<div class="inside">	  
				<p><?php _e('Thank you for using the plugin', 'wc-mytarget'); ?> <strong>Integrate myTarget for WooCommerce</strong></p>
				<p><?php _e('Please help make the plugin better', 'wc-mytarget'); ?> <a href="//forms.gle/xrrjHYNzQrRepNzXA" target="_blank" ><?php _e('answering 3 questions', 'wc-mytarget'); ?></a>!</p>
				<p><?php _e('If this plugin useful to you, please support the project one way', 'wc-mytarget'); ?>:</p>
				<ul class="imtfw_ul">
					<li><a href="//wordpress.org/support/plugin/wc-mytarget/reviews/" target="_blank"><?php _e('Leave a comment on the plugin page', 'wc-mytarget'); ?></a>.</li>
					<li><?php _e('Support the project financially', 'wc-mytarget'); ?>. <a href="//pay.cloudtips.ru/p/45d8ff3f" target="_blank"> <?php _e('Donate now', 'wc-mytarget'); ?></a>.</li>
					<li><?php _e('Noticed a bug or have an idea how to improve the quality of the plugin', 'wc-mytarget'); ?>? <a href="mailto:support@icopydoc.ru"><?php _e('Let me know', 'wc-mytarget'); ?></a>.</li>
				</ul>
				<p><?php _e('The author of the plugin Maxim Glazunov', 'wc-mytarget'); ?>.</p>
				<p><span style="color: red;"><?php _e('Accept orders for individual revision of the plugin', 'wc-mytarget'); ?></span>:<br /><a href="mailto:support@icopydoc.ru"><?php _e('Leave a request', 'wc-mytarget'); ?></a>.</p>
			</div>
		</div><?php
	}
	
	private function get_pref() {
		return $this->pref;
	}

	private function get_radio_name() {
		return $this->get_pref().'_its_ok';
	}

	private function get_input_name() {
		return $this->get_pref().'_email';
	}

	private function get_textarea_name() {
		return $this->get_pref().'_message';
	}

	private function get_submit_name() {
		return $this->get_pref().'_submit_send_stat';
	}

	private function get_nonce_action() {
		return $this->get_pref().'_nonce_action_send_stat';
	}

	private function get_nonce_field() {
		return $this->get_pref().'_nonce_field_send_stat';
	}

	private function listen_submits_func() {
		if (isset($_REQUEST[$this->get_submit_name()])) {
			$this->send_data();
			$message = __('The data has been sent. Thank you', 'wc-mytarget');
			$class = 'notice notice-success is-dismissible';	

			add_action('my_admin_notices', function() use ($message, $class) { 
				$this->admin_notices_func($message, $class);
			}, 10, 2);
		}
	}

	private function send_data() {
		if (!empty($_POST) && check_admin_referer($this->get_nonce_action(), $this->get_nonce_field())) { 	
			if (is_multisite()) { 
				$imtfw_is_multisite = 'включен';	
				$imtfw_keeplogs = get_blog_option(get_current_blog_id(), 'imtfw_keeplogs');
			} else {
				$imtfw_is_multisite = 'отключен'; 
				$imtfw_keeplogs = get_option('imtfw_keeplogs');
			}
			$unixtime = current_time('Y-m-d H:i');
			$mail_content = '<h1>Заявка (#'.$unixtime.')</h1>';
			$mail_content .= "Версия плагина: ". IMTFW_PLUGIN_VERSION . "<br />";
			$mail_content .= "Версия WP: ".get_bloginfo('version'). "<br />";
			$woo_version = get_woo_version_number();
			$mail_content .= "Версия WC: ".$woo_version. "<br />";
			$mail_content .= "Версия PHP: ".phpversion(). "<br />";   
			$mail_content .= "Режим мультисайта: ".$imtfw_is_multisite. "<br />";
			$mail_content .= "Вести логи: ".$imtfw_keeplogs. "<br />";
			$mail_content .= 'Расположение логов: <a href="'.IMTFW_PLUGIN_UPLOADS_DIR_URL.'/plugin.log" target="_blank">'.IMTFW_PLUGIN_UPLOADS_DIR_URL.'/plugin.log</a><br />';	
			$possible_problems_arr = IMTFW_Debug_Page::get_possible_problems_list();
			if ($possible_problems_arr[1] > 0) {
				$possible_problems_arr[3] = str_replace('<br/>', PHP_EOL, $possible_problems_arr[3]);
				$mail_content .= "Самодиагностика: ". PHP_EOL .$possible_problems_arr[3];
			} else {
				$mail_content .= "Самодиагностика: Функции самодиагностики не выявили потенциальных проблем". "<br />";
			}
			if (isset($_POST[$this->get_radio_name()])) {
				$mail_content .= PHP_EOL ."Помог ли плагин: ".sanitize_text_field($_POST[$this->get_radio_name()]);
			} 
			if (isset($_POST[$this->get_input_name()])) {				
				$mail_content .= '<br />Почта: <a href="mailto:'.sanitize_email($_POST[$this->get_input_name()]).'?subject=Integrate myTarget for WooCommerce (#'.$unixtime.')" target="_blank" rel="nofollow noreferer" title="'.sanitize_email($_POST['imtfw_email']).'">'.sanitize_email($_POST['imtfw_email']).'</a>';
			}
			if (isset($_POST[$this->get_textarea_name()])) {
				$mail_content .= "<br />Сообщение: ".sanitize_text_field($_POST[$this->get_textarea_name()]);
			}
			add_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
			wp_mail('support@icopydoc.ru', 'Отчёт Integrate myTarget for WooCommerce', $mail_content);
			// Сбросим content-type, чтобы избежать возможного конфликта
			remove_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
		}
	}

	public static function set_html_content_type() {
		return 'text/html';
	}

	private function admin_notices_func($message, $class) {
		printf('<div class="notice %1$s"><p>%2$s</p></div>', $class, $message);
	}
}
?>