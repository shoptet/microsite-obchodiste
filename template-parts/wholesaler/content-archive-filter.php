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
    <?php _e( 'Zobrazit filtry', '' ); ?>
  </button>

  <div class="collapse d-md-block" id="filtersCollapse">
    <div class="mt-3 mt-md-0">

      <p class="h5 h-heavy">
        <?php _e( 'Kategorie', '' ); ?>
      </p>

      <?php
      if ( is_tax() ) {
        $checked_categories[] = $wp_query->get_queried_object()->term_id;
      } else {
        $checked_categories = ( isset($_GET[ 'category' ]) && is_array($_GET[ 'category' ]) ) ? $_GET[ 'category' ] : [];
      }
      ?>

      <?php foreach ( get_terms( 'customtaxonomy' ) as $term ): ?>
        <div class="custom-control custom-checkbox">
          <input
            class="custom-control-input"
            type="checkbox"
            value="<?php echo $term->term_id; ?>"
            id="filterCategory<?php echo $term->term_id; ?>"
            name="category[]"
            data-slug="<?php echo $term->slug; ?>"
            <?php if ( in_array ( $term->term_id, $checked_categories ) ) { echo "checked"; } ?>
          >
          <label
            class="custom-control-label"
            for="filterCategory<?php echo $term->term_id; ?>"
          >
            <?php echo $term->name; ?>
            <span class="text-semilight">
              (<?php echo $term->count; ?>)
            </span>
          </label>
        </div>
      <?php endforeach; ?>

      <div class="filters-divider"></div>

      <p class="h5 h-heavy mt-0">
        <?php _e( 'Lokalita', '' ); ?>
      </p>

      <?php
      $checked_regions = ( isset($_GET[ 'region' ]) && is_array($_GET[ 'region' ]) ) ? $_GET[ 'region' ] : [];
      ?>

      <?php // TODO: rewrite get_field_object( 'region' )[ 'choices' ] to field name function ?>
      <?php foreach ( get_field_object( 'region' )[ 'choices' ] as $region_id => $region_name ): ?>
        <?php $region_post_count = get_post_count_by_meta( 'region', $region_id, 'custom' ); ?>
        <?php if ( ! $region_post_count ) continue; // Skip empty region ?>
        <div class="custom-control custom-checkbox">
          <input
            class="custom-control-input"
            type="checkbox"
            value="<?php echo $region_id; ?>"
            id="filterCategory<?php echo $region_id; ?>"
            name="region[]"
            <?php if ( in_array ( $region_id, $checked_regions ) ) { echo "checked"; } ?>
          >
          <label
            class="custom-control-label"
            for="filterCategory<?php echo $region_id; ?>"
          >
            <?php echo $region_name; ?>
            <span class="text-semilight">
              (<?php echo $region_post_count; ?>)
            </span>
          </label>
        </div>
      <?php endforeach; ?>

      <div class="filters-divider"></div>

      <a
        class="small"
        role="button"
        href="<?php echo get_post_type_archive_link( 'custom' ); ?>"
      >
        <i class="fas fa-times text-muted mr-1"></i>
        <?php _e( 'Zrušit filtry', '' ); ?>
      </a>

      <button type="submit" class="btn btn-primary btn-block mt-2" id="filterSubmit">
        <?php _e( 'Filtrovat', '' ); ?>
      </button>

    </div>
  </div>
</div>
