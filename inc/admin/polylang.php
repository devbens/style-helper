<?php
/**
 * @Author: Benjamin Pelto
 *
 * @package style-helper
 */

/**
 *  Check that Polylang Pro license is confugured and valid.
 *
 *  @since  2.0.0
 */
add_action( 'admin_init', 'helper_is_polylang_license_set' );
function helper_is_polylang_license_set() {
  if ( ! is_plugin_active( 'polylang-pro/polylang.php' ) ) {
    return;
  }

  $set = true;
  $licences = get_option( 'polylang_licenses' );

  if ( ! is_array( $licences ) ) {
    add_action( 'admin_notices', 'helper_polylang_license_not_set' );
    $set = false;
  }

  if ( isset( $licences['polylang-pro'] ) ) {
    if ( 'invalid' === $licences['polylang-pro']['data']->license ) {
      $set = false;
    }
  } else {
    $set = false;
  }

  if ( ! $set ) {
    add_action( 'admin_notices', 'helper_polylang_license_not_set' );
  }

  return $set;
} // end helper_is_helpscout_beacon_configured

/**
 *  Show notice if there are problems with Polylang Pro license.
 *
 *  @since  2.0.0
 */
function helper_polylang_license_not_set() {
  $class = 'notice notice-error';
  $message = __( 'Polylang Pro license not set or invalid. Please update the license or contact your agency to fix this issue.', 'style-helper' );

  printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
} // end helper_helpscout_beacon_not_configured_notice
