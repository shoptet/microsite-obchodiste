<?php
if ( $related_wholesaler = get_field( "related_wholesaler" ) ):
$the_query = new WP_Query( [
  'post_type' => 'product',
  'posts_per_page' => 12,
  'post_status' => 'publish',
  'post__not_in' => [ $post->ID ], // exclude current post
  'orderby' => 'rand',
  'ep_integrate' => true,
  'meta_query' => [
    [
      'key' => 'related_wholesaler',
      'value' => $related_wholesaler->ID,
    ],
  ],
] );
?>
<?php if ( $the_query->have_posts() ): ?>
  <div class="pt-5 pb-4">
    <h2 class="text-center h3 mb-5">
      <?php
      printf(
        __( 'Velkoobchod %s dále nabízí', 'shp-obchodiste' ),
        $related_wholesaler->post_title
      );
      ?>
    </h2>
    <div class="row row-bordered row-bordered-3-columns no-gutters">
      <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
        <div class="col-12 col-md-6 col-xl-4">
          <?php get_template_part( 'src/template-parts/product/content', 'tease' ); ?>
        </div>
      <?php endwhile; wp_reset_query(); ?>
    </div>
  </div>
<?php
endif;
endif;
 ?>
