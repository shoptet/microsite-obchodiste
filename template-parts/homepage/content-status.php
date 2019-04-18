<div class="row-status">
  <div class="container">
    <p class="h3 mb-0">
      <?php
      $options = get_fields( 'options' );
      $fake_message_number = ( isset( $options[ 'fake_message_number' ] ) ) ? (int) $options[ 'fake_message_number' ] : 0;
      printf(
        __( '<strong>%d</strong>&nbsp;velkoobchodů s&nbsp;nabídkou <strong>%d</strong>&nbsp;produktů obdrželo <strong>%d</strong>&nbsp;poptávek od e-shopů', 'shp-obchodiste' ),
        wp_count_posts( 'custom' )->publish,
        wp_count_posts( 'product' )->publish,
       ( wp_count_posts( 'wholesaler_message' )->publish + $fake_message_number )
      );
      ?>
    </p>
  </div>
</div>
