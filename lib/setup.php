<?php

/**
 * Register custom post types and taxonomies
 */
add_action( 'init', function() {
  register_post_type( 'custom', get_cpt_wholesaler_args() );
  register_taxonomy( 'wholesaler_category', 'custom', get_cpt_wholesaler_taxonomy_args() );
} );
