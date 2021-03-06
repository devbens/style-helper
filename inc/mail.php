<?php
/**
 * Actions related to sending mail in development and staging envarioments.
 *
 * @Author: Benjamin Pelto
 *
 * @package style-helper
 */

/**
 *  Force to address in wp_mail function so that test emails wont go to client.
 *
 *  Turn off by using `remove_filter( 'wp_mail', 'helper_helper_force_mail_to' )`
 *
 *  @since  0.1.0
 */
if ( wp_get_environment_type() === 'development' ) {
  add_filter( 'wp_mail', 'helper_helper_force_mail_to' );
}

// Turn off by using `remove_filter( 'wp_mail', 'helper_helper_force_mail_to' )`
if ( wp_get_environment_type() === 'staging' ) {
  add_filter( 'wp_mail', 'helper_helper_force_mail_to' );
  add_filter( 'wp_mail_from', 'helper_staging_wp_mail_from' );
}

/**
 *  Force to address in wp_mail.
 *
 *  Change allowed staging roles by using `add_filter( 'helper_helper_mail_to_allowed_roles', 'myprefix_override_helper_helper_mail_to_allowed_roles' )`
 *  Change address from admin_email by using `add_filter( 'helper_helper_mail_to', 'myprefix_override_helper_helper_mail_to' )`
 *
 *  @since  0.1.0
 *  @param  array $args Default wp_mail agruments.
 *  @return array         New wp_mail agruments with forced to address
 */
function helper_helper_force_mail_to( $args ) {
  $to = apply_filters( 'helper_helper_mail_to', 'benjophp@gmail.com' );

  if ( wp_get_environment_type() === 'staging' ) {
    $allowed_roles = apply_filters( 'helper_helper_mail_to_allowed_roles', [ 'administrator', 'editor', 'author' ] );
    $user = get_user_by( 'email', $args['to'] );

    if ( is_a( $user, 'WP_User' ) ) {
      if ( array_intersect( $allowed_roles, $user->roles ) ) {
        $to = $args['to'];
      }
    }
  }

  $args['to'] = apply_filters( 'helper_helper_mail_to', $to );
  return $args;
} // end helper_helper_force_mail_to

/**
 *  Do not force to address when sending notification to new user created.
 *
 *  Turn off by using `remove_action( 'edit_user_created_user', 'helper_dont_force_created_user_mail' )`
 *
 *  @since  1.2.0
 *  @param  string $user_id ID of new user.
 *  @param  string $notify  Who to notify about user registration.
 */
add_action( 'edit_user_created_user', 'helper_dont_force_created_user_mail', 10, 2 );
function helper_dont_force_created_user_mail( $user_id, $notify ) {
  remove_filter( 'wp_mail', 'helper_helper_force_mail_to' );
  wp_send_new_user_notifications( $user_id, $notify );
  add_filter( 'wp_mail', 'helper_helper_force_mail_to' );
} // end helper_dont_force_created_user_mail

/**
 *  Force from address in staging to fix some oddness.
 *
 *  @since  1.8.1
 *  @return string  Email address
 */
function helper_staging_wp_mail_from() {
  return 'wordpress@' . str_replace( [ 'http://', 'https://', '/wp' ], '', get_site_url() );
} // end helper_staging_wp_mail_from
