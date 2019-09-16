<?php
  $hide_wholesaler_in_product_list = ( isset( $GLOBALS[ 'hide_wholesaler_in_product_list' ] ) && $GLOBALS[ 'hide_wholesaler_in_product_list' ] );
?>
<div class="list-bordered-item d-none d-lg-block bg-light font-weight-bold py-2 px-3">
  <div class="d-flex">
    <div class="flex-shrink-0 mr-3">
      <div class="product-tease-image-placeholder"></div>
    </div>
    <div class="row w-100">
      <div class="col-12 <?php echo ($hide_wholesaler_in_product_list ? 'col-lg-8' : 'col-lg-6' ); ?>">
        <?php _e( 'Název a popis produktu', 'shp-obchodiste' ); ?>
      </div>
      <?php if ( ! $hide_wholesaler_in_product_list ): ?>
      <div class="col-12 col-lg-3 text-center">
        <?php _e( 'Velkoobchod', 'shp-obchodiste' ); ?>
      </div>
      <?php endif; ?>
      <div class="col-12 <?php echo ($hide_wholesaler_in_product_list ? 'col-lg-4' : 'col-lg-3' ); ?> text-right">
        <?php _e( 'Cena', 'shp-obchodiste' ); ?>
      </div>
    </div>
  </div>
</div>