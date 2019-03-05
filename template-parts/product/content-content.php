<div class="row product" itemscope itemtype="http://schema.org/Organization">
  <div class="col-12 col-lg-7 col-xl-8">

    <?php get_template_part( 'src/template-parts/product/content', 'header' ); ?>

    <?php if ( get_field( "description" ) ): ?>
    <h2 class="h-heavy mb-1">
      <?php _e( 'Popis produktu', 'shp-obchodiste' ); ?>
    </h2>
    <?php the_field( "description" ); ?>
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
