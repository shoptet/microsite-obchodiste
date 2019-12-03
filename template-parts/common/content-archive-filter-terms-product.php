<?php
$post_type = get_post_type_in_archive_or_taxonomy();

$taxonomy = $post_type . 'taxonomy';
$current_term = ( is_tax() ? $wp_query->get_queried_object() : null );

$render_tree = function ( $parent_term = null ) use ( &$render_tree, &$taxonomy, &$current_term, &$post_type ) {
  $terms = get_terms( [
    'taxonomy' => $taxonomy,
    'parent' => $parent_term ? $parent_term->term_id : 0,
    'hide_empty' => true,
    'hierarchical_force' => true,
  ] );
  if ( empty( $terms ) ) return;
  ?>
  <ul class="<?php echo ( $parent_term ? 'list-tree' : 'list-unstyled' ); ?>">
    <?php foreach ( $terms as $term ): ?>
      <?php
      $is_current = $current_term && $term->term_id == $current_term->term_id;
      $is_parent = $current_term && term_is_ancestor_of( $term, $current_term, $taxonomy );
      $posts_in_term = count_posts_by_term( $post_type, $term, $taxonomy );
      ?>
      <li>
        <a
          href="<?php echo get_term_link( $term, $taxonomy ); ?>"
          data-id="<?php echo $term->term_id; ?>"
          data-slug="<?php echo $term->slug; ?>"
        >
          <span class="<?php echo ( $is_current ? 'font-weight-bold' : '' ); ?>"><?php echo $term->name; ?></span>&nbsp;<span class="text-semilight">(<?php echo $posts_in_term; ?>)</span>
        </a>
        <?php
        if ( $is_current || $is_parent ) {
          $render_tree( $term );
        }
        ?>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php
};
?>

<p class="h5 h-heavy mt-0 mb-2">
  <?php _e( 'Kategorie', 'shp-obchodiste' ); ?>
</p>

<div class="filters-list" id="archiveFormCategoryLinks">
  <input type="hidden" name="category[]" value="<?php echo $current_term ? $current_term->term_id : ''; ?>">
  <?php $render_tree(); ?>
</div>

<div class="filters-divider"></div>
