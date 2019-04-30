<a
  class="special-offer-tease <?php echo ( isset( $GLOBALS[ 'is_special_offer_tease_secondary' ] ) && $GLOBALS[ 'is_special_offer_tease_secondary' ] ? 'special-offer-tease-secondary' : '' ) ?>"
  href="<?php echo get_permalink( get_field( "related_wholesaler" ) ) . "#" . get_post_field( "post_name" ); ?>"
  title="<?php _e( 'Zobrazit nabídku', 'shp-obchodiste' ); ?>"
>
  <div class="d-flex">

    <div class="flex-shrink-0 mr-3">
      <?php if ( get_field( "image" ) ): ?>
      <div class="special-offer-tease-image d-block">
        <img
          src="<?php echo get_field( "image" )[ "sizes" ][ "medium" ]; ?>"
          alt="<?php echo the_title(); ?>"
        >
      </div>
      <?php else: ?>
      <div class="special-offer-tease-image special-offer-image-empty"></div>
      <?php endif; ?>
    </div>

    <div class="mt-1">

      <h3 class="special-offer-tease-title h6 mb-0">
        <?php the_title(); ?>
      </h3>

      <?php if ( get_field( "description" ) ): ?>
      <p class="special-offer-tease-description fs-90 fs-lg-100 mt-2 mb-0">
        <?php echo truncate( strip_tags( get_field( "description" ) ), 110 ); ?>
      </p>
      <?php endif; ?>

      <p class="mt-2 mb-0">
        <?php _e( 'Cena:', 'shp-obchodiste' ); ?>
        <strong><?php echo separate_thousands( get_field( "price" ), true ); ?> <?php _e( 'Kč', 'shp-obchodiste' ); ?></strong>
        <?php if ( get_field( "amount" ) ) echo " / " . get_field( "amount" ); ?>
      </p>

    </div>

  </div>

</a>
