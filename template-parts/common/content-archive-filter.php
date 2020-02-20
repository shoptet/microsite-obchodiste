<?php
$post_type = get_post_type_in_archive_or_taxonomy();
?>

<div class="filters mb-4 mb-md-0">
  <button
    class="btn btn-primary d-md-none"
    type="button"
    data-toggle="collapse"
    href="#filtersCollapse"
    role="button"
    aria-expanded="false"
    aria-controls="filtersCollapse"
  >
    <?php _e( 'Zobrazit filtry', 'shp-obchodiste' ); ?>
  </button>

  <div class="collapse d-md-block" id="filtersCollapse">
    <div class="mt-3 mt-md-0">

      <?php
      get_template_part( 'src/template-parts/common/content', 'archive-filter-terms' );
      get_template_part( 'src/template-parts/common/content', 'archive-filter-region' );
      ?>
      
      <a
        class="small"
        role="button"
        href="<?php echo get_post_type_archive_link( $post_type ); ?>"
      >
        <i class="fas fa-times text-muted mr-1"></i>
        <?php _e( 'Zrušit filtry', 'shp-obchodiste' ); ?>
      </a>

      <button type="submit" class="btn btn-primary btn-block mt-2" id="filterSubmit">
        <?php _e( 'Filtrovat', 'shp-obchodiste' ); ?>
      </button>

    </div>
  </div>
</div>
