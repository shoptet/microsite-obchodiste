<h2 class="h-heavy">
  <?php _e( 'Kontaktní údaje', '' ); ?>
</h2>

<div class="mb-2 clearfix">

  <?php if ( get_field( "logo" ) ): ?>
  <div class="wholesaler-logo">
    <img
      src="<?php echo get_field( "logo" )[ "sizes" ][ "medium" ]; ?>"
      alt="<?php echo the_title(); ?>"
      itemprop="logo"
    >
  </div>
  <?php endif; ?>

  <address itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
    <a href="#">
      <?php echo the_title(); ?>
    </a>
    <br>
    <?php if ( get_field( "street" ) ): ?>
    <span itemprop="streetAddress"><?php the_field( "street" ); ?></span>
    <br>
    <?php endif; ?>
    <span itemprop="streetAddress"><?php the_field( "postalCode" ); ?></span>
    &nbsp;<span itemprop="addressLocality"><?php the_field( "city" ); ?></span>
  </address>

  <dl class="dl-pair-inline">
    <?php if ( get_field( "in" ) ): ?>
    <dt><?php _e( 'IČO', '' ); ?></dt>
    <dd>
      <a href="http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_res.cgi?odp=html&ICO=<?php the_field( "in" ); ?>" target="_blank" itemprop="identifier">
        <?php the_field( "in" ); ?>
      </a>
    </dd>
    <?php endif; ?>
    <?php if ( get_field( "tin" ) ): ?>
    <dt><?php _e( 'DIČ', '' ); ?></dt>
    <dd  itemprop="taxID"><?php the_field( "tin" ); ?></dd>
    <?php endif; ?>
  </dl>

  <?php if ( get_field( "website" ) ): ?>
  <p>
    <a href="<?php the_field( "website" ); ?>" target="_blank" itemprop="url">
      <?php echo display_url( get_field( "website" ) ); ?>
    </a>
  </p>
  <?php endif; ?>

  <?php if ( get_field( "facebook" ) || get_field( "twitter" ) ): ?>
  <ul class="list-inline mb-0">
    <?php if ( get_field( "facebook" ) ): ?>
    <li class="list-inline-item">
      <a class="link-facebook" href="<?php the_field( "facebook" ); ?>" target="_blank" itemprop="sameAs">
        <i class="fab fa-2x fa-facebook-square"></i>
      </a>
    </li>
    <?php endif; ?>
    <?php if ( get_field( "twitter" ) ): ?>
    <li class="list-inline-item">
      <a class="link-twitter" href="<?php the_field( "twitter" ); ?>" target="_blank" itemprop="sameAs">
        <i class="fab fa-2x fa-twitter-square"></i>
      </a>
    </li>
    <?php endif; ?>
  </ul>
  <?php endif; ?>

</div>
