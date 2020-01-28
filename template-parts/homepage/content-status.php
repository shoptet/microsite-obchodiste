<?php
$options = get_fields( 'options' );
$fake_message_number = ( isset( $options[ 'fake_message_number' ] ) ) ? (int) $options[ 'fake_message_number' ] : 0;
$custom_post_count = CounterCache::getPostTypeCount( 'custom' );
$message_post_count = CounterCache::getPostTypeCount( 'wholesaler_message' );
?>
<div class="row-status">
  <div class="container">
    <p class="h3 mb-0">
      <?php
      printf(
        __( '<strong>%d</strong>&nbsp;velkoobchodů obdrželo <strong>%d</strong>&nbsp;poptávek od e-shopů', 'shp-obchodiste' ),
        $custom_post_count,
        ( $message_post_count + $fake_message_number )
      );
      ?>
    </p>
  </div>
</div>
