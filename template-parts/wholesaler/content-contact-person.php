<?php if ( get_field( "contact_full_name" ) || get_field( "contact_email" )  || get_field( "contact_tel" ) ): ?>

<h2 class="h-heavy">
  <?php _e( 'Kontaktní osoba', 'shp-obchodiste' ); ?>
</h2>

<div class="contact-person mb-4">

  <?php if ( get_field( "contact_photo" ) ): ?>
  <div
    class="contact-person-image"
    style="background-image: url(<?php echo get_field( "contact_photo" )[ "sizes" ][ "thumbnail" ]; ?>)"
  >
    <img
      src="<?php echo get_field( "contact_photo" )[ "sizes" ][ "thumbnail" ]; ?>"
      <?php if ( get_field( "contact_full_name" ) ): ?>
        alt="<?php the_field( "contact_full_name" ); ?>"
      <?php endif; ?>
    >
  </div>
  <?php endif; ?>

  <div class="contact-person-body">

    <?php if ( get_field( "contact_full_name" ) ): ?>
    <p class="font-weight-bold text-truncate mb-0">
      <?php the_field( "contact_full_name" ); ?> 
    </p>
    <?php endif; ?>

    <ul class="list-unstyled mb-0">
      <?php if ( get_field( "contact_email" ) ): ?>
      <li class="text-truncate">
        <a href="mailto:<?php the_field( "contact_email" ); ?>">
          <?php the_field( "contact_email" ); ?> 
        </a>
        <meta itemprop="email" content="<?php the_field( "contact_email" ); ?>">
      </li>
      <?php endif; ?>
      <?php if ( get_field( "contact_tel" ) ): ?>
      <li class="text-truncate">
        <a href="tel:<?php the_field( "contact_tel" ); ?>" itemprop="telephone">
          <?php the_field( "contact_tel" ); ?>
        </a>
        <meta itemprop="telephone" content="<?php the_field( "contact_tel" ); ?>">
      </li>
      <?php endif; ?>
    </ul>

  </div>
</div>

<?php endif; ?>
