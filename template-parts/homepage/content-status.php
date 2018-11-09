<div class="row-status">
  <div class="container">
    <p class="h3 mb-0">
      <?php
      $options = get_fields( 'options' );
      $fake_message_number = ( isset( $options[ 'fake_message_number' ] ) ) ? (int) $options[ 'fake_message_number' ] : 0;
      printf(
        __( '<strong>%d</strong>&nbsp;velkoobchodů z&nbsp;<strong>%d</strong>&nbsp;kategorií obdrželo poptávky od <strong>%d</strong>&nbsp;e-shopů', 'shp-obchodiste' ),
        wp_count_posts( 'custom' )->publish,
        count( get_terms( 'customtaxonomy' ) ),
       ( wp_count_posts( 'wholesaler_message' )->publish + $fake_message_number )
      );
      ?>
    </p>
  </div>
</div>
