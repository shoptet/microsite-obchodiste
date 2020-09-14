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
    'hierarchical' => true,
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