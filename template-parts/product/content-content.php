<div class="row product">

  <div class="col-12 col-lg-7 col-xl-8">

    <?php get_template_part( 'src/template-parts/product/content', 'header' ); ?>

    <?php if ( $description = get_field( "description" ) ): ?>
    <div class="product-detail">
      <h2 class="h3">
        <?php _e( 'Popis produktu', 'shp-obchodiste' ); ?>
      </h2>
      <?php echo wp_kses( $description, get_product_description_allowed_html() ); ?>
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

    <?php
      wp_reset_postdata();
      endif;
    ?>

    <?php get_template_part( 'src/template-parts/wholesaler/content', 'contact-form' ); ?>

  </div>
</div>

<div class="mt-4 mb-2 text-right small">
  <a
    class="text-muted"
    href="mailto:info@obchodiste.cz?subject=<?php _e( 'Hlašení nelegálního obsahu', 'shp-obchodiste' ); ?>&body=<?php echo get_permalink(); ?>"
    target="_blank"
  >
    <?php _e( 'Myslíte si, že tento produkt je v rozporu se zákonem?', 'shp-obchodiste' ); ?>
  </a>
</div>

<?php 
  $banner_post = null;
  if ( $term = get_field('category') ) {
    $banner_post = get_ad_banner_by_term( $term->term_id );
  }
?>
<?php if ($banner_post): ?>
<div class="mt-5">
  <?php
    global $post;
    $post = $banner_post;
    setup_postdata($banner_post);
    get_template_part( 'src/template-parts/common/content', 'ad-banner' );
    wp_reset_postdata();
  ?>
</div>
<?php endif; ?>