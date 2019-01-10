<?php
$the_query = new WP_Query( [
  'post_type' => 'special_offer',
  'posts_per_page' => 14,
  'post_status' => 'publish',
] );
?>
<section class="section section-primary py-5">
  <div class="section-inner container">

    <h2 class="text-center h3 mb-5">
      <?php _e( 'Akční nabídka', 'shp-obchodiste' ); ?>
    </h2>

    <div class="row row-bordered-one-row no-gutters">
      <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
        <div class="col-12 col-lg-4">

          <?php get_template_part( 'src/template-parts/special_offer/content', 'tease' ); ?>

        </div>
      <?php endwhile; ?>
    </div>

  </div>
</section>
