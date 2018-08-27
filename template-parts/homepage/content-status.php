<div class="row-status">
  <div class="container">
    <p class="h3 mb-0">
      <?php
      printf(
        __( '<strong>%d</strong>&nbsp;velkoobchodů z&nbsp;<strong>%d</strong>&nbsp;kategorií obdrželo poptávky od <strong>%d</strong>&nbsp;e-shopů', '' ),
        wp_count_posts( 'custom' )->publish,
        count( get_terms( 'customtaxonomy' ) ),
        wp_count_posts( 'wholesaler_message' )->publish
      );
      ?>
    </p>
  </div>
</div>
