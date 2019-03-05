<div class="special-offer-detail d-flex" id="<?php echo get_post_field( "post_name" ); ?>">

  <div class="flex-shrink-0 mr-3">
    <?php if ( get_field( "image" ) ): ?>
    <a class="special-offer-image d-block colorbox" href="<?php echo get_field( "image" )[ "sizes" ][ "large" ]; ?>">
      <img
        src="<?php echo get_field( "image" )[ "sizes" ][ "medium" ]; ?>"
        alt="<?php echo the_title(); ?>"
      >
    </a>
    <?php else: ?>
    <div class="special-offer-image special-offer-image-empty"></div>
    <?php endif; ?>
  </div>

  <div class="d-md-flex w-100 mt-1">

    <div class="flex-grow-1">

      <h3 class="h4 mb-2 font-weight-bold">
        <?php the_title(); ?>
      </h3>

      <?php if ( get_field( "description" ) ): ?>
      <p class="fs-90 fs-lg-100 mb-2">
        <?php echo get_field( "description" ); ?>
      </p>
      <?php endif; ?>

      <p class="mb-0">
        <?php _e( 'Cena:', 'shp-obchodiste' ); ?>
        <strong><?php echo separate_thousands( get_field( "price" ) ); ?> <?php _e( 'Kč', 'shp-obchodiste' ); ?></strong>
        <?php if ( get_field( "amount" ) ) echo " / " . get_field( "amount" ); ?>
      </p>

    </div>

    <div class="align-self-center mt-2 mt-md-0 ml-md-3">
      
      <a
        href="#wholesalerContactForm"
        class="btn btn-primary"
        data-wholesaler-contact="special-offer"
        data-wholesaler-contact-item="<?php the_title(); ?>"
        role="button"
      >
        <?php _e( 'Mám zájem', 'shp-obchodiste' ); ?>
      </a>
    </div>

  </div>
</div>