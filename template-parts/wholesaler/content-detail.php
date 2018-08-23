<?php if ( get_field( "short_about" ) ): ?>
<div class="wholesaler-block">
  <h2 class="h-heavy mb-1">
    <?php _e( 'Krátce o naší nabídce', '' ); ?>
  </h2>
  <div itemprop="description">
    <?php the_field( "short_about" ); ?>
  </div>
</div>
<?php endif; ?>

<?php if ( ! empty( get_field( "services" ) ) ): ?>
<div class="wholesaler-block">

  <h2 class="h-heavy mb-2">
    <?php _e( 'Co vám nabídneme', '' ); ?>
  </h2>

  <ul class="fa-ul list-horizontal">
    <?php foreach ( get_field( "services" ) as $service ): ?>
    <?php if ( ! empty( $service ) ): ?>
    <li>
      <span class="fa-li"><i class="fas fa-check-circle text-success"></i></span>
      <?php echo $service; ?>
    </li>
    <?php endif; ?>
    <?php endforeach; ?>
  </ul>

</div>
<?php endif; ?>

<?php if ( get_field( "about_company" ) ): ?>
<div class="wholesaler-block">
  <h2 class="h-heavy mb-1">
    <?php _e( 'O naší firmě', '' ); ?>
  </h2>
  <?php the_field( "about_company" ); ?>
</div>
<?php endif; ?>

<?php if ( get_field( "about_products" ) || get_field( "video" ) ): ?>
<div class="wholesaler-block">
  <?php if ( get_field( "about_products" ) ): ?>
  <h2 class="h-heavy mb-1">
    <?php _e( 'O našich produktech', '' ); ?>
  </h2>
  <?php the_field( "about_products" ); ?>
  <?php endif; ?>

  <?php get_template_part( 'src/template-parts/wholesaler/content', 'gallery' ); ?>

  <?php if ( get_field( "video" ) ): ?>
  <div class="embed-responsive embed-responsive-16by9 mt-3">
    <?php the_field( "video" ); ?>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>
