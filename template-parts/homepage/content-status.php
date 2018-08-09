<div class="row-status">
  <div class="container">
    <p class="h3 mb-0">
      <?php
      printf(
        __( '<strong>%d</strong>&nbsp;velkoobchodů z&nbsp;<strong>%d</strong>&nbsp;kategorií a&nbsp;<strong>%d</strong>&nbsp;krajů', '' ),
        wp_count_posts( 'custom' )->publish,
        count( get_terms( 'customtaxonomy' ) ),
        count( get_used_regions() )
      );
      ?>
    </p>
  </div>
</div>
