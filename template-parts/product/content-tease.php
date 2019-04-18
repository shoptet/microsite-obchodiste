<?php
  $is_product_tease_in_row = ( isset( $GLOBALS[ 'is_product_tease_in_row' ] ) && $GLOBALS[ 'is_product_tease_in_row' ] );
  $is_product_on_homepage = ( isset( $GLOBALS[ 'is_product_on_homepage' ] ) && $GLOBALS[ 'is_product_on_homepage' ] );
?>
<a
  class="product-tease <?php if ( $is_product_on_homepage ) echo 'product-tease-secondary'; ?>"
  href="<?php echo get_permalink(); ?>"
  title="<?php _e( 'Zobrazit produkt', 'shp-obchodiste' ); ?>"
>
  <div class="d-flex">

    <div class="flex-shrink-0">
      <?php if ( $thumbnail = get_field( "thumbnail" ) ): ?>
      <div class="product-tease-image d-block">
        <img
          src="<?php echo $thumbnail[ "sizes" ][ "medium" ]; ?>"
          alt="<?php echo the_title(); ?>"
        >
      </div>
      <?php else: ?>
      <div class="product-tease-image product-image-empty"></div>
      <?php endif; ?>
    </div>

    <div class="<?php if ( $is_product_tease_in_row ) echo 'row no-gutters flex-grow-1 align-items-center'; ?>">

      <div class="<?php if ( $is_product_tease_in_row ) echo 'col-12 col-lg-6'; ?>">

        <h3 class="product-tease-title h6 mb-0">
          <?php the_title(); ?>
        </h3>

        <?php if ( $short_description = get_field( "short_description" ) ): ?>
        <div class="product-tease-description block-ellipsis fs-90 fs-lg-100 mt-2 mb-0">
          <p><?php echo truncate( strip_tags( $short_description ), 110 ); ?></p>
        </div>
        <?php endif; ?>

      </div>

      <?php if ( $is_product_tease_in_row ): ?>
      <div class="col-12 col-lg-3 pl-lg-1 pl-xl-2 text-lg-center mt-lg-0 mt-2 fs-90 text-muted">
        <?php if ( $related_wholesaler = get_field( "related_wholesaler" ) ):?>
        <span class="d-lg-none">Velkoobchod:</span> <span title="<?php _e( 'Velkoobchod', 'shp-obchodiste' ); ?>"><?php echo $related_wholesaler->post_title; ?></span>
        <?php endif; ?>
        </div>
      <?php endif; ?>

      <div class="<?php if ( $is_product_tease_in_row ) echo 'col-12 col-lg-3 pl-lg-1 pl-xl-2 mt-lg-0 text-lg-right'; ?> mt-1">

        <?php if ( $price = get_field( "price" ) ): ?>      
        <p class="mb-0">
          <span class="font-weight-bold <?php if ( $is_product_tease_in_row ) echo 'fs-125'; ?>"><?php echo separate_thousands( $price ); ?></span>
          <?php _e( '<span class="font-weight-bold">Kč</span>&nbsp;/&nbsp;ks', 'shp-obchodiste' ); ?>
        </p>

          <?php if ( $is_product_tease_in_row && $minimal_order = get_field( "minimal_order" ) ): ?>      
          <p class="text-muted mb-0 small">
            <?php _e( 'Min. objednávka', 'shp-obchodiste' ); ?>
            <span>
              <?php echo separate_thousands( $minimal_order ); ?>&nbsp;<?php _e( 'ks', 'shp-obchodiste' ); ?>
            </span>
          </p>
          <?php endif; ?>
        <?php else: ?>
        <p class="mb-0 font-weight-bold">
          <?php _e( 'Cena na požádání', 'shp-obchodiste' ); ?>
        </p>
        <?php endif; ?>

      </div>
    </div>
  </div>

</a>
