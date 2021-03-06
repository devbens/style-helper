<?php
/**
 * Media library and file actions.
 *
 * @Author: Benjamin Pelto
 *
 * @package style-helper
 */

/**
 * Custom uploads folder media/ instead of default content/uploads/.
 *
 * Turn off by using filter `add_filter( 'helper_change_uploads_path', '__return_false' )`
 *
 * @since 0.1.0
 */
if ( apply_filters( 'helper_change_uploads_path', true ) ) {
  $update_option = true;

  if ( 'production' === wp_get_environment_type() && get_option( 'helper_changed_uploads_path' ) ) {
    $update_option = false;
  }

  if ( $update_option ) {
    update_option( 'upload_path', untrailingslashit( str_replace( 'wp', 'media', ABSPATH ) ) );
    update_option( 'upload_url_path', untrailingslashit( str_replace( 'wp', 'media', get_site_url() ) ) );
    update_option( 'helper_changed_uploads_path', date_i18n( 'Y-m-d H:i:s' ) );
  } // end option update

  define( 'uploads', 'media' ); // phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ConstantNotUpperCase
  add_filter( 'option_uploads_use_yearmonth_folders', '__return_false', 100 );
} // end filter if
