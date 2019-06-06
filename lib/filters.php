<?php

require_once( ABSPATH . 'wp-admin/includes/screen.php' );

/**
<<<<<<< HEAD
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
Disallow: /*category[]=*
Disallow: /*category%5B%5D=*
Disallow: /*region[]=*
Disallow: /*region%5B%5D=*
Disallow: /*services[]=*
Disallow: /*services%5B%5D=*
Disallow: /*orderby=*
Disallow: /*services=*
Disallow: /*p=*
Disallow: /*s=*
';
  return $robots_text;
});

/**
=======
>>>>>>> Remove robots.txt filter
 * Add products and wholesaler categories dropdown to main menu
 */
add_filter( 'wp_nav_menu_items', function( $items_html, $args ) {
  if( $args->menu_id !== 'shp_navigation' ) return $items_html;

  $menu_items_data = [
    'product' => [
      'title' => __( 'Produkty', 'shp-obchodiste' ),
      'taxonomy' => 'producttaxonomy',
    ],
    'custom' => [
      'title' => __( 'Velkoobchody', 'shp-obchodiste' ),
      'taxonomy' => 'customtaxonomy',
    ],
  ];

  $menu_items_html = '';

  foreach ( $menu_items_data as $post_type => $data ) {

    $menu_items_html .= '
      <li class="shp_menu-item has-dropdown">
        <a class="shp_menu-item-link" href="' . get_post_type_archive_link( $post_type ) . '">
        ' . $data['title'] . '
        </a>
        <span class="caret dropdown-toggle" data-target="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></span>
        <ul class="shp_navigation-submenu dropdown-menu dropdown-menu-right">
          <li class="shp_menu-item">
            <a class="shp_menu-item-link dropdown-item first" href="' . get_post_type_archive_link( $post_type ) . '">
            ' . __( 'Všechny kategorie', 'shp-obchodiste' ) . '
            </a>
          </li>
    ';

    foreach ( get_terms( $data['taxonomy'] ) as $term ) {
      $menu_items_html .= '
        <li class="shp_menu-item">
          <a class="shp_menu-item-link dropdown-item" href="' . get_term_link( $term ) . '">
          ' . $term->name . '
          </a>
        </li>
      ';
    }
  
    $menu_items_html .= '</ul></li>';

  }

  return $menu_items_html . $items_html;
}, 10, 2 );

/**
 * Hide redundant meta boxes in wholesaler and product edit page
 */
add_filter( 'add_meta_boxes', function() {
   // Hide category meta box
  remove_meta_box( 'tagsdiv-customtaxonomy', 'custom', 'side' );
  remove_meta_box( 'customtaxonomydiv', 'custom', 'side' ); // if taxonomy is hierarchical
  remove_meta_box( 'tagsdiv-producttaxonomy', 'product', 'side' );
  remove_meta_box( 'producttaxonomydiv', 'product', 'side' ); // if taxonomy is hierarchical
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
    // Remove Yoast meta box
   remove_meta_box( 'wpseo_meta', 'custom', 'normal' );
   remove_meta_box( 'wpseo_meta', 'product', 'normal' );
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
 * Validate product import CSV file
 */
add_filter( 'acf/validate_value/name=product_import_file', function( $valid, $value ) {
  // bail early if value is already invalid
  if( ! $valid ) return $valid;

  $file_path = get_attached_file( $value );
  $fp = fopen( $file_path, 'r' );

  if ( ! $fp ) {
    $valid =  __( 'Soubor nelze otevřít', 'shp-obchodiste' );
    return $valid;
  }

  $header = fgetcsv( $fp, 0, ';' );
  $mandatory = [
    'name',
  ];

  // Check for mandatory fields
  if ( count( array_intersect( $mandatory, $header ) ) !== count( $mandatory ) ) {
    $valid =  __( 'Hlavička souboru neobsahuje všechny povinné položky', 'shp-obchodiste' );
    return $valid;
  }

  $col_num = count( $header );
  $data = [];
  
  while ( $row = fgetcsv( $fp, 0, ';' ) ) {

    // Check the number of fields in a row to be equal to the header
    if ( count( $row ) !== $col_num ) {
      $valid =  __( 'Jeden nebo více řádků obsahují jiný počet položek než hlavička', 'shp-obchodiste' );
      break;
    }

    // Check the mandatory fields in row
    $row = array_combine( $header, $row );
    foreach ( $mandatory as $m ) {
      if ( empty( $row[ $m ] ) ) {
        $valid =  __( 'Jedna nebo více povinných položek nejsou vyplněny ve všech řádcích', 'shp-obchodiste' );
      }
    }
  }

  fclose( $fp );
  
  return $valid;
}, 10, 2 );

/**
 * Update wholesaler breadcrumb items
 */
add_filter( 'wpseo_breadcrumb_links', function( $crumbs ) {
  if ( is_singular( 'custom' ) ) {
    array_splice( $crumbs, 1, 2 ); // Remove wholesaler archive and wholesaler category link from breadcrumbs
    $term_crumb = [ 'term' => get_field( 'category' ) ];
    array_splice( $crumbs, 1, 0, [ $term_crumb ] ); // Add main category link to breadcrumbs
  } else if ( is_singular( 'product' ) ) {
    array_splice( $crumbs, 1, 1 ); // Remove product archive link from breadcrumbs
    if ( $related_wholesaler = get_field( 'related_wholesaler' ) ) {
      $post_crumb = [ 'id' => $related_wholesaler->ID ];
      array_splice( $crumbs, 1, 0, [ $post_crumb ] ); // Add related wholesaler link to breadcrumbs
      $term_crumb = [ 'term' => get_field( 'category', $related_wholesaler->ID ) ];
      array_splice( $crumbs, 1, 0, [ $term_crumb ] ); // Add related wholesaler main category link to breadcrumbs
    }
  }
  if (is_paged()) {
    array_pop($crumbs); // Remove page number item from archives
  }
  return $crumbs;
} );

/**
 * Rename pagination slug
 */
add_filter('init', function () {
  global $wp_rewrite;
  $wp_rewrite->pagination_base = __( 'strana', 'shp-obchodiste' );
  $wp_rewrite->flush_rules();
}, 0);

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
add_filter( 'views_edit-custom', 'remove_list_view_for_subscribers', 11);
add_filter( 'views_edit-special_offer', 'remove_list_view_for_subscribers', 11);
add_filter( 'views_edit-product', 'remove_list_view_for_subscribers', 11);

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
          margin: 0 auto 15px auto;
          max-width: 230px;
        "
      >
    </a>
  ';
  $new_message .= '<p style="margin-bottom:40px;text-align:center;">';
  $new_message .= __( 'Nabídněte svoje produkty maloobchodním prodejcům. Služba Obchodistě je zcela zdarma, bez jakýchkoliv přímých nebo nepřímých poplatků.', 'shp-obchodiste' );
  $new_message .= '</p>';

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
function remove_bulk_actions_for_subscribers( $actions ) {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( user_can( $current_user, 'subscriber' ) ) unset( $actions['edit'] );
  return $actions;
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
  return $args;
} );

/**
 * Set default related wholesaler for subscriber
 */
add_filter('acf/load_value/name=related_wholesaler', function( $value ) {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( ! is_admin() ) return $value;
  if ( ! user_can( $current_user, 'subscriber' ) ) return $value;
  if ( $related_wholesaler = get_user_wholesaler( $current_user, 'publish' ) )
    $value = $related_wholesaler->ID;
  else
    $value = NULL;
  return $value;
} );

/**
 * Remove related wholesaler for product import page field
 */
add_filter('acf/load_value/name=related_wholesaler', function( $value ) {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( ! is_admin() ) return $value;
  $screen = get_current_screen();
  if ( 'product_page_product-import' !== $screen->base ) return $value;
  if ( ! user_can( $current_user, 'subscriber' ) ) return NULL;
  if ( $related_wholesaler = get_user_wholesaler( $current_user ) )
    return $related_wholesaler->ID;
  return NULL;
} );

/**
 * Set related wholesaler to product
 */
add_filter( 'acf/update_value/name=related_wholesaler', function( $value ) {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( ! user_can( $current_user, 'subscriber' ) ) return $value;

  if ( $wholesaler = get_user_wholesaler( $current_user, 'publish' ) ) {
    return $wholesaler->ID;
  }

  return $value;
} );

add_filter( 'post_type_labels_custom', function ( $labels ) {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it

  if ( ! user_can( $current_user, 'subscriber' ) ) return;

  $labels->menu_name = __( 'Můj velkoobchod', 'shp-obchodiste' );

  $wholesaler = get_user_wholesaler( $current_user, 'publish' );
  if ( $wholesaler ) {
    $labels->all_items = __( 'Upravit medailonek', 'shp-obchodiste' );
  } else {
    $labels->all_items = __( 'Přidat medailonek', 'shp-obchodiste' );
  }
  return $labels;
} );

add_filter( 'post_type_labels_product', function ( $labels ) {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it

  if ( ! user_can( $current_user, 'subscriber' ) ) return;

  $labels->menu_name = __( 'Moje produkty', 'shp-obchodiste' );

  return $labels;
} );

/**
 * Remove admin footer for subscriber
 */
function remove_admin_footer ( $text ) {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( user_can( $current_user, 'subscriber' ) ) {
    $text = '';
  }
  return $text;
}
add_filter( 'admin_footer_text', 'remove_admin_footer', 11 );
add_filter( 'update_footer', 'remove_admin_footer', 11 );

/**
 * ACF ZIP validation
 */
add_filter( 'acf/validate_value/name=zip', function( $valid, $value, $field, $input ) {
  // bail early if value is already invalid
  if( ! $valid ) return $valid;

  if ( ! preg_match('/^[0-9]{5}$/', $value ) ) {
    $valid = __( 'Zadejte prosím PSČ ve správném formátu', 'shp-obchodiste' );
  }
  
  return $valid;
}, 10, 4 );

/**
 * ACF IN validation
 */
add_filter( 'acf/validate_value/name=in', function( $valid, $value, $field, $input ) {
  // bail early if value is already invalid
  if( ! $valid ) return $valid;

  if ( ! preg_match('/^[0-9]+$/', $value ) ) {
    $valid = __( 'Zadejte prosím IČ ve správném formátu', 'shp-obchodiste' );
  }
  
  return $valid;
}, 10, 4 );

/**
 * ACF url validation
 */
add_filter( 'acf/validate_value/name=website', function( $valid, $value, $field, $input ) {
  // bail early if value is already invalid
  if( ! $valid ) return $valid;

  if ( ! preg_match('/^(https?:\/\/)?([a-zA-Z0-9]([a-zA-ZäöüÄÖÜ0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}.*/', $value ) ) {
    $valid = __( 'Zadejte prosím URL ve správném formátu', 'shp-obchodiste' );
  }
  
  return $valid;
}, 10, 4 );

/**
 * ACF tel validation
 */
add_filter( 'acf/validate_value/name=contact_tel', function( $valid, $value, $field, $input ) {
  // bail early if value is already invalid
  if( ! $valid ) return $valid;

  if ( ! preg_match('/^[+]?[0-9]{9,}$/', $value ) ) {
    $valid = __( 'Zadejte prosím telefonní číslo ve správném formátu', 'shp-obchodiste' );
  }
  
  return $valid;
}, 10, 4 );

/**
 * Show only own media for subscribers
 */
add_filter( 'ajax_query_attachments_args', function ( $query = [] ) {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( user_can( $current_user, 'subscriber' ) ) {
    $query['author'] = $current_user->ID;
  }
  return $query;
} );

/**
 * Change product og type
 */
add_filter( 'wpseo_opengraph_type', function ( $type ) {
  if ( is_singular( 'product' ) ) {
    return 'product';
  }
  return $type;
} );

/**
 * Change archive file path
 */
add_filter( 'archive_template', function ( $archive_template ) {
  if ( is_post_type_archive( [ 'custom', 'special_offer', 'product' ] ) )
    return get_template_directory() . '/src/archive.php';
  return $archive_template;
} ) ;

/**
 * Change taxonomy file path
 */
add_filter( 'taxonomy_template', function ( $taxonomy_template ) {
  if ( is_tax( [ 'customtaxonomy', 'producttaxonomy' ] ) )
    return get_template_directory() . '/src/taxonomy.php';
  return $taxonomy_template;
} ) ;

/**
 * Change single file path
 */
add_filter( 'single_template', function ( $single_template ) {
  $object = get_queried_object();
  switch ( $object->post_type ) {
    case 'custom':
    return get_template_directory() . '/src/single.php';
    case 'product':
    return get_template_directory() . '/src/single-product.php';
  }
  return $single_template;
} ) ;

/**
 * Move Yoast SEO plugin to bottom
 */
add_filter( 'wpseo_metabox_prio', function () {
  return 'low';
} );

/**
 * Add related wholesaler column to post list in admin
 */
add_filter( 'manage_edit-product_columns', function ( $columns ) {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  $custom_columns = [];
  if ( ! user_can( $current_user, 'subscriber' ) ) {
    $custom_columns['related_wholesaler'] = __( 'Velkoobchod', 'shp-obchodiste' );
  }
  $custom_columns['sync_state'] = __( 'Stav synchronizace', 'shp-obchodiste' );
	return
		array_slice( $columns, 0, 3, true ) +
		$custom_columns +
		array_slice( $columns, 3, 4, true );
} );

/**
 * Add related post and its type to message post list in admin
 */
add_filter( 'manage_edit-wholesaler_message_columns', function ( $columns ) {
	return
		array_slice( $columns, 0, 2, true ) +
		[ 'related_post_type' => __( 'Typ zdroje', 'shp-obchodiste' ) ] +
		[ 'related_post' => __( 'Zdroj', 'shp-obchodiste' ) ] +
		array_slice( $columns, 2, 4, true );
} );

/**
 * Add wholesaler and product filter links to message post list in admin
 */
add_filter( 'views_edit-wholesaler_message', function ( $views ) {
  // Unset unnecessary views
  unset( $views[ 'mine' ] );
  unset( $views[ 'publish' ] );

  $current = '';
  if ( isset($_REQUEST['related_post_type']) ) {
    $current = $_REQUEST['related_post_type'];
  };

  $custom_views_data = [
    'custom' => __( 'Velkoobchody', 'shp-obchodiste' ),
    'product' => __( 'Produkty', 'shp-obchodiste' ),
  ];

  // Genereate custom views
  $custom_views = [];
  foreach ( $custom_views_data as $post_type => $label ) {
    $attr_href = admin_url( 'edit.php?post_type=wholesaler_message&related_post_type=' . $post_type );
    $attr_class = ( $current == $post_type ? 'class="current"' : '' );
    $custom_views[ $post_type ] = '<a href="' . $attr_href . '" ' . $attr_class . '>'. $label . '</a>';
  }

  // Add custom views after "All" item
  return
    array_slice( $views, 0, 1, true ) +
		$custom_views +
    array_slice( $views, 1, 5, true );
} );

/**
 * Remove unimportant formats in mce editor
 */
add_filter( 'tiny_mce_before_init', function ( $formats ) {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( ! user_can( $current_user, 'subscriber' ) ) return $formats;
  $formats[ 'block_formats' ] = 'Paragraph=p;';
  $formats[ 'block_formats' ] .= __( 'Nadpis', 'shp-obchodiste' ) . '=h3;';
  return $formats;
} );

/**
 * Remove unimportant buttons in mce editor
 */
add_filter( 'mce_buttons', function ( $buttons ) {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( ! user_can( $current_user, 'subscriber' ) ) return $buttons;
  $buttons_to_remove = [
    'alignleft',
    'aligncenter',
    'alignright',
    'wp_more',
    'spellchecker',
    'fullscreen',
    'wp_adv',
  ];
  $screen = get_current_screen();
  // Remove format select from wholesaler edit page
  if ( is_admin() && 'post' === $screen->base && 'custom' === $screen->post_type ) {
    $buttons_to_remove[] = 'formatselect';
  }
  $filtered_buttons = array_diff( $buttons, $buttons_to_remove );
  return $filtered_buttons;
} );

/**
 * Add post status to admin list
 */
add_filter( 'display_post_states', function ( $states, $post ) {
  switch ( $post->post_status ) {
    case 'waiting':
    $states[] = __( 'Čeká na zpracování...', 'shp-obchodiste' );
    break;
    case 'done':
    $states[] = __( 'Hotovo', 'shp-obchodiste' );
    break;
    case 'error':
    $states[] = __( 'Chyba', 'shp-obchodiste' );
    break;
  }
  return $states;
}, 10, 2 );

/**
 * Add cron schedule interval options
 */
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['one_second'] = [
		'interval' => 1,
		'display' => __( 'Každou 1 sekundu', 'shp-obchodiste' ),
  ];
  $schedules['one_minute'] = [
		'interval' => 60,
		'display' => __( 'Každou 1 minutu', 'shp-obchodiste' ),
  ];
  $schedules['five_minutes'] = [
		'interval' => 5*60,
		'display' => __( 'Každých 5 minut', 'shp-obchodiste' ),
  ];
	return $schedules;
} );

/**
 * Fix large CSV file upload
 */
add_filter( 'wp_check_filetype_and_ext', function ( $data, $file, $filename, $mimes ) {
  $wp_filetype = wp_check_filetype( $filename, $mimes );

  $ext = $wp_filetype['ext'];
  $type = $wp_filetype['type'];
  $proper_filename = $data['proper_filename'];

  return compact( 'ext', 'type', 'proper_filename' );
}, 10, 4 );