<?php
$region_count = function ( $region ) use ( &$post_type ) {
  if ( in_array( $post_type, [ 'product' ] ) )
    return count( get_posts_by_region( $post_type, $region[ 'id' ] ) );
  return get_post_count_by_meta( 'region', $region[ 'id' ], $post_type );
};
$post_type = get_post_type_in_archive_or_taxonomy();
$regions = get_used_regions_by_country( $post_type );
$checked_regions = ( isset($_GET[ 'region' ]) && is_array($_GET[ 'region' ]) ) ? $_GET[ 'region' ] : [];

if ( ! empty( $regions ) ): ?>

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