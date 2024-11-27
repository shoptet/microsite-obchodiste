<?php

/**
 * Disable slow unused queries on single page
 */
add_filter( 'get_previous_post_where', 'handle_get_prev_next_post_where' );
add_filter( 'get_next_post_where', 'handle_get_prev_next_post_where' );
function handle_get_prev_next_post_where () {
  return 'WHERE 0';
}

require_once( ABSPATH . 'wp-admin/includes/screen.php' );

add_filter( 'shoptet_post_count_query_args', function($query_args) {
  return [
    'obchodisteLeadsCount' => [
      'post_type' => 'wholesaler_message',
      'post_status' => 'publish',
    ],
    'obchodisteProductsCount' => [
      'post_type' => 'product',
      'post_status' => 'publish',
    ],
    'obchodisteProjectsCount' => [
      'post_type' => 'custom',
      'post_status' => 'publish',
    ],
  ];
} );

/**
 * Remove Yoast page analysis columns from post lists
 */
add_filter( 'manage_edit-product_columns', 'remove_yoast_columns' );
add_filter( 'manage_edit-custom_columns', 'remove_yoast_columns' );
function remove_yoast_columns ($columns) {
  unset($columns['wpseo-score']);
  unset($columns['wpseo-score-readability']);
  unset($columns['wpseo-title']);
  unset($columns['wpseo-metadesc']);
  unset($columns['wpseo-focuskw']);
  unset($columns['wpseo-links']);
  unset($columns['wpseo-linked']);
  return $columns;
}

add_filter( 'get_terms_args', function( $args, $taxonomies ) {
  if (
    in_array( 'producttaxonomy', $taxonomies ) ||
    in_array( 'customtaxonomy', $taxonomies )
  ) {
    $args['hierarchical'] = false;
  }
  // Force rewrite default filter
  if ( isset( $args['hierarchical_force'] ) ) {
    $args['hierarchical'] = $args['hierarchical_force'];
  }
  return $args;
}, 10, 2 );

/**
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

    $terms = get_terms( [
      'taxonomy' => $data['taxonomy'],
      'parent' => 0,
      'hide_empty' => true,
      'hierarchical_force' => true,
    ] );

    $menu_items_html .= '
      <li class="shp_menu-item shp_navigation-submenu-wide has-dropdown">
        <a class="shp_menu-item-link" href="' . get_post_type_archive_link( $post_type ) . '">
        ' . $data['title'] . '
        </a>
        <span class="caret dropdown-toggle" data-target="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></span>
        <ul class="shp_navigation-submenu dropdown-menu dropdown-menu-right">
          <li class="shp_menu-item">
            <a class="shp_menu-item-link dropdown-item" href="' . get_post_type_archive_link( $post_type ) . '">
            ' . __( 'Všechny kategorie', 'shp-obchodiste' ) . '
            </a>
          </li>
    ';

    foreach ( $terms as $term ) {
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
    set_post_thumbnail( $post_id, $value );
    remove_placeholder_logo( $post_id );
  } else {
    generate_placeholder_logo( $post_id );
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
    set_post_thumbnail( $post_id, $value );
  }
  return $value;
}, 10, 3 );

/**
 * Update wholesaler breadcrumb items
 */
add_filter( 'wpseo_breadcrumb_links', function( $crumbs ) {
  if ( is_singular( 'custom' ) ) {

    // Add only homepage and current page
    $crumbs_copy = [];
    for ( $i = 0, $len = count($crumbs); $i < $len; $i++ ) {
      if ( $i == 0 || $i == ($len-1) ) {
        $crumbs_copy[] = $crumbs[$i];
      }
    }
    $crumbs = $crumbs_copy;

    // Add main category in the middle
    $term = get_term( get_field( 'category' ) );
    if ( is_wp_error( $term ) ) {
      return $crumbs;
    }
    $term_crumb = [
      'url'  => get_term_link($term),
      'text' => $term->name,
    ];

    array_splice( $crumbs, 1, 0, [ $term_crumb ] );

  } else if ( is_singular( 'product' ) ) {
    // Remove product archive link from breadcrumbs
    array_splice( $crumbs, 1, 1 );

    if ( $related_wholesaler = get_field( 'related_wholesaler' ) ) {
      // Add related wholesaler link to breadcrumbs
      $post_crumb = [
        'url'  => get_permalink($related_wholesaler),
        'text' => $related_wholesaler->post_title,
      ];
      array_splice( $crumbs, 1, 0, [ $post_crumb ] );

      // Add related wholesaler main category link to breadcrumbs
      $term_crumb = [ 'term' => get_field( 'category', $related_wholesaler->ID ) ];
      $term = get_term( get_field( 'category', $related_wholesaler->ID ) );
      if ( is_wp_error( $term ) ) {
        return $crumbs;
      }
      $term_crumb = [
        'url'  => get_term_link($term),
        'text' => $term->name,
      ];
      array_splice( $crumbs, 1, 0, [ $term_crumb ] );
    }
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
 * Remove wholesaler and product list views for subscriber
 */
function remove_list_view_for_subscribers($views) {
  global $current_user;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( user_can( $current_user, 'subscriber' ) ) return [];
  return $views;
}
add_filter( 'views_edit-custom', 'remove_list_view_for_subscribers', 11);
add_filter( 'views_edit-product', 'remove_list_view_for_subscribers', 11);

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
  preg_match( '~[a-z]+://\S+~', $email[ 'message' ], $match ); // Get password url from message
  if ( empty( $match ) ) {
    capture_sentry_message( 'Cannot get password url from the new user notification e-mail' );
    $set_password_url = __( 'Kontaktuje nás prosím', 'shp-obchodiste' );
  } else {
    $set_password_url = $match[0];
  }

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
 * Remove wholesaler and product quick edit action for subscribers
 */
add_filter( 'post_row_actions', function( $actions, $post ) {
  global $current_user;
	wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( ! user_can( $current_user, 'subscriber' ) ) return $actions;
  unset( $actions['inline hide-if-no-js'] );
  return $actions;
}, 10, 2 );

/**
 * Remove wholesaler and product bulk actions for subscribers
 */
function remove_bulk_actions_for_subscribers( $actions ) {
  global $current_user;
  wp_get_current_user(); // Make sure global $current_user is set, if not set it
  if ( user_can( $current_user, 'subscriber' ) ) unset( $actions['edit'] );
  return $actions;
}
add_filter( 'bulk_actions-edit-custom', 'remove_bulk_actions_for_subscribers' );
// add_filter( 'bulk_actions-edit-product', 'remove_bulk_actions_for_subscribers' );

/**
 * Show only publish owner wholesalers in product edit page
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
  if ( ! isset( $screen->base ) || 'product_page_product-import' !== $screen->base ) return $value;
  if ( ! user_can( $current_user, 'subscriber' ) ) return NULL;
  if ( $related_wholesaler = get_user_wholesaler( $current_user ) )
    return $related_wholesaler->ID;
  return NULL;
} );

/**
 * Show parent terms in product taxonomy ACF field
 */
add_filter( 'acf/fields/taxonomy/result/name=product_category', 'handle_category_acf_field', 10, 4 );
add_filter( 'acf/fields/taxonomy/result/name=category', 'handle_category_acf_field', 10, 4 );
add_filter( 'acf/fields/taxonomy/result/name=wholesaler_tax_terms', 'handle_category_acf_field', 10, 4 );
add_filter( 'acf/fields/taxonomy/result/name=product_tax_terms', 'handle_category_acf_field', 10, 4 );
function handle_category_acf_field ( $title, $term, $field, $post_id ) {
  $allowed_taxonomies = [
    'producttaxonomy',
    'customtaxonomy',
  ];
  if ( ! in_array( $term->taxonomy, $allowed_taxonomies ) ) return $title;
  $args = [
    'link' => false,
    'separator' => ' > ',
    'inclusive' => false,
  ];
  $parent_terms = get_term_parents_list( $term->term_id, $term->taxonomy, $args );
  $title = sprintf( '<span style="opacity:.5">%s</span><strong>%s</strong>', $parent_terms, $term->name );
  return $title;
};

/**
 * Show only parent categories in wholesaler taxonomy ACF field
 */
add_filter( 'acf/fields/taxonomy/query', function ( $args, $field ) {
  if (
    in_array( $field['name'], [ 'category', 'minor_category_1', 'minor_category_2' ] ) &&
    'customtaxonomy' == $args['taxonomy']
  ) {
    $args['parent'] = 0;
  }
  return $args;
}, 10, 2 );


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

  // do not validate empty value
  if ( empty( $value ) ) return $valid;

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
  if ( is_post_type_archive( [ 'custom', 'product' ] ) )
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
 * Make the first capital letter only if the entire product name is capitalized
 */
add_filter( 'product_title_import', function ( $product_title ) {
  if ( mb_strtoupper( $product_title ) == $product_title ) {
    $product_title = ucfirst( mb_strtolower( $product_title ) );
  }
  return $product_title;
} );

/**
 * Append project title to wholesaler meta title
 */
add_filter( 'wpseo_title', function ( $title ) {
  if ( is_singular( 'custom' ) && $project_title = get_field( 'project_title' ) ) {
    $post_title = get_the_title();
    $site_name = get_bloginfo( 'name' );
    $title = sprintf( '%s (%s) &ndash; %s', $post_title, $project_title, $site_name );
  }
  return $title;
} );

/**
 * Post Count API Plugin: Increase leads count of fake message number
 */
add_filter( 'post-count-api-items', function ( $items ) {
  if ( isset( $items['obchodisteLeadsCount'] ) ) {
    $options = get_fields( 'options' );
    $fake_message_number = ( isset( $options[ 'fake_message_number' ] ) ) ? intval( $options[ 'fake_message_number' ] ) : 0;
    $items['obchodisteLeadsCount'] = intval( $items['obchodisteLeadsCount'] ) + $fake_message_number;
  }
  return $items;
} );