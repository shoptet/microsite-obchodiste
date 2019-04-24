<?php if ( get_field( "gallery" ) ): ?>
<ul class="gallery mt-3">
  <?php foreach ( get_field( "gallery" ) as $image ): ?>
  <li>
    <a class="colorbox" href="<?php echo $image[ "sizes" ][ "large" ]; ?>">
      <img
        src="<?php echo $image[ "sizes" ][ "medium" ]; ?>"
        alt="<?php echo $image[ "alt" ]; ?>"
      >
    </a>
  </li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>
