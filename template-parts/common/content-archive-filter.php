<?php
$post_type = get_post_type_in_archive_or_taxonomy();

switch ( $post_type ) {
  case 'custom':
  $categories = get_terms( 'customtaxonomy' );
  break;
  case 'special_offer':
  $categories = get_wholesaler_terms_related_to_post_type( $post_type );
  break;
  case 'product':
  $categories = get_terms( 'producttaxonomy' );
  break;
}

$term_count = function ( $term ) use ( &$post_type ) {
  if ( 'special_offer' === $post_type )
    return count( get_posts_by_related_wholesaler_term( $post_type, $term->term_id ) );
  return $term->count;
};

$region_count = function ( $region ) use ( &$post_type ) {
  if ( in_array( $post_type, [ 'special_offer', 'product' ] ) )
    return count( get_posts_by_region( $post_type, $region[ 'id' ] ) );
  return get_post_count_by_meta( 'region', $region[ 'id' ], $post_type );
};

$regions = get_used_regions_by_country( $post_type );

if ( is_tax() ) {
  $checked_categories[] = $wp_query->get_queried_object()->term_id;
} else {
  $checked_categories = ( isset( $_GET[ 'category' ]) && is_array($_GET[ 'category' ] ) ) ? $_GET[ 'category' ] : [];
}

$checked_regions = ( isset($_GET[ 'region' ]) && is_array($_GET[ 'region' ]) ) ? $_GET[ 'region' ] : [];

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

      <?php if ( ! empty( $categories ) ): ?>

        <p class="h5 h-heavy mt-0 mb-2">
          <?php _e( 'Kategorie', 'shp-obchodiste' ); ?>
        </p>

        <?php foreach ( $categories as $term ): ?>
          <div class="custom-control custom-checkbox">
            <input
              class="custom-control-input"
              type="checkbox"
              value="<?php echo $term->term_id; ?>"
              id="filterCategory<?php echo $term->term_id; ?>"
              name="category[]"
              data-slug="<?php echo $term->slug; ?>"
              <?php if ( in_array ( $term->term_id, $checked_categories ) ) echo "checked"; ?>
            >
            <label
              class="custom-control-label"
              for="filterCategory<?php echo $term->term_id; ?>"
            >
              <?php echo $term->name; ?>
              <span class="text-semilight">
                (<?php echo $term_count( $term ); ?>)
              </span>
            </label>
          </div>
        <?php endforeach; ?>

        <div class="filters-divider"></div>

      <?php endif; ?>
      <?php if ( ! empty( $regions ) ): ?>

        <p class="h5 h-heavy mt-0 mb-2">
          <?php _e( 'Lokalita', 'shp-obchodiste' ); ?>
        </p>

        <?php foreach ( $regions as $country_code => $country ): ?>
          <p class="font-weight-bold my-2"><?php echo $country[ 'name' ]; ?></p>
          <?php foreach ( $country[ 'used_regions' ] as $region ): ?>
            <div class="custom-control custom-checkbox">
              <input
                class="custom-control-input"
                type="checkbox"
                value="<?php echo $region[ 'id' ]; ?>"
                id="filterRegion<?php echo $region[ 'id' ]; ?>"
                name="region[]"
                <?php if ( in_array ( $region[ 'id' ], $checked_regions ) ) echo "checked"; ?>
              >
              <label
                class="custom-control-label"
                for="filterRegion<?php echo $region[ 'id' ]; ?>"
              >
                <?php echo $region[ 'name' ]; ?>
                <!-- <span class="text-semilight">
                  (<?php
                  // TODO: Optimize region_count function
                  //echo $region_count( $region );
                  ?>)
                </span> -->
              </label>
            </div>
          <?php endforeach; ?>
        <?php endforeach; ?>

        <div class="filters-divider"></div>

      <?php endif; ?>

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
