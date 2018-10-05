<?php if ( get_field( "contact_full_name" ) || get_field( "contact_email" )  || get_field( "contact_tel" ) ): ?>

<h2 class="h-heavy">
  <?php _e( 'Kontaktní osoba', 'shp-obchodiste' ); ?>
</h2>

<div class="card mb-4">
  <div class="d-flex align-items-center">

    <?php if ( get_field( "contact_photo" ) ): ?>
    <img
      class="align-self-start rounded-top-left rounded-bottom-left"
      src="<?php echo get_field( "contact_photo" )[ "sizes" ][ "thumbnail" ]; ?>"
      <?php if ( get_field( "contact_full_name" ) ): ?>
        alt="<?php the_field( "contact_full_name" ); ?>"
      <?php endif; ?>
      height="104"
      width="104"
    >
    <?php endif; ?>

    <div class="p-3 text-truncate">

      <?php if ( get_field( "contact_full_name" ) ): ?>
      <p class="font-weight-bold mb-0">
        <?php the_field( "contact_full_name" ); ?>
      </p>
      <?php endif; ?>

      <ul class="list-unstyled mb-0">
        <?php if ( get_field( "contact_email" ) ): ?>
        <li>
          <a href="mailto:<?php the_field( "contact_email" ); ?>">
            <?php the_field( "contact_email" ); ?>
          </a>
          <meta itemprop="email" content="<?php the_field( "contact_email" ); ?>">
        </li>
        <?php endif; ?>
        <?php if ( get_field( "contact_tel" ) ): ?>
        <li>
          <a href="tel:<?php the_field( "contact_tel" ); ?>" itemprop="telephone">
            <?php the_field( "contact_tel" ); ?>
          </a>
          <meta itemprop="telephone" content="<?php the_field( "contact_tel" ); ?>">
        </li>
        <?php endif; ?>
      </ul>

    </div>

  </div>
</div>

<?php endif; ?>
