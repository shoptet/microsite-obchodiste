<?php get_header(); ?>

<section class="section section-primary">
    <div class="section-inner container">
    <?php get_template_part( 'template-parts/utils/content', 'breadcrumb' ); ?>
    </div>

    <div class="container">
        
    <?php
        /* Start the Loop */
        while ( have_posts() ) : the_post();

        get_template_part( 'src/template-parts/wholesaler/content', 'content' );

        get_template_part( 'src/template-parts/wholesaler/content', 'related' );

        endwhile; // End of the loop.
    ?>

    </div>
</section>

<?php get_footer(); ?>
