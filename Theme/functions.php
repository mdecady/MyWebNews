<?php
add_action('after_setup_theme', function() {
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
});

/**
 * Скрываем верхнюю админ-панель для обычных пользователей.
 * Админу оставляем, чтобы удобно модерировать.
 */
add_filter('show_admin_bar', function($show) {
    return current_user_can('manage_options') ? $show : false;
});

/**
 * Получить ссылку на страницу по ярлыку.
 */
function newspaper_page_url($slug, $fallback) {
    $page = get_page_by_path($slug);

    if ($page) {
        return get_permalink($page);
    }

    return home_url($fallback);
}

/**
 * Ссылка на страницу фотоконкурсов.
 */
function newspaper_contests_url() {
    return newspaper_page_url('fotokonkursy', '/fotokonkursy/');
}

/**
 * Ссылка на внутреннюю страницу входа.
 */
function newspaper_login_url() {
    return newspaper_page_url('login', '/login/');
}

/**
 * Ссылка на внутреннюю страницу регистрации.
 */
function newspaper_register_url() {
    return newspaper_page_url('registration', '/registration/');
}

/**
 * Ссылка на выход.
 */
function newspaper_logout_url() {
    return wp_logout_url(home_url('/'));
}

/**
 * Обработка формы входа.
 */
add_action('init', function() {
    if (empty($_POST['newspaper_login_submit'])) {
        return;
    }

    if (
        empty($_POST['newspaper_login_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['newspaper_login_nonce'])), 'newspaper_login_action')
    ) {
        wp_safe_redirect(add_query_arg('login_error', 'nonce', newspaper_login_url()));
        exit;
    }

    $login = isset($_POST['user_login']) ? sanitize_text_field(wp_unslash($_POST['user_login'])) : '';
    $password = isset($_POST['user_password']) ? (string) wp_unslash($_POST['user_password']) : '';
    $remember = !empty($_POST['rememberme']);

    if ($login === '' || $password === '') {
        wp_safe_redirect(add_query_arg('login_error', 'empty', newspaper_login_url()));
        exit;
    }

    /**
     * Разрешаем вход и по логину, и по email.
     */
    if (is_email($login)) {
        $user_by_email = get_user_by('email', $login);

        if ($user_by_email) {
            $login = $user_by_email->user_login;
        }
    }

    $user = wp_signon([
        'user_login'    => $login,
        'user_password' => $password,
        'remember'      => $remember,
    ], is_ssl());

    if (is_wp_error($user)) {
        wp_safe_redirect(add_query_arg('login_error', 'invalid', newspaper_login_url()));
        exit;
    }

    wp_safe_redirect(newspaper_contests_url());
    exit;
});

/**
 * Обработка формы регистрации.
 */
add_action('init', function() {
    if (empty($_POST['newspaper_register_submit'])) {
        return;
    }

    if (
        empty($_POST['newspaper_register_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['newspaper_register_nonce'])), 'newspaper_register_action')
    ) {
        wp_safe_redirect(add_query_arg('register_error', 'nonce', newspaper_register_url()));
        exit;
    }

    if (!get_option('users_can_register')) {
        wp_safe_redirect(add_query_arg('register_error', 'disabled', newspaper_register_url()));
        exit;
    }

    $username = isset($_POST['user_login']) ? sanitize_user(wp_unslash($_POST['user_login'])) : '';
    $email = isset($_POST['user_email']) ? sanitize_email(wp_unslash($_POST['user_email'])) : '';
    $password = isset($_POST['user_password']) ? (string) wp_unslash($_POST['user_password']) : '';
    $password_repeat = isset($_POST['user_password_repeat']) ? (string) wp_unslash($_POST['user_password_repeat']) : '';

    if ($username === '' || $email === '' || $password === '' || $password_repeat === '') {
        wp_safe_redirect(add_query_arg('register_error', 'empty', newspaper_register_url()));
        exit;
    }

    if (!is_email($email)) {
        wp_safe_redirect(add_query_arg('register_error', 'email', newspaper_register_url()));
        exit;
    }

    if (username_exists($username)) {
        wp_safe_redirect(add_query_arg('register_error', 'username_exists', newspaper_register_url()));
        exit;
    }

    if (email_exists($email)) {
        wp_safe_redirect(add_query_arg('register_error', 'email_exists', newspaper_register_url()));
        exit;
    }

    if ($password !== $password_repeat) {
        wp_safe_redirect(add_query_arg('register_error', 'password_mismatch', newspaper_register_url()));
        exit;
    }

    if (mb_strlen($password) < 6) {
        wp_safe_redirect(add_query_arg('register_error', 'password_short', newspaper_register_url()));
        exit;
    }

    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_safe_redirect(add_query_arg('register_error', 'unknown', newspaper_register_url()));
        exit;
    }

    /**
     * Новый пользователь — обычный участник.
     */
    wp_update_user([
        'ID'   => $user_id,
        'role' => 'subscriber',
    ]);

    /**
     * Автоматически авторизуем после регистрации.
     */
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true);

    wp_safe_redirect(newspaper_contests_url());
    exit;
});

/**
 * Шорткод внутренней формы входа.
 * Использование: [newspaper_login_form]
 */
add_shortcode('newspaper_login_form', function() {
    ob_start();

    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        ?>
        <div class="auth-card">
            <h2>Вы уже вошли</h2>
            <p>Аккаунт: <strong><?php echo esc_html($current_user->user_login); ?></strong></p>
            <div class="auth-actions">
                <a class="auth-button" href="<?php echo esc_url(newspaper_contests_url()); ?>">Перейти к фотоконкурсам</a>
                <a class="auth-button auth-button-secondary" href="<?php echo esc_url(newspaper_logout_url()); ?>">Выйти</a>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    $error = isset($_GET['login_error']) ? sanitize_text_field(wp_unslash($_GET['login_error'])) : '';

    $messages = [
        'nonce'   => 'Ошибка безопасности. Обновите страницу и попробуйте снова.',
        'empty'   => 'Заполните логин/email и пароль.',
        'invalid' => 'Неверный логин, email или пароль.',
    ];
    ?>

    <div class="auth-card">
        <h2>Вход в аккаунт</h2>
        <p class="auth-description">Войдите, чтобы подать заявку на фотоконкурс или проголосовать за участников.</p>

        <?php if ($error && isset($messages[$error])) : ?>
            <div class="auth-message auth-message-error">
                <?php echo esc_html($messages[$error]); ?>
            </div>
        <?php endif; ?>

        <form method="post" class="auth-form">
            <?php wp_nonce_field('newspaper_login_action', 'newspaper_login_nonce'); ?>

            <label>
                <span>Логин или email</span>
                <input type="text" name="user_login" required>
            </label>

            <label>
                <span>Пароль</span>
                <input type="password" name="user_password" required>
            </label>

            <label class="auth-checkbox">
                <input type="checkbox" name="rememberme" value="1">
                <span>Запомнить меня</span>
            </label>

            <button type="submit" name="newspaper_login_submit" value="1" class="auth-button">
                Войти
            </button>
        </form>

        <p class="auth-bottom-text">
            Нет аккаунта?
            <a href="<?php echo esc_url(newspaper_register_url()); ?>">Зарегистрироваться</a>
        </p>
    </div>

    <?php
    return ob_get_clean();
});

/**
 * Шорткод внутренней формы регистрации.
 * Использование: [newspaper_register_form]
 */
add_shortcode('newspaper_register_form', function() {
    ob_start();

    if (is_user_logged_in()) {
        ?>
        <div class="auth-card">
            <h2>Вы уже зарегистрированы</h2>
            <p>Можно перейти к фотоконкурсам и подать заявку.</p>
            <a class="auth-button" href="<?php echo esc_url(newspaper_contests_url()); ?>">Перейти к фотоконкурсам</a>
        </div>
        <?php
        return ob_get_clean();
    }

    $error = isset($_GET['register_error']) ? sanitize_text_field(wp_unslash($_GET['register_error'])) : '';

    $messages = [
        'nonce'             => 'Ошибка безопасности. Обновите страницу и попробуйте снова.',
        'disabled'          => 'Регистрация пользователей отключена в настройках сайта.',
        'empty'             => 'Заполните все поля.',
        'email'             => 'Введите корректный email.',
        'username_exists'   => 'Пользователь с таким логином уже существует.',
        'email_exists'      => 'Пользователь с таким email уже существует.',
        'password_mismatch' => 'Пароли не совпадают.',
        'password_short'    => 'Пароль должен быть не короче 6 символов.',
        'unknown'           => 'Не удалось создать пользователя. Попробуйте еще раз.',
    ];
    ?>

    <div class="auth-card">
        <h2>Регистрация</h2>
        <p class="auth-description">Создайте аккаунт участника, чтобы отправлять фотографии на конкурс.</p>

        <?php if ($error && isset($messages[$error])) : ?>
            <div class="auth-message auth-message-error">
                <?php echo esc_html($messages[$error]); ?>
            </div>
        <?php endif; ?>

        <form method="post" class="auth-form">
            <?php wp_nonce_field('newspaper_register_action', 'newspaper_register_nonce'); ?>

            <label>
                <span>Логин</span>
                <input type="text" name="user_login" required>
            </label>

            <label>
                <span>Email</span>
                <input type="email" name="user_email" required>
            </label>

            <label>
                <span>Пароль</span>
                <input type="password" name="user_password" required>
            </label>

            <label>
                <span>Повторите пароль</span>
                <input type="password" name="user_password_repeat" required>
            </label>

            <button type="submit" name="newspaper_register_submit" value="1" class="auth-button">
                Зарегистрироваться
            </button>
        </form>

        <p class="auth-bottom-text">
            Уже есть аккаунт?
            <a href="<?php echo esc_url(newspaper_login_url()); ?>">Войти</a>
        </p>
    </div>

    <?php
    return ob_get_clean();
});