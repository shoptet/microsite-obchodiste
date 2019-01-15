<?php /* Template Name: Homepage template */ ?>
<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
  <?php get_template_part( 'src/template-parts/homepage/content', 'hero' ); ?>
  <?php get_template_part( 'src/template-parts/homepage/content', 'status' ); ?>
  <?php get_template_part( 'src/template-parts/homepage/content', 'special-offers' ); ?>
  <?php get_template_part( 'src/template-parts/homepage/content', 'recent' ); ?>
  <?php get_template_part( 'src/template-parts/homepage/content', 'posts' ); ?>
  <?php get_template_part( 'src/template-parts/homepage/content', 'testimonials' ); ?>
  <?php get_template_part( 'src/template-parts/homepage/content', 'faq' ); ?>
<?php endwhile; ?>

<?php get_footer(); ?>
