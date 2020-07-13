<?php
$the_query = new WP_Query( [
  'post_type' => 'product',
  'posts_per_page' => 10,
  'ep_integrate' => true,
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
  <div class="row">
    <div class="col-12 col-lg-10 offset-lg-1">
      <h2 class="h3 mb-5 text-center">
        <?php
        printf(
          __( 'Produkty od dodavatele %s', 'shp-obchodiste' ),
          get_the_title()
        );
        ?>
      </h2>
      <div class="list-bordered">
        <?php $GLOBALS[ 'hide_wholesaler_in_product_list' ] = true; ?>
        <?php get_template_part( 'src/template-parts/product/content', 'list-header' ); ?>
        <?php $GLOBALS[ 'is_product_tease_in_row' ] = true; ?>
        <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
          <div class="list-bordered-item">
            <?php get_template_part( 'src/template-parts/product/content', 'tease' ); ?>
          </div>
        <?php endwhile; wp_reset_query(); ?>
        <?php unset( $GLOBALS[ 'hide_wholesaler_in_product_list' ] ); ?>
        <?php unset( $GLOBALS[ 'is_product_tease_in_row' ] ); ?>
      </div>
    </div>
  </div>
  
  <p class="text-center mt-4 mb-0">
    <a
      href="<?php echo get_post_type_archive_link( 'product' ); ?>?related_wholesaler[]=<?php echo $post->ID; ?>"
      class="btn btn-primary btn-lg ws-normal"
    >
      <?php _e( 'Zobrazit všechny produkty', 'shp-obchodiste' ); ?>
    </a>
  </p>

</div>
<?php endif; ?>