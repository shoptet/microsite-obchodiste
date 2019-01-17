<?php

/**
 * Returns wholesaler custom post type arguments
 */
function get_cpt_wholesaler_args(): array
{
  $labels = [
    'name' => __( 'Velkoobchody', 'shp-obchodiste' ),
    'singular_name' => __( 'Velkoobchod', 'shp-obchodiste' ),
    'menu_name' => __( 'Velkoobchody', 'shp-obchodiste' ),
    'all_items' => __( 'Všechny velkoobchody', 'shp-obchodiste' ),
    'add_new' => __( 'Přidat nový', 'shp-obchodiste' ),
    'add_new_item' => __( 'Přidat nový velkoobchod', 'shp-obchodiste' ),
    'edit_item' => __( 'Upravit velkoobchod', 'shp-obchodiste' ),
    'new_item' => __( 'Nový velkoobchod', 'shp-obchodiste' ),
    'view_item' => __( 'Zobrazit velkoobchod', 'shp-obchodiste' ),
    'view_items' => __( 'Zobrazit velkoobchody', 'shp-obchodiste' ),
    'search_items' => __( 'Vyhledat velkoobchod', 'shp-obchodiste' ),
    'not_found' => __( 'Nebyl nalezen žádný velkoobchod', 'shp-obchodiste' ),
    'not_found_in_trash' => __( 'V koši nebyl nalezen žádný velkoobchod', 'shp-obchodiste' ),
    'archives' => __( 'Archiv velkoobchodů', 'shp-obchodiste' ),
    'items_list' => __( 'Výpis velkoobchodů', 'shp-obchodiste' ),
  ];
  $args = [
    'label' => __( 'Velkoobchody', 'shp-obchodiste' ),
    'labels' => $labels,
    'description' => '',
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_rest' => false,
    'rest_base' => '',
    'has_archive' => 'velkoobchody',
    'show_in_menu' => true,
    'exclude_from_search' => false,
    'capability_type' => 'wholesaler',
    'map_meta_cap' => true,
    'hierarchical' => false,
    'rewrite' => [ 'slug' => 'velkoobchod', 'with_front' => true ],
    'query_var' => true,
    'menu_icon' => 'dashicons-store',
    'supports' => [ 'title', 'thumbnail' ],
  ];
  return $args;
}

/**
 * Returns wholesaler custom post type taxonomy arguments
 */
function get_cpt_wholesaler_taxonomy_args(): array
{
  $labels = [
    'name' => __( 'Kategorie', 'shp-obchodiste' ),
    'singular_name' => __( 'Kategorie', 'shp-obchodiste' ),
  ];
  $args = [
    'label' => __( 'Kategorie', 'shp-obchodiste' ),
    'labels' => $labels,
    'public' => true,
    'hierarchical' => false,
    'label' => 'Kategorie',
    'show_ui' => true,
    'show_in_menu' => true,
    'show_in_nav_menus' => true,
    'query_var' => true,
    'rewrite' => [ 'slug' => 'velkoobchody', 'with_front' => true ],
    'show_admin_column' => false,
    'show_in_rest' => false,
    'rest_base' => '',
    'show_in_quick_edit' => false,
  ];
  return $args;
}

/**
 * Returns wholesaler message custom post type arguments
 */
function get_cpt_wholesaler_message_args(): array
{
  $labels = [
    'name' => __( 'Zprávy', 'shp-obchodiste' ),
    'singular_name' => __( 'Zpráva', 'shp-obchodiste' ),
    'menu_name' => __( 'Zprávy', 'shp-obchodiste' ),
    'all_items' => __( 'Všechny zprávy', 'shp-obchodiste' ),
    'add_new' => __( 'Přidat novou', 'shp-obchodiste' ),
    'add_new_item' => __( 'Přidat novou zprávu', 'shp-obchodiste' ),
    'edit_item' => __( 'Upravit zprávu', 'shp-obchodiste' ),
    'new_item' => __( 'Nová zpráva', 'shp-obchodiste' ),
    'view_item' => __( 'Zobrazit zprávu', 'shp-obchodiste' ),
    'view_items' => __( 'Zobrazit zprávy', 'shp-obchodiste' ),
    'search_items' => __( 'Vyhledat zprávu', 'shp-obchodiste' ),
    'not_found' => __( 'Nebyla nalezena žádná zpráva', 'shp-obchodiste' ),
    'not_found_in_trash' => __( 'V koši nebyla nalezena žádná zpráva', 'shp-obchodiste' ),
    'archives' => __( 'Archiv zpráv', 'shp-obchodiste' ),
    'items_list' => __( 'Výpis zpráv', 'shp-obchodiste' ),
  ];
  $args = [
    'label' => __( 'Zprávy', 'shp-obchodiste' ),
    'labels' => $labels,
    'description' => '',
    'public' => false,
    'publicly_queryable' => false,
    'show_ui' => true,
    'show_in_rest' => false,
    'rest_base' => '',
    'has_archive' => false,
    'show_in_menu' => true,
    'exclude_from_search' => true,
    'capability_type' => 'message',
    'capabilities' => [
      'create_posts' => 'do_not_allow',
    ],
    'map_meta_cap' => true,
    'hierarchical' => false,
    'rewrite' => false,
    'query_var' => true,
    'menu_icon' => 'dashicons-testimonial',
    'supports' => [ 'title' ],
  ];
  return $args;
}

/**
 * Returns special offer custom post type arguments
 */
function get_cpt_special_offer_args(): array
{
  $labels = [
    'name' => __( 'Akční nabídka', 'shp-obchodiste' ),
    'singular_name' => __( 'Akční nabídka', 'shp-obchodiste' ),
    'menu_name' => __( 'Akční nabídka', 'shp-obchodiste' ),
    'all_items' => __( 'Všechny nabídky', 'shp-obchodiste' ),
    'add_new' => __( 'Přidat novou', 'shp-obchodiste' ),
    'add_new_item' => __( 'Přidat novou nabídku', 'shp-obchodiste' ),
    'edit_item' => __( 'Upravit nabídku', 'shp-obchodiste' ),
    'new_item' => __( 'Nová nabídka', 'shp-obchodiste' ),
    'view_item' => __( 'Zobrazit nabídku', 'shp-obchodiste' ),
    'view_items' => __( 'Zobrazit nabídku', 'shp-obchodiste' ),
    'search_items' => __( 'Vyhledat nabídka', 'shp-obchodiste' ),
    'not_found' => __( 'Nebyla nalezena žádná nabídka', 'shp-obchodiste' ),
    'not_found_in_trash' => __( 'V koši nebyla nalezena žádná nabídka', 'shp-obchodiste' ),
    'archives' => __( 'Archiv nabídek', 'shp-obchodiste' ),
    'items_list' => __( 'Výpis nabídek', 'shp-obchodiste' ),
  ];
  $args = [
    'label' => __( 'Akční nabídka', 'shp-obchodiste' ),
    'labels' => $labels,
    'description' => '',
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_rest' => false,
    'rest_base' => '',
    'has_archive' => 'akcni-nabidka',
    'show_in_menu' => true,
    'exclude_from_search' => false,
    'capability_type' => 'special_offer',
    'map_meta_cap' => true,
    'hierarchical' => false,
    'rewrite' => false,
    'query_var' => true,
    'rewrite' => [ 'slug' => 'akcni-nabidka', 'with_front' => true ],
    'menu_icon' => 'dashicons-megaphone',
    'supports' => [ 'title' ],
  ];
  return $args;
}