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
    'all_items' => __( 'Všechny medailonky', 'shp-obchodiste' ),
    'add_new' => __( 'Přidat nový', 'shp-obchodiste' ),
    'add_new_item' => __( 'Přidat nový medailonek', 'shp-obchodiste' ),
    'edit_item' => __( 'Upravit medailonek', 'shp-obchodiste' ),
    'new_item' => __( 'Nový medailonek', 'shp-obchodiste' ),
    'view_item' => __( 'Zobrazit medailonek', 'shp-obchodiste' ),
    'view_items' => __( 'Zobrazit medailonky', 'shp-obchodiste' ),
    'search_items' => __( 'Vyhledat medailonek', 'shp-obchodiste' ),
    'not_found' => __( 'Nebyl nalezen žádný medailonek', 'shp-obchodiste' ),
    'not_found_in_trash' => __( 'V koši nebyl nalezen žádný medailonek', 'shp-obchodiste' ),
    'archives' => __( 'Archiv velkoobchodů', 'shp-obchodiste' ),
    'items_list' => __( 'Výpis medailonků', 'shp-obchodiste' ),
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
    'supports' => [ 'title', 'thumbnail', 'author' ],
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
    'supports' => [ 'title', 'author' ],
  ];
  return $args;
}

/**
 * Returns product custom post type arguments
 */
function get_cpt_product_args(): array
{
  $labels = [
    'name' => __( 'Produkty', 'shp-obchodiste' ),
    'singular_name' => __( 'Produkt', 'shp-obchodiste' ),
    'menu_name' => __( 'Produkty', 'shp-obchodiste' ),
    'all_items' => __( 'Všechny produkty', 'shp-obchodiste' ),
    'add_new' => __( 'Přidat nový', 'shp-obchodiste' ),
    'add_new_item' => __( 'Přidat nový produkt', 'shp-obchodiste' ),
    'edit_item' => __( 'Upravit produkt', 'shp-obchodiste' ),
    'new_item' => __( 'Nový produkt', 'shp-obchodiste' ),
    'view_item' => __( 'Zobrazit produkt', 'shp-obchodiste' ),
    'view_items' => __( 'Zobrazit produkty', 'shp-obchodiste' ),
    'search_items' => __( 'Vyhledat produkty', 'shp-obchodiste' ),
    'not_found' => __( 'Nebyl nalezen žádný produkt', 'shp-obchodiste' ),
    'not_found_in_trash' => __( 'V koši nebyl nalezen žádný produkt', 'shp-obchodiste' ),
    'archives' => __( 'Archiv produktů', 'shp-obchodiste' ),
    'items_list' => __( 'Výpis produktů', 'shp-obchodiste' ),
  ];
  $args = [
    'label' => __( 'Produkty', 'shp-obchodiste' ),
    'labels' => $labels,
    'description' => '',
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_rest' => false,
    'rest_base' => '',
    'has_archive' => 'produkty',
    'show_in_menu' => true,
    'exclude_from_search' => false,
    'capability_type' => 'product',
    'map_meta_cap' => true,
    'hierarchical' => false,
    'query_var' => true,
    'rewrite' => [ 'slug' => 'produkt', 'with_front' => true ],
    'menu_icon' => 'dashicons-cart',
    'supports' => [ 'title', 'thumbnail', 'author' ],
  ];
  return $args;
}

/**
 * Returns product custom post type taxonomy arguments
 */
function get_cpt_product_taxonomy_args(): array
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
    'rewrite' => [ 'slug' => 'produkty', 'with_front' => true ],
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
function get_cpt_sync_args(): array
{
  $labels = [
    'name' => __( 'Synchronizace', 'shp-obchodiste' ),
    'singular_name' => __( 'Synchronizace', 'shp-obchodiste' ),
    'menu_name' => __( 'Synchronizace', 'shp-obchodiste' ),
    'all_items' => __( 'Všechny položky', 'shp-obchodiste' ),
    'add_new' => __( 'Přidat novou', 'shp-obchodiste' ),
    'add_new_item' => __( 'Přidat novou položku', 'shp-obchodiste' ),
    'edit_item' => __( 'Upravit položku', 'shp-obchodiste' ),
    'new_item' => __( 'Nová položka', 'shp-obchodiste' ),
    'view_item' => __( 'Zobrazit položku', 'shp-obchodiste' ),
    'view_items' => __( 'Zobrazit položky', 'shp-obchodiste' ),
    'search_items' => __( 'Vyhledat položku', 'shp-obchodiste' ),
    'not_found' => __( 'Nebyla nalezena žádná položka', 'shp-obchodiste' ),
    'not_found_in_trash' => __( 'V koši nebyla nalezena žádná položka', 'shp-obchodiste' ),
    'archives' => __( 'Archiv položek', 'shp-obchodiste' ),
    'items_list' => __( 'Výpis položek', 'shp-obchodiste' ),
  ];
  $args = [
    'label' => __( 'Synchronizace', 'shp-obchodiste' ),
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
    'capability_type' => 'sync',
    'capabilities' => [
      'create_posts' => 'do_not_allow',
    ],
    'map_meta_cap' => true,
    'hierarchical' => false,
    'rewrite' => false,
    'query_var' => true,
    'menu_icon' => 'dashicons-update',
    'supports' => [ 'title' ],
  ];
  return $args;
}