<?php if (!defined('ABSPATH')) {exit;}
/**
* Plugin Debug Page
*
* @link			https://icopydoc.ru/
* @since		1.0.0
*/

class IMTFW_Debug_Page {
	private $pref = 'imtfw';	
	private $feedback;

	public function __construct($pref = null) {
		if ($pref) {$this->pref = $pref;}
		$this->feedback = new IMTFW_Feedback();

		$this->listen_submit();
		$this->get_html_form();	
	}

	public function get_html_form() { ?>
 		<div class="wrap">
			<h1><?php _e('Debug page', 'wc-mytarget'); ?> v.<?php echo esc_html(imtfw_optionGET('imtfw_version')); ?></h1>
			<?php do_action('my_admin_notices'); ?>
			<div id="dashboard-widgets-wrap">
				<div id="dashboard-widgets" class="metabox-holder">
					<div id="postbox-container-1" class="postbox-container">
						<div class="meta-box-sortables">
							<?php $this->get_html_block_logs(); ?>
						</div>
					</div>
					<div id="postbox-container-2" class="postbox-container">
						<div class="meta-box-sortables">
							<?php $this->get_html_block_possible_problems(); ?>
						</div>
					</div>
					<div id="postbox-container-3" class="postbox-container">
						<div class="meta-box-sortables">
							<?php $this->get_html_block_sandbox(); ?>
						</div>
					</div>
					<div id="postbox-container-4" class="postbox-container">
						<div class="meta-box-sortables">
							<?php do_action('imtfw_before_support_project'); ?>
							<?php $this->feedback->get_form(); ?>
						</div>
					</div>
				</div>
			</div>		
		</div><?php // end get_html_form();
	}

	public function get_html_block_logs() { 
		$imtfw_keeplogs = imtfw_optionGET($this->get_input_name_keeplogs());
		$imtfw_disable_notices = imtfw_optionGET($this->get_input_name_disable_notices()); ?>		    	 
		<div class="postbox">
			<h2 class="hndle"><?php _e('Logs', 'wc-mytarget'); ?></h2>
			<div class="inside">
				<p><?php if ($imtfw_keeplogs === 'on') {
					$upload_dir = wp_get_upload_dir();
					echo '<strong>'. __("Log-file here", 'wc-mytarget').':</strong><br /><a href="'.esc_html($upload_dir['baseurl']).'/wc-mytarget/plugin.log" target="_blank">'.esc_html($upload_dir['basedir']).'/wc-mytarget/plugin.log</a>';			
				} ?></p>
				<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data">
					<table class="form-table"><tbody>
					<tr>
						<th scope="row"><label for="<?php echo esc_html($this->get_input_name_keeplogs()); ?>"><?php _e('Keep logs', 'wc-mytarget'); ?></label><br />
							<input class="button" id="<?php echo esc_html($this->get_submit_name_clear_logs()); ?>" type="submit" name="<?php echo esc_html($this->get_submit_name_clear_logs()); ?>" value="<?php _e('Clear logs', 'wc-mytarget'); ?>" />
						</th>
						<td class="overalldesc">
							<input type="checkbox" name="<?php echo esc_html($this->get_input_name_keeplogs()); ?>" id="<?php echo esc_html($this->get_input_name_keeplogs()); ?>" <?php checked(esc_html($imtfw_keeplogs), 'on' ); ?>/><br />
							<span class="description"><?php _e('Do not check this box if you are not a developer', 'wc-mytarget'); ?>!</span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="<?php echo esc_html($this->get_input_name_disable_notices()); ?>"><?php _e('Disable notices', 'wc-mytarget'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="<?php echo esc_html($this->get_input_name_disable_notices()); ?>" id="<?php echo esc_html($this->get_input_name_disable_notices()); ?>" <?php checked(esc_html($imtfw_disable_notices), 'on'); ?>/><br />
							<span class="description"><?php _e('Disable notices from', 'wc-mytarget'); ?> myTarget for WooCommerce</span>
						</td>
					</tr>		 
					<tr>
						<th scope="row"><label for="button-primary"></label></th>
						<td class="overalldesc"><?php wp_nonce_field($this->get_nonce_action_debug_page(), $this->get_nonce_field_debug_page()); ?><input id="button-primary" class="button-primary" type="submit" name="<?php echo esc_html($this->get_submit_name()); ?>" value="<?php _e('Save', 'wc-mytarget'); ?>" /><br />
						<span class="description"><?php _e('Click to save the settings', 'wc-mytarget'); ?></span></td>
					</tr>         
					</tbody></table>
				</form>
			</div>
		</div><?php
	} // end get_html_block_logs();

	public function get_html_block_possible_problems() { ?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Possible problems', 'wc-mytarget'); ?></h2>
			<div class="inside"><?php
				$possible_problems_arr = $this->get_possible_problems_list();
				if ($possible_problems_arr[1] > 0) { // $possibleProblemsCount > 0) {
					echo '<ol>'.esc_html($possible_problems_arr[0]).'</ol>';
				} else {
					echo '<p>'. __('Self-diagnosis functions did not reveal potential problems', 'wc-mytarget').'.</p>';
				}
			?></div>
		</div><?php
	} // end get_html_block_sandbox();

	public function get_html_block_sandbox() { ?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Sandbox', 'wc-mytarget'); ?></h2>
			<div class="inside"><?php
				require_once IMTFW_PLUGIN_DIR_PATH.'sandbox.php';
				try {
					imtfw_run_sandbox();
				} catch (Exception $e) {
					echo 'Exception: ',  esc_html($e->getMessage()), "\n";
				}
			?></div>
	   </div><?php
	} // end get_html_block_sandbox();

	public static function get_possible_problems_list() {
		$possibleProblems = ''; $possibleProblemsCount = 0; $conflictWithPlugins = 0; $conflictWithPluginsList = ''; 				
		if (class_exists('MPSUM_Updates_Manager')) {
			$possibleProblemsCount++;
			$conflictWithPlugins++;
			$conflictWithPluginsList .= 'Easy Updates Manager<br/>';
		}
		if (class_exists('OS_Disable_WordPress_Updates')) {
			$possibleProblemsCount++;
			$conflictWithPlugins++;
			$conflictWithPluginsList .= 'Disable All WordPress Updates<br/>';
		}
		if ($conflictWithPlugins > 0) {
			$possibleProblemsCount++;
			$possibleProblems .= '<li><p>'. __('Most likely, these plugins negatively affect the operation of', 'wc-mytarget'). ' Integrate myTarget for WooCommerce:</p>'.$conflictWithPluginsList.'<p>'. __('If you are a developer of one of the plugins from the list above, please contact me', 'wc-mytarget').': <a href="mailto:support@icopydoc.ru">support@icopydoc.ru</a>.</p></li>';
		}
		return array($possibleProblems, $possibleProblemsCount, $conflictWithPlugins, $conflictWithPluginsList);
	}
	
	private function get_pref() {
		return $this->pref;
	}

	private function get_input_name_keeplogs() {
		return $this->get_pref().'_keeplogs';
	}

	private function get_input_name_disable_notices() {
		return $this->get_pref().'_disable_notices';
	}

	private function get_submit_name() {
		return $this->get_pref().'_submit_debug_page';
	}

	private function get_nonce_action_debug_page() {
		return $this->get_pref().'_nonce_action_debug_page';
	}

	private function get_nonce_field_debug_page() {
		return $this->get_pref().'_nonce_field_debug_page';
	}

	private function get_submit_name_clear_logs() {
		return $this->get_pref().'_submit_clear_logs';
	}	

	private function listen_submit() {
		if (isset($_REQUEST[$this->get_submit_name()])) {
			$this->seve_data();
			$message = __('Updated', 'wc-mytarget');
			$class = 'notice-success';	

			add_action('my_admin_notices', function() use ($message, $class) { 
				$this->admin_notices_func($message, $class);
			}, 10, 2);
		}
		
		if (isset($_REQUEST[$this->get_submit_name_clear_logs()])) {
			$filename = IMTFW_PLUGIN_UPLOADS_DIR_PATH.'/plugin.log';
			$res = unlink($filename);
			if ($res == true) {
				$message = __('Logs were cleared', 'wc-mytarget');
				$class = 'notice-success';				
			} else {
				$message = __('Error accessing log file. The log file may have been deleted previously', 'wc-mytarget');
				$class = 'notice-warning';	
			}

			add_action('my_admin_notices', function() use ($message, $class) { 
				$this->admin_notices_func($message, $class);
			}, 10, 2);
		}
		return;
	}

	private function seve_data() {
		if (!empty($_POST) && check_admin_referer($this->get_nonce_action_debug_page(), $this->get_nonce_field_debug_page())) { 
			if (isset($_POST[$this->get_input_name_keeplogs()])) {
				$keeplogs = sanitize_text_field( $_POST[$this->get_input_name_keeplogs()] );
			} else {
				$keeplogs = '';
			}
			if (isset($_POST[$this->get_input_name_disable_notices()])) {
				$disable_notices = sanitize_text_field( $_POST[$this->get_input_name_disable_notices()] );
			} else {
				$disable_notices = '';
			}
			if (is_multisite()) {
				update_blog_option(get_current_blog_id(), 'imtfw_keeplogs', $keeplogs);
				update_blog_option(get_current_blog_id(), 'imtfw_disable_notices', $disable_notices);
			} else {
				update_option('imtfw_keeplogs', $keeplogs);
				update_option('imtfw_disable_notices', $disable_notices);
			}
		}
		return;
	}

	private function admin_notices_func($message, $class) {
		printf('<div class="notice %1$s"><p>%2$s</p></div>', $class, $message);
	}
}