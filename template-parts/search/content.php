<?php
$isWholesalerDefault = is_singular( 'custom' ) || is_post_type_archive( 'custom' ) || is_tax( 'customtaxonomy' );
?>

<?php if ( ! is_front_page() ): ?>
  <form class="mr-lg-2" action="<?php echo get_post_type_archive_link( $isWholesalerDefault ? 'custom' : 'product' ); ?>" id="searchFormHeader" role="search">

    <div class="input-group">
      <div class="input-group-prepend">
        <select class="custom-select pl-2" name="searchFormHeaderPostTypeSelect" id="searchFormHeaderPostTypeSelect" style="width:<?php echo ( $isWholesalerDefault ? 'auto' : '6.5rem' ); ?>">
          <option id="searchFormHeaderPostTypeSelectProduct" value="product" data-width="6.5rem" <?php echo ( $isWholesalerDefault ? '' : 'selected' ); ?>>
            <?php _e( 'Produkty', 'shp-obchodiste' ); ?>
          </option>
          <option id="searchFormHeaderPostTypeSelectCustom" value="custom" data-width="auto" <?php echo ( $isWholesalerDefault ? 'selected' : '' ); ?>>
            <?php _e( 'Velkoobchody', 'shp-obchodiste' ); ?>
          </option>
        </select>
      </div>
      <input type="text" class="form-control px-2" name="s" value="<?php the_search_query(); ?>" placeholder="<?php echo ( $isWholesalerDefault ? 'Jakého velkoobchodního prodejce hledáte?' : 'Jaký produkt byste chtěli prodávat?' ); ?>">
      <div class="input-group-append">
        <button type="submit" class="btn btn-input px-2 border-left-0" aria-label="<?php _e( 'Vyhledat', 'shp-obchodiste' ); ?>">
          <i class="fas fa-search fs-90"></i>
        </button>
      </div>
    </div>

  </form>
<?php endif; ?>