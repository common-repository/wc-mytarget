<?php defined( 'WP_UNINSTALL_PLUGIN' ) || exit;
if ( is_multisite() ) {
	delete_blog_option( get_current_blog_id(), 'imtfw_keeplogs' );
	delete_blog_option( get_current_blog_id(), 'imtfw_disable_notices' );
	delete_blog_option( get_current_blog_id(), 'imtfw_errors' );
	delete_blog_option( get_current_blog_id(), 'imtfw_mytarget_id' );
	delete_blog_option( get_current_blog_id(), 'imtfw_dynamic_remarketing' );
	delete_blog_option( get_current_blog_id(), 'imtfw_feed_id' );
	delete_blog_option( get_current_blog_id(), 'imtfw_code_location' );
} else {
	delete_option( 'imtfw_keeplogs' );
	delete_option( 'imtfw_disable_notices' );
	delete_option( 'imtfw_errors' );
	delete_option( 'imtfw_mytarget_id', '' );
	delete_option( 'imtfw_dynamic_remarketing' );
	delete_option( 'imtfw_feed_id', '' );
	delete_option( 'imtfw_code_location' );
}