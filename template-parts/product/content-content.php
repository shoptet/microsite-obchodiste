<div class="row product">

  <div class="col-12 col-lg-7 col-xl-8" itemscope itemtype="http://schema.org/Product">

    <?php get_template_part( 'src/template-parts/product/content', 'header' ); ?>

    <?php if ( $description = get_field( "description" ) ): ?>
    <div class="product-detail">
      <h2 class="h3">
        <?php _e( 'Popis produktu', 'shp-obchodiste' ); ?>
      </h2>
      <?php echo $description; ?>
    </div>
    <?php endif; ?>

  </div>
  <div class="col-12 col-lg-5 col-xl-4">

    <?php
      if ( $related_wholesaler = get_field( "related_wholesaler" ) ):
      // Set global post variable to related wholesaler post
      global $post; 
      $post = get_post( $related_wholesaler->ID );
      setup_postdata( $post );
    ?>

    <?php get_template_part( 'src/template-parts/wholesaler/content', 'contact-company' ); ?>

    <?php get_template_part( 'src/template-parts/wholesaler/content', 'contact-person' ); ?>

    <?php get_template_part( 'src/template-parts/wholesaler/content', 'contact-form' ); ?>

    <?php
      wp_reset_postdata();
      endif;
    ?>

  </div>
</div>
