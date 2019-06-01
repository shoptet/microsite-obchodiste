<?php
$options = get_fields( 'options' );
$age_test_product_categories = $options['age_test_product_categories'];
$the_query = new WP_Query( [
  'post_type' => 'product',
  'posts_per_page' => 18,
  'post_status' => 'publish',
  'orderby' => 'rand',
  'tax_query' => [
    [
      'taxonomy' => 'producttaxonomy',
			'field' => 'term_id',
			'terms' => $age_test_product_categories,
			'operator' => 'NOT IN',
    ],
  ],
] );
?>
<?php if ( $the_query->have_posts() ) : ?>
  <section class="section section-secondary bg-secondary-light py-5">
    <div class="section-inner container">

      <h2 class="text-center h3 mb-5">
        <?php _e( 'Zboží od velkoobchodů, které můžete prodávat', 'shp-obchodiste' ); ?>
      </h2>

      <div class="row row-bordered row-bordered-3-columns no-gutters">
        <?php $GLOBALS[ 'is_product_on_homepage' ] = true; ?>
        <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
          <div class="col-12 col-md-6 col-xl-4">

            <?php get_template_part( 'src/template-parts/product/content', 'tease' ); ?>

          </div>
        <?php endwhile; wp_reset_query(); ?>
        <?php unset( $GLOBALS[ 'is_product_on_homepage' ] ); ?>
      </div>

      <p class="text-center mt-4 mb-0">
        <a
          href="<?php echo get_post_type_archive_link( 'product' ); ?>"
          class="btn btn-primary btn-lg ws-normal"
        >
          <?php _e( 'Zobrazit nabídky produktů od velkoobchodů', 'shp-obchodiste' ); ?>
        </a>
      </p>

    </div>
  </section>
<?php endif; ?>