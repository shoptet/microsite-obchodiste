<?php if ( get_field( "gallery" ) ): ?>
<ul class="gallery mt-3" itemscope itemtype="http://schema.org/ImageGallery">
  <?php foreach ( get_field( "gallery" ) as $image ): ?>
  <li itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
    <a class="colorbox" href="<?php echo $image[ "sizes" ][ "large" ]; ?>" itemprop="contentUrl">
      <img
        src="<?php echo $image[ "sizes" ][ "medium" ]; ?>"
        alt="<?php echo $image[ "alt" ]; ?>"
        itemprop="thumbnail"
      >
    </a>
  </li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>
