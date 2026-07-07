<?php
get_header();
?>

<main class="container my-5">
    <div class="p-4 p-md-5 contacts-container bg-white shadow-sm" style="border-radius: var(--radius);">

        <h1 class="fw-bold text-uppercase mb-3 contacts-header">Контакты</h1>

        <hr class="contacts-divider mb-5">

        <div class="contacts-info-block">
            <p>Мы всегда рады комментариям, замечаниям, пожеланиям и просто письмам наших читателей.</p>

            <p class="mt-4">Телефон: <strong>+7 (913) 888-88-88</strong></p>

            <p>Часы работы: <strong>Пн–Пт с 9:00 до 18:00</strong></p>

            <p>Email: <strong>example@mail.ru</strong></p>

            <p class="mt-4">Редакторы: <strong>Макарцева Мария, Цыбенова Марина</strong></p>
        </div>
        
        <a href="<?php echo home_url(); ?>" class="btn btn-dark mt-4">На главную</a>
    </div>
</main>

<?php 
get_footer(); 
?>