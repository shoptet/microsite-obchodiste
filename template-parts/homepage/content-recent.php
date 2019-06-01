<?php
$options = get_fields( 'options' );
$age_test_wholesaler_categories = $options['age_test_wholesaler_categories'];
$the_query = new WP_Query( [
  'post_type' => 'custom',
  'posts_per_page' => 8,
  'post_status' => 'publish',
  'tax_query' => [
    [
      'taxonomy' => 'customtaxonomy',
			'field' => 'term_id',
			'terms' => $age_test_wholesaler_categories,
			'operator' => 'NOT IN',
    ],
  ],
] );
?>
<section class="section section-primary py-5">
  <div class="section-inner container">

    <h2 class="text-center h3 mb-5">
      <?php _e( 'Noví velkoobchodní prodejci', 'shp-obchodiste' ); ?>
    </h2>

    <div class="row row-bordered row-bordered-2-columns no-gutters">
      <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
        <div class="col-12 col-lg-6">

          <?php get_template_part( 'src/template-parts/wholesaler/content', 'tease' ); ?>

        </div>
      <?php endwhile; ?>
    </div>

    <p class="text-center mt-4 mb-0">
      <a
        href="<?php echo get_post_type_archive_link( 'custom' ); ?>"
        class="btn btn-primary btn-lg ws-normal"
      >
        <?php _e( 'Zobrazit všechny velkoobchody', 'shp-obchodiste' ); ?>
      </a>
    </p>

  </div>
</section>
