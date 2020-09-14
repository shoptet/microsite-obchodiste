<?php

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
