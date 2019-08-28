<?php
$post_type = get_post_type_in_archive_or_taxonomy();

$taxonomy = $post_type . 'taxonomy';
$terms = get_terms( [ 'taxonomy' => $taxonomy, 'parent' => 0 ] );

if ( is_tax() ) {
  $checked_categories[] = $wp_query->get_queried_object()->term_id;
} else {
  $checked_categories = ( isset( $_GET[ 'category' ]) && is_array($_GET[ 'category' ] ) ) ? $_GET[ 'category' ] : [];
}

?>

<p class="h5 h-heavy mt-0 mb-2">
  <?php _e( 'Kategorie', 'shp-obchodiste' ); ?>
</p>

<?php foreach ( $terms as $term ): ?>
  <div class="custom-control custom-checkbox">
    <input
      class="custom-control-input"
      type="checkbox"
      value="<?php echo $term->term_id; ?>"
      id="filterCategory<?php echo $term->term_id; ?>"
      name="category[]"
      data-slug="<?php echo $term->slug; ?>"
      <?php if ( in_array( $term->term_id, $checked_categories ) ) echo "checked"; ?>
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
