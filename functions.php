<?php defined( 'ABSPATH' ) || exit;
/**
 * @since 0.1.0
 *
 * @param string $optName (require)
 * @param string $value (require)
 * @param string $n (not require)
 *
 * @return true/false
 * Возвращает то, что может быть результатом add_blog_option, add_option
 */
function imtfw_optionADD( $optName, $value = '', $n = '' ) {
	if ( $optName == '' ) {
		return false;
	}
	if ( $n === '1' ) {
		$n = '';
	}
	$optName = $optName . $n;
	if ( is_multisite() ) {
		return add_blog_option( get_current_blog_id(), $optName, $value );
	} else {
		return add_option( $optName, $value );
	}
}
/**
 * @since 0.1.0
 *
 * @param string $optName (require)
 * @param string $value (require)
 * @param string $n (not require)
 *
 * @return true/false
 * Возвращает то, что может быть результатом update_blog_option, update_option
 */
function imtfw_optionUPD( $optName, $value = '', $n = '' ) {
	if ( $optName == '' ) {
		return false;
	}
	if ( $n === '1' ) {
		$n = '';
	}
	$optName = $optName . $n;
	if ( is_multisite() ) {
		return update_blog_option( get_current_blog_id(), $optName, $value );
	} else {
		return update_option( $optName, $value );
	}
}
/**
 * @since 0.1.0
 * @updated in v2.0.0
 *
 * @param string $optName (require)
 * @param string $n (not require)
 *
 * @return true/false
 * Возвращает то, что может быть результатом get_blog_option, get_option
 */
function imtfw_optionGET( $optName, $n = '' ) {
	if ( $optName == '' ) {
		return false;
	}
	if ( $n === '1' ) {
		$n = '';
	}
	$optName = $optName . $n;
	if ( is_multisite() ) {
		return get_blog_option( get_current_blog_id(), $optName );
	} else {
		return get_option( $optName );
	}
}
/**
 * @since 0.1.0
 *
 * @param string $optName (require)
 * @param string $n (not require)
 *
 * @return true/false
 * Возвращает то, что может быть результатом delete_blog_option, delete_option
 */
function imtfw_optionDEL( $optName, $n = '' ) {
	if ( $optName == '' ) {
		return false;
	}
	if ( $n === '1' ) {
		$n = '';
	}
	$optName = $optName . $n;
	if ( is_multisite() ) {
		return delete_blog_option( get_current_blog_id(), $optName );
	} else {
		return delete_option( $optName );
	}
}