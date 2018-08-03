<?php

/**
 * Returns wholesaler custom post type arguments
 */
function get_cpt_wholesaler_args(): array
{
  $labels = [
    'name' => __( 'Velkoobchody', '' ),
    'singular_name' => __( 'Velkoobchod', '' ),
    'menu_name' => __( 'Velkoobchody', '' ),
    'all_items' => __( 'Všechny velkoobchody', '' ),
    'add_new' => __( 'Přidat nový', '' ),
    'add_new_item' => __( 'Přidat nový velkoobchod', '' ),
    'edit_item' => __( 'Upravit velkoobchod', '' ),
    'new_item' => __( 'Nový velkoobchod', '' ),
    'view_item' => __( 'Zobrazit velkoobchod', '' ),
    'view_items' => __( 'Zobrazit velkoobchody', '' ),
    'search_items' => __( 'Vyhledat velkoobchod', '' ),
    'not_found' => __( 'Nebyl nalezen žádný velkoobchod', '' ),
    'not_found_in_trash' => __( 'V koši nebyl nalezen žádný velkoobchod', '' ),
    'archives' => __( 'Archiv velkoobchodů', '' ),
    'items_list' => __( 'Výpis velkoobchodů', '' ),
  ];
  $args = [
    'label' => __( 'Velkoobchody', '' ),
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
    'name' => __( 'Kategorie', '' ),
    'singular_name' => __( 'Kategorie', '' ),
  ];
  $args = [
    'label' => __( 'Kategorie', '' ),
    'labels' => $labels,
    'public' => true,
    'hierarchical' => false,
    'label' => 'Kategorie',
    'show_ui' => true,
    'show_in_menu' => true,
    'show_in_nav_menus' => true,
    'query_var' => true,
    'rewrite' => [ 'slug' => 'wholesaler_category', 'with_front' => true ],
    'show_admin_column' => false,
    'show_in_rest' => false,
    'rest_base' => '',
    'show_in_quick_edit' => false,
  ];
  return $args;
}
