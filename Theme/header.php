<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php wp_head(); ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo esc_url(get_stylesheet_uri()); ?>">
</head>

<body <?php body_class(); ?>>

<header class="main-header sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
            <?php bloginfo('name'); ?>
        </a>

        <div class="header-actions">
            <a href="<?php echo esc_url(newspaper_contests_url()); ?>" class="header-btn">
                Фотоконкурсы
            </a>

            <?php if (is_user_logged_in()) : ?>
                <a href="<?php echo esc_url(newspaper_logout_url()); ?>" class="header-btn">
                    Выйти
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url(newspaper_login_url()); ?>" class="header-btn">
                    Войти
                </a>

                <a href="<?php echo esc_url(newspaper_register_url()); ?>" class="header-btn">
                    Регистрация
                </a>
            <?php endif; ?>

            <button 
                class="menu-button" 
                type="button" 
                data-bs-toggle="offcanvas" 
                data-bs-target="#offcanvasRight" 
                aria-controls="offcanvasRight"
            >
                ☰ Разделы
            </button>
        </div>
    </div>
</header>

<div class="offcanvas offcanvas-end sidebar" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasRightLabel">РАЗДЕЛЫ</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Закрыть"></button>
    </div>

    <div class="offcanvas-body">
        <nav class="sidebar-menu">
            <?php 
            $my_slugs = array('фильмы', 'музыка', 'мода', 'книги', 'интересное', 'игры', 'животные');

            foreach ($my_slugs as $slug) {
                $cat_obj = get_category_by_slug($slug);
                
                if ($cat_obj) {
                    $name = $cat_obj->name;
                    $link = get_category_link($cat_obj->term_id);
                    echo '<a href="' . esc_url($link) . '">' . esc_html($name) . '</a>';
                }
            }
            ?>

            <hr style="border-color: rgba(255,255,255,0.2)">

            <a href="<?php echo esc_url(newspaper_contests_url()); ?>" class="special-link">
                ФОТОКОНКУРСЫ
            </a>

            <a href="<?php echo esc_url(home_url('/contacts')); ?>" class="special-link">
                КОНТАКТЫ
            </a>

            <hr style="border-color: rgba(255,255,255,0.2)">

            <?php if (is_user_logged_in()) : ?>
                <a href="<?php echo esc_url(newspaper_logout_url()); ?>">
                    ВЫЙТИ
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url(newspaper_login_url()); ?>">
                    ВОЙТИ
                </a>

                <a href="<?php echo esc_url(newspaper_register_url()); ?>">
                    ЗАРЕГИСТРИРОВАТЬСЯ
                </a>
            <?php endif; ?>

            <?php if (current_user_can('manage_options')) : ?>
                <hr style="border-color: rgba(255,255,255,0.2)">

                <a href="<?php echo esc_url(admin_url('edit.php?post_type=pc_contest')); ?>">
                    УПРАВЛЕНИЕ КОНКУРСАМИ
                </a>

                <a href="<?php echo esc_url(admin_url('edit.php?post_type=pc_contest&page=pc-submissions')); ?>">
                    МОДЕРАЦИЯ ЗАЯВОК
                </a>
            <?php endif; ?>
        </nav>
    </div>
</div>