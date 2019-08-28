<?php
$post_type = get_post_type_in_archive_or_taxonomy();

$taxonomy = $post_type . 'taxonomy';
$current_term = ( is_tax() ? $wp_query->get_queried_object() : null );

$render_branch = function ( $parent_term = null ) use ( &$render_branch, &$taxonomy, &$current_term, &$post_type ) {
  $terms = get_terms( [
    'taxonomy' => $taxonomy,
    'parent' => $parent_term ? $parent_term->term_id : 0,
  ] );
  ?>
  <ul>
    <?php foreach ( $terms as $term ): ?>
      <?php
      $is_parent = $current_term && term_is_ancestor_of( $term, $current_term, $taxonomy );
      $is_current = $current_term && $term->term_id == $current_term->term_id;
      $posts_in_term = count_posts_by_term( $post_type, $term, $taxonomy );
      ?>
      <li>
        <a href="<?php echo get_term_link( $term, $taxonomy ); ?>">
          <span
            class="<?php echo ( $is_current ? 'font-weight-bold' : '' ); ?>"
          >
            <?php echo $term->name; ?>
          </span>
          <span class="text-semilight">(<?php echo $posts_in_term; ?>)</span>
        </a>
        <?php
        if ( $is_current || $is_parent ) {
          $render_branch( $term );
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

<?php $render_branch(); ?>

<div class="filters-divider"></div>
