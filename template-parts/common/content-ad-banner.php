<?php if ( $banner_mobile = get_field( 'banner_mobile' ) ) : ?>
<a
  class="d-block d-md-none"
  href="<?php echo get_field('target_url'); ?>"
  rel="sponsorred"
>
  <img
    class="w-100"
    src="<?php echo $banner_mobile['url']; ?>"
    alt="<?php echo $banner_mobile['alt'] ?>"
    loading="lazy"
  >
</a>
<?php endif; ?>

<?php if ( $banner_desktop = get_field( 'banner_desktop' ) ) : ?>
<a
  class="d-none d-md-block"
  href="<?php echo get_field('target_url'); ?>"
  rel="sponsored"
>
  <img
    class="w-100"
    src="<?php echo $banner_desktop['url']; ?>"
    alt="<?php echo $banner_desktop['alt'] ?>"
    loading="lazy"
  >
</a>
<?php endif; ?>