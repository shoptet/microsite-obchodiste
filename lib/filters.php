<?php

/**
 * Edit robots.txt file
 */
add_filter('robots_txt', function( $robots_text ) {
  // via https://moz.com/community/q/default-robots-txt-in-wordpress-should-i-change-it#reply_329849
  $robots_text .= '
Disallow: /wp-includes/
Disallow: /wp-login.php
Disallow: /wp-register.php
';
  // Do not index filtering, ordering and searching
  $robots_text .= '
Disallow: /*category=*
Disallow: /*region=*
Disallow: /*orderby=*
Disallow: /*services=*
Disallow: /*p=*
Disallow: /*s=*
';
  return $robots_text;
});

/**
 * Add wholesaler categories dropdown to main menu
 */
add_filter( 'wp_nav_menu_items', function( $items, $args ) {
  if( $args->menu_id !== 'shp_navigation' ) return $items;

  $taxonomy_items = '
    <li class="shp_menu-item has-dropdown">
      <a class="shp_menu-item-link" href="' . get_post_type_archive_link( 'custom' ) . '">
      ' . __( 'Kategorie', 'shp-obchodiste' ) . '
      </a>
      <span id="categoriesDropdown" class="caret dropdown-toggle" data-target="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></span>
      <ul class="shp_navigation-submenu dropdown-menu dropdown-menu-right" aria-labelledby="categoriesDropdown">
        <li class="shp_menu-item">
          <a class="shp_menu-item-link dropdown-item first" href="' . get_post_type_archive_link( 'custom' ) . '">
          ' . __( 'Všechny kategorie', 'shp-obchodiste' ) . '
          </a>
        </li>
  ';

  foreach ( get_terms( 'customtaxonomy' ) as $term ) {
    $taxonomy_items .= '
      <li class="shp_menu-item">
        <a class="shp_menu-item-link dropdown-item" href="' . get_term_link( $term ) . '">
        ' . $term->name . '
        </a>
      </li>
    ';
  }

  $taxonomy_items .= '</ul></li>';
  return $taxonomy_items . $items;
}, 10, 2 );

/**
 * Hide redundant meta boxes in wholesaler and product edit page
 */
add_filter( 'add_meta_boxes', function() {
   // Hide category meta box
  remove_meta_box( 'tagsdiv-customtaxonomy', 'custom', 'side' );
  remove_meta_box( 'customtaxonomydiv', 'custom', 'side' ); // if taxonomy is hierarchical
   // Hide featured image metabox
  remove_meta_box( 'postimagediv', 'custom', 'side' );
  remove_meta_box( 'postimagediv', 'product', 'side' );
} );

/**
 * Remove Yoast meta box for subscribers
 */
add_filter( 'add_meta_boxes', function() {
 global $current_user;
 wp_get_current_user(); // Make sure global $current_user is set, if not set it
 if ( user_can( $current_user, 'subscriber' ) ) {
   remove_meta_box( 'wpseo_meta', 'custom', 'normal' ); // Remove Yoast meta box
 }
} );

/**
 * Set wholesaler logo as featured image
 */
add_filter( 'acf/update_value/name=logo', function( $value, $post_id, $field ) {
  // Not the correct post type, bail out
  if ( 'custom' !== get_post_type( $post_id ) ) {
    return $value;
  }
  // Skip empty value
  if ( $value != ''  ) {
    // Add the value which is the image ID to the _thumbnail_id meta data for the current post
    add_post_meta( $post_id, '_thumbnail_id', $value );
  }
  return $value;
}, 10, 3 );

/**
 * Set product thumbnail as featured image
 */
add_filter( 'acf/update_value/name=thumbnail', function( $value, $post_id, $field ) {
  // Not the correct post type, bail out
  if ( 'product' !== get_post_type( $post_id ) ) {
    return $value;
  }
  // Skip empty value
  if ( $value != ''  ) {
    // Add the value which is the image ID to the _thumbnail_id meta data for the current post
    add_post_meta( $post_id, '_thumbnail_id', $value );
  }
  return $value;
}, 10, 3 );

/**
 * Update wholesaler breadcrumb items
 */
add_filter( 'wpseo_breadcrumb_links', function( $crumbs ) {
  if ( ! is_singular( 'custom' ) ) return;
  array_splice( $crumbs, 1, 2 ); // Remove wholesaler archive and wholesaler category link from breadcrumbs
  $term_crumb = [ 'term' => get_field( 'category' )];
  array_splice( $crumbs, 1, 0, [ $term_crumb ] ); // Add main category link to breadcrumbs
  return $crumbs;
} );

/**
 * Join posts and postmeta tables for searching
 */
add_filter( 'posts_join', function( $join ) {
  global $wpdb;
  if ( ! is_admin() && is_archive() ) {
    $join .= ' LEFT JOIN ' . $wpdb->postmeta . ' AS mt0 ON ' . $wpdb->posts . '.ID = mt0.post_id ';
  }
  return $join;
} );

/**
 * Modify the search query with posts_where
 */
add_filter( 'posts_where', function( $where ) {
  global $wpdb;
  if ( ! is_admin() && is_archive() ) {
    $where = preg_replace(
      "/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
      "(" . $wpdb->posts . ".post_title LIKE $1)
      OR (
				(mt0.meta_key = 'short_about' OR mt0.meta_key = 'about_company' OR mt0.meta_key = 'about_products')
        AND
        (mt0.meta_value LIKE $1)
      )", $where );
  }
  return $where;
});

/**
 * Prevent duplicates in the search
 */
add_filter( 'posts_distinct', function( $where ) {
  if ( ! is_admin() && is_archive() ) {
    $where = 'DISTINCT';
  }
  return $where;
});

/**
 * Remove wholesaler, special offer and product list views for subscriber
 */
function remove_list_view_for_subscribers($views) {
  global $current_user;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( user_can( $current_user, 'subscriber' ) ) return [];
  return $views;
}
add_filter( 'views_edit-custom', 'remove_list_view_for_subscribers');
add_filter( 'views_edit-special_offer', 'remove_list_view_for_subscribers');
add_filter( 'views_edit-product', 'remove_list_view_for_subscribers');

/**
 * Update login header
 */
add_filter( 'login_message', function( $message ) {

  $custom_logo_id = get_theme_mod( 'custom_logo' );
  $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
  $custom_logo_url = '';
  if ( has_custom_logo() ) $custom_logo_url = esc_url( $logo[ 0 ] );

  $new_message = '
    <a href="' . get_home_url() . '">
      <img
        src="' . $custom_logo_url . '"
        style="
          display: block;
          margin: 0 auto 50px auto;
          max-width: 230px;
        "
      >
    </a>
  ';

  // Add title to login pages
  if ( ! isset( $_REQUEST[ 'action' ] ) )
    $new_message .= '<h1 style="margin-bottom:20px">' . __( 'Přihlášení', 'shp-obchodiste' ) . '</h1>';
  else if ( $_REQUEST[ 'action' ] === 'register' )
    $new_message .= '<h1 style="margin-bottom:20px">' . __( 'Registrace', 'shp-obchodiste' ) . '</h1>';
  else if ( $_REQUEST[ 'action' ] === 'lostpassword' )
    $new_message .= '<h1 style="margin-bottom:20px">' . __( 'Zapomenuté heslo', 'shp-obchodiste' ) . '</h1>';

  // Add messages to login pages
  if ( ! isset( $_REQUEST[ 'action' ] ) )
    $new_message .= '
      <p class="message">
        ' . sprintf(
          __( 'Nemáte-li vytvořený účet, nejprve se <a href="%s">registrujte</a>', 'shp-obchodiste' ),
          wp_registration_url()
        ) . '
      </p>
    ';
  else if ( $_REQUEST[ 'action' ] === 'register' )
    $new_message .= '
      <p class="message">
        ' . __( 'Zvolte si uživatelské jméno a vložte svůj e-mail', 'shp-obchodiste' ) . '
      </p>
      <p class="message">
        ' . sprintf(
          __( 'Pokud již máte vytvořený účet, <a href="%s">přihlašte se</a>', 'shp-obchodiste' ),
          wp_login_url()
        ) . '
      </p>
    ';
  else
    $new_message .= $message;

  return $new_message;
});

/**
 * Update login footer
 */
add_filter( 'login_footer', function() {
  echo '
    <a href="https://www.shoptet.cz/" target="_blank">
      <img
        src="' . get_template_directory_uri() . '/src/dist/img/shoptet-logo.svg"
        style="
          display: block;
          max-width: 120px;
          margin: 50px auto 50px auto;
        "
      >
    </a>
  ';
});

/**
 * Redirect subscriber to admin wholesaler list after login
 */
add_filter( 'login_redirect', function( $redirect_to, $request, $user ) {
  if ( isset( $user->roles ) && is_array( $user->roles ) ) {
    if ( in_array( 'subscriber', $user->roles ) ) {
      return admin_url( 'edit.php?post_type=custom' );
    }
  }
  return $redirect_to;
}, 10, 3);

/**
 * Edit new user notification e-mail
 */
add_filter( 'wp_new_user_notification_email', function( $email, $user ) {
  preg_match( '/<http(.*?)>/', $email[ 'message' ], $match ); // Get password url from message
  $set_password_url = substr( $match[ '0' ], 1, -1 ); // Remove '<' and '>' from match string

  $options = get_fields( 'options' );

  $email_from = $options[ 'email_from' ];
	$email_subject = $options[ 'welcome_email_subject' ];
	$email_body = $options[ 'welcome_email_body' ];

	$to_replace = [
		'%username%' => $user->user_login,
		'%set_password_url%' => $set_password_url,
  ];
  $email_body = strtr($email_body, $to_replace);

  $email[ 'subject' ] = $email_subject;
  $email[ 'message' ] = $email_body;
  $email[ 'headers' ] = [
    'From: ' . $email_from,
    'Content-Type: text/html; charset=UTF-8',
  ];

  return $email;
}, 10, 2);

/**
 * Remove wholesaler, special offer and product quick edit action for subscribers
 */
add_filter( 'post_row_actions', function( $actions, $post ) {
  global $current_user;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( ! user_can( $current_user, 'subscriber' ) ) return $actions;
  unset( $actions['inline hide-if-no-js'] );
  if ( $post->post_type != 'special_offer' ) return $actions;
  unset( $actions['view'] );
  return $actions;
}, 10, 2 );

/**
 * Remove wholesaler, special offer and product bulk actions for subscribers
 */
function remove_bulk_actions_for_subscribers() {
  global $current_user;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
  return ! user_can( $current_user, 'subscriber' );
}
add_filter( 'bulk_actions-edit-custom', 'remove_bulk_actions_for_subscribers' );
add_filter( 'bulk_actions-edit-special_offer', 'remove_bulk_actions_for_subscribers' );
add_filter( 'bulk_actions-edit-product', 'remove_bulk_actions_for_subscribers' );

/**
 * Show only publish owner wholesalers in special offer edit page
 */
add_filter( 'acf/fields/post_object/query/name=related_wholesaler', function( $args ) {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( ! user_can( $current_user, 'subscriber' ) ) return $args;
  $args[ 'author' ] = $current_user->ID;
  $args[ 'post_status' ] = 'publish';
  return $args;
} );