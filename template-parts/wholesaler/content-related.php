<?php
$terms = get_the_terms( $post->ID, 'customtaxonomy' );
if ( ! $terms ) return; 
$term_ids = array_map( function( $term ) {
  return $term->term_id;
}, $terms );
$the_query = new WP_Query( [
  'post_type' => 'custom',
  'posts_per_page' => 2,
  'post_status' => 'publish',
  'post__not_in' => [ $post->ID ], // exclude current post
  'tax_query' => [ [
    'taxonomy' => 'customtaxonomy',
    'field' => 'term_id',
    'terms' => $term_ids,
  ] ],
] );
?>
<?php if ( $the_query->post_count ): ?>
  <div class="pt-5 pb-4">
    <h2 class="text-center h3 mb-1">
      <?php _e( 'Nezaujal vás tento dodavatel?', 'shp-obchodiste' ); ?>
    </h2>
    <p class="mb-4 text-center mb-5">
      <?php _e( 'Podívejte se na další ze stejné kategorie', 'shp-obchodiste' ); ?>
    </p>
    <div class="row row-bordered no-gutters">
      <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
        <div class="col-12 col-lg-6">
          <?php get_template_part( 'src/template-parts/wholesaler/content', 'tease' ); ?>
        </div>
      <?php endwhile; wp_reset_query(); ?>
    </div>
  </div>
<?php endif ?>
