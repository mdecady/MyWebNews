<?php get_header(); ?>

<main class="container my-5">
    <?php 
    if (have_posts()) : 
        while (have_posts()) : the_post(); 
    ?>
        <article class="page-content-card p-4 p-md-5">
            <h1 class="fw-bold text-uppercase mb-4"><?php the_title(); ?></h1>

            <div class="page-content-text">
                <?php the_content(); ?>
            </div>
        </article>
    <?php 
        endwhile; 
    endif; 
    ?>
</main>

<?php get_footer(); ?>