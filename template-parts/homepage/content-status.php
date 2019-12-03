<div class="row-status">
  <div class="container">
    <p class="h3 mb-0">
      <?php
      $options = get_fields( 'options' );
      $fake_message_number = ( isset( $options[ 'fake_message_number' ] ) ) ? (int) $options[ 'fake_message_number' ] : 0;
      printf(
        __( '<strong>%s</strong>&nbsp;velkoobchodů s&nbsp;nabídkou <strong>%s</strong>&nbsp;produktů obdrželo <strong>%s</strong>&nbsp;poptávek od e-shopů', 'shp-obchodiste' ),
        separate_thousands( wp_count_posts( 'custom' )->publish ),
        separate_thousands( wp_count_posts( 'product' )->publish ),
        separate_thousands( wp_count_posts( 'wholesaler_message' )->publish + $fake_message_number )
      );
      ?>
    </p>
  </div>
</div>
