<?php get_header(); ?>

<main class="container my-5">
    <?php 
    if (have_posts()) : 
        while (have_posts()) : the_post(); 
    ?>
        <article class="main-news-card">
            <div class="img-wrapper">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('full', array('class' => 'img-fluid', 'style' => 'width:100%')); ?>
                <?php endif; ?>
            </div>

            <div class="card-content p-4 p-md-5">
                <h1 class="display-4 fw-bold mb-3"><?php the_title(); ?></h1>
                
                <div class="d-flex text-muted mb-4">
                    <span class="me-3"><?php echo get_the_date(); ?></span>
                    <span>Рубрика: <?php the_category(', '); ?></span>
                </div>

                <hr>

                <div class="news-content-text">
                    <?php the_content(); ?>
                </div>

                <div class="mt-5">
                    <a href="<?php echo home_url(); ?>" class="btn btn-outline-dark">← Назад к списку</a>
                </div>
            </div>
        </article>
    <?php 
        endwhile; 
    endif; 
    ?>
</main>

<?php get_footer(); ?>