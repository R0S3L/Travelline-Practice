<?php

session_start();

require_once __DIR__ . '/AuthController.php';

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// / → редирект на /login
if ($uri === '/') {
    redirect('/login');
}

// GET /login — показать форму
if ($uri === '/login' && $method === 'GET') {
    $errors = [];
    require __DIR__ . '/login.php';
    exit;
}

// POST /login — обработать вход
if ($uri === '/login' && $method === 'POST') {
    $login    = trim((string) ($_POST['login']    ?? ''));
    $password = trim((string) ($_POST['password'] ?? ''));
    $errors   = [];

    try {
        login($login, $password);
        redirect('/admin');
    } catch (RuntimeException $e) {
        $errors = [$e->getMessage()];
        require __DIR__ . '/login.php';
        exit;
    }
}

// POST /logout — выход
if ($uri === '/logout' && $method === 'POST') {
    logout();
    redirect('/login');
}

// GET /admin — страница после входа
if ($uri === '/admin') {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    $login = htmlspecialchars($_SESSION['user']['login'] ?? '', ENT_QUOTES, 'UTF-8');
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="ru"><head><meta charset="UTF-8"><title>Админка</title></head>
    <body>
        <p>Добро пожаловать, <strong>{$login}</strong>!</p>
        <form action="/logout" method="POST">
            <button type="submit">Выйти</button>
        </form>
    </body></html>
    HTML;
    exit;
}

// 404
http_response_code(404);
echo '404 Not Found';