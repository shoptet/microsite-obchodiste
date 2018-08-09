<?php

/**
 * Register custom post types and taxonomies
 */
add_action( 'init', function() {
  register_post_type( 'custom', get_cpt_wholesaler_args() );
  register_taxonomy( 'wholesaler_category', 'custom', get_cpt_wholesaler_taxonomy_args() );
  register_post_type( 'wholesaler_message', get_cpt_wholesaler_message_args() );
} );

/**
 * Pass wordpress ajax url to javascript
 */
add_action( 'wp_footer', function() {
  printf(
    '<script>window.ajaxurl = \'%s\';</script>',
    admin_url( 'admin-ajax.php' )
  );
} );

/**
 * Handle ajax wholesaler message request
 */
add_action( 'wp_ajax_wholesaler_message', 'handle_wholesaler_message' );
add_action( 'wp_ajax_nopriv_wholesaler_message', 'handle_wholesaler_message' );
function handle_wholesaler_message() {

  // Sanitize message post data
  $name = sanitize_text_field( $_POST[ 'name' ] );
  $email = sanitize_email( $_POST[ 'email' ] );
  $message = sanitize_textarea_field( $_POST[ 'message' ] );
  $wholesaler_id = intval( $_POST[ 'wholesaler_id' ] );

  // Insert wholesaler message post
  $postarr = [
    'post_type' => 'wholesaler_message',
    'post_title' => $name,
    'post_status' => 'publish',
    'meta_input' => [
      'email' => $email,
      'message' => $message,
      'wholesaler' => $wholesaler_id,
    ],
  ];
  wp_insert_post( $postarr );

  // Get wholesaler post fields
  $wholesaler_title = get_the_title( $wholesaler_id );
  $wholesaler_contact_email = get_post_meta( $wholesaler_id, 'contact_email' );

  // Get ACF e-mail options
  $options = get_fields( 'options' );
  $email_from = $options[ 'email_from' ];
  $wholesaler_email_body = $options[ 'wholesaler_email_body' ];
  $wholesaler_email_subject = $options[ 'wholesaler_email_subject' ];
  $retailer_email_body = $options[ 'retailer_email_body' ];
  $retailer_email_subject = $options[ 'retailer_email_subject' ];

  // Replace e-mail body variables
  $to_replace = [
    '%contact_name%' => $name,
    '%contact_email%' => $email,
    '%contact_message%' => $message,
    '%wholesaler_name%' => $wholesaler_title,
  ];
  $wholesaler_email_body = strtr( $wholesaler_email_body, $to_replace );
  $retailer_email_body = strtr( $retailer_email_body, $to_replace );

  // Send e-mail to wholesaler
  wp_mail(
    $wholesaler_contact_email,
    $wholesaler_email_subject,
    $wholesaler_email_body,
    [
      'From: ' . $email_from,
      'Reply-to: ' . $email,
      'Content-Type: text/html; charset=UTF-8',
    ]
  );

  // Send e-mail to retailer
  wp_mail(
    $email,
    $retailer_email_subject,
    $retailer_email_body,
    [
      'From: ' . $email_from,
      'Content-Type: text/html; charset=UTF-8',
    ]
  );

  wp_die();
}
