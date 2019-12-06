<?php
$options = get_fields( 'options' );
$fake_message_number = ( isset( $options[ 'fake_message_number' ] ) ) ? (int) $options[ 'fake_message_number' ] : 0;
$custom_post_count = intval( get_option( 'post_count_custom', 0 ) );
$product_post_count = intval( get_option( 'post_count_product', 0 ) );
$message_post_count = intval( get_option( 'post_count_wholesaler_message', 0 ) );
?>
<div class="row-status">
  <div class="container">
    <p class="h3 mb-0">
      <?php
      printf(
        __( '<strong>%s</strong>&nbsp;velkoobchodů s&nbsp;nabídkou <strong>%s</strong>&nbsp;produktů obdrželo <strong>%s</strong>&nbsp;poptávek od e-shopů', 'shp-obchodiste' ),
        separate_thousands( $custom_post_count ),
        separate_thousands( $product_post_count ),
        separate_thousands( $message_post_count + $fake_message_number )
      );
      ?>
    </p>
  </div>
</div>
