<?php defined( 'ABSPATH' ) || exit;
require_once plugin_dir_path( __FILE__ ) . 'includes/old-php-add-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/icopydoc-useful-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/wc-add-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'functions.php'; // Подключаем файл функций
require_once plugin_dir_path( __FILE__ ) . 'includes/backward-compatibility.php';

require_once plugin_dir_path( __FILE__ ) . 'classes/system/class-imtfw-debug-page.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/system/class-imtfw-error-log.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/system/class-imtfw-feedback.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/system/class-imtfw-settings-page.php';