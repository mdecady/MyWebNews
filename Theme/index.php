<?php get_header(); ?>

<main class="container my-5"> 
    <h2 class="mb-4 text-uppercase">Последние новости</h2>
    <div class="slider-wrapper mb-5">
        <div id="custom-slider" class="slider-container">
            <?php
            $slider_query = new WP_Query(array('posts_per_page' => 3));
            $s_idx = 0;
            if ($slider_query->have_posts()) : while ($slider_query->have_posts()) : $slider_query->the_post();
            ?>
                <div class="my-slide <?= ($s_idx == 0) ? 'active' : '' ?>">
                    <img src="<?php the_post_thumbnail_url(); ?>" alt="slide">
                    <div class="slide-info">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    </div>
                </div>
            <?php $s_idx++; endwhile; wp_reset_postdata(); endif; ?>
            <button id="prevBtn" class="slider-control prev">&lt;</button>
            <button id="nextBtn" class="slider-control next">&gt;</button>
        </div>
    </div>

    <h2 class="mb-4 text-uppercase">Новости</h2>
    <div class="col-lg-12">
        <?php
        $n = 0;
        $row_open = false;
        
        if (have_posts()) : 
            while (have_posts()) : the_post();
                $mode = $n % 3;

                if ($mode == 0) {
                    if ($row_open) { echo '</div>'; $row_open = false; }
                    ?>
                    <article class="main-news-card mb-5">
                        <div class="img-wrapper">
                            <img src="<?php the_post_thumbnail_url(); ?>" alt="фото">
                        </div>
                        <div class="card-content text-center p-4">
                            <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                            <p class="lead text-muted"><?php echo get_the_excerpt(); ?></p>
                        </div>
                    </article>
                    <?php
                } else {
                    if (!$row_open) { echo '<div class="row g-4 mb-5">'; $row_open = true; }
                    ?>
                    <div class="col-md-6">
                        <div class="small-news-item p-4">
                            <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                            <p class="text-muted"><?php echo get_the_excerpt(); ?></p>
                        </div>
                    </div>
                    <?php
                    if ($mode == 2) { echo '</div>'; $row_open = false; }
                }
                $n++;
            endwhile;

            if ($row_open) echo '</div>';

            echo '<div class="pagination-wrapper my-5 text-center">';
            the_posts_pagination(array(
                'mid_size'  => 2,
                'prev_text' => '« Назад',
                'next_text' => 'Вперед »',
            ));
            echo '</div>';

        else :
            echo '<p>Новостей пока нет.</p>';
        endif; 
        ?>
    </div>
</main>

<script src="<?php echo get_template_directory_uri(); ?>/slider-logic.js"></script>
<?php get_footer(); ?>