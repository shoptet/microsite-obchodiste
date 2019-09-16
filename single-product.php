<?php get_header(); ?>

<section class="section section-primary">
    <div class="section-inner container">
    <?php get_template_part( 'template-parts/utils/content', 'breadcrumb' ); ?>
    </div>

    <div class="container" itemscope itemtype="http://schema.org/Product">
        
    <?php
        /* Start the Loop */
        while ( have_posts() ) : the_post();

        get_template_part( 'src/template-parts/product/content', 'content' );

        get_template_part( 'src/template-parts/product/content', 'related' );

        endwhile; wp_reset_query(); // End of the loop.
    ?>

    </div>
</section>

<?php get_footer(); ?>
