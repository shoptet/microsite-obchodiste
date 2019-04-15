<?php
$the_query = new WP_Query( [
  'post_type' => 'special_offer',
  'posts_per_page' => -1,
  'meta_query' => [
    [
      'key' => 'related_wholesaler',
      'value' => $post->ID,
    ],
  ],
] );
?>
<?php if ( $the_query->post_count ): ?>
  <div class="pt-5 pb-4">
    <h2 class="h2 mb-4">
      <?php _e( 'Akční nabídka', 'shp-obchodiste' ); ?>
    </h2>
    <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
      <?php get_template_part( 'src/template-parts/wholesaler/content-special-offer', 'detail' ); ?>
    <?php endwhile; wp_reset_query(); ?>
  </div>
<?php endif ?>
