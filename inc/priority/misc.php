<?php
/**
 * Collection of miscellaneous prioritized actions.
 *
 * @Author: Benjamin Pelto
 *
 * @package style-helper
 */

/**
 * Add preload thumbnail image size for lazyload.
 *
 * @since  1.11.0
 */
add_action( 'init', 'helper_add_lazyload_image_sizes' );
function helper_add_lazyload_image_sizes() {
  add_image_size( 'tiny-lazyload-thumbnail', 20, 20 );
} // end helper_add_lazyload_image_sizes

/**
 * Disable emojicons.
 *
 * Turn off by using `remove_action( 'init', 'helper_helper_disable_wp_emojicons' )`
 *
 * @since  0.1.0
 * @link http://wordpress.stackexchange.com/questions/185577/disable-emojicons-introduced-with-wp-4-2
 */
add_action( 'init', 'helper_helper_disable_wp_emojicons' );
function helper_helper_disable_wp_emojicons() {
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

  // Disable classic smilies
  add_filter( 'option_use_smilies', '__return_false' );
  add_filter( 'tiny_mce_plugins', 'helper_helper_disable_emojicons_tinymce' );
} // end helper_helper_disable_wp_emojicons

/**
 * Disable emojicons.
 *
 * @since 0.1.0
 * @param array $plugins Plugins.
 */
function helper_helper_disable_emojicons_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) {
    return array_diff( $plugins, [ 'wpemoji' ] );
  } else {
    return [];
  }
} // end helper_helper_disable_emojicons_tinymce

/**
 * Add support for correct UTF8 orderby for post_title and term name (äöå).
 *
 * Turn off by using `remove_filter( 'init', 'helper_orderby_fix' )`
 * Props Teemu Suoranta https://gist.github.com/TeemuSuoranta/2174f78f37248aeef483526d1c5d176f
 *
 *  @since  1.5.0
 *  @return string ordering clause for query
 */
add_filter( 'init', 'helper_orderby_fix' );
function helper_orderby_fix() {
  /**
   * Add support for correct UTF8 orderby for post_title and term name (äöå).
   *
   *  @since  1.5.0
   *  @param string $orderby ordering clause for query
   *  @return string ordering clause for query
   */
  add_filter( 'posts_orderby', function( $orderby ) {
    global $wpdb;

    if ( strstr( $orderby, 'post_title' ) ) {

      $order        = ( strstr( $orderby, 'post_title ASC' ) ? 'ASC' : 'DESC' );
      $old_orderby  = $wpdb->posts . '.post_title ' . $order;
      $utf8_orderby = ' CONVERT ( LCASE(' . $wpdb->posts . '.post_title) USING utf8) COLLATE utf8_bin ' . $order;

      // replace orderby clause in $orderby
      $orderby = str_replace( $old_orderby, $utf8_orderby, $orderby );
    }

    return $orderby;
  } );

  /**
   * Add support for correct UTF8 orderby for term name (äöå).
   *
   *  @since  1.5.0
   *  @param string $orderby ordering clause for terms query
   *  @param array  $this_query_vars an array of terms query arguments
   *  @param array  $this_query_vars_taxonomy an array of taxonomies
   *  @return string ordering clause for terms query
   */
  add_filter( 'get_terms_orderby', function( $orderby, $this_query_vars, $this_query_vars_taxonomy ) {
    if ( strstr( $orderby, 't.name' ) ) {
      $old_orderby  = 't.name';
      $utf8_orderby = 'CONVERT ( LCASE(t.name) USING utf8) COLLATE utf8_bin ';

      // replace orderby clause in $orderby.
      $orderby = str_replace( $old_orderby, $utf8_orderby, $orderby );
    }

    return $orderby;
  }, 10, 3);
} // end helper_orderby_fix
