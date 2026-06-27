<?php
session_start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function htmlEscape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function old(string $name): string
{
    return isset($_POST[$name]) ? htmlEscape((string) $_POST[$name]) : '';
}

function renderLoginPage(array $errors = []): void
{
    $errorHtml = '';
    if (!empty($errors)) {
        $errorHtml = '<div class="error-banner">' . htmlEscape($errors[0]) . '</div>';
    }

    $emailValue = old('email');
    echo <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — TravelLine Tech</title>
    <link rel="stylesheet" href="assets/css/simple-copy.css">
</head>
<body>
    <main class="container registration-page">
        <section class="card registration-card">
            <h1 class="hero_h1">Вход</h1>
            {$errorHtml}

            <form class="registration-form" action="/login" method="POST">
                <label>
                    Login
                    <input type="text" name="email" placeholder="login"
                           value="{$emailValue}" required>
                </label>
                <label>
                    Пароль
                    <input type="password" name="password" placeholder="password" required>
                </label>
                <button type="submit" class="button-primary">Войти</button>
            </form>
        </section>
    </main>
</body>
</html>
HTML;
    exit;
}

function renderAdminPage(array $user): void
{
    $login = htmlEscape($user['login'] ?? '');
    echo "<!DOCTYPE html>\n<html lang=\"ru\">\n<head>\n<meta charset=\"UTF-8\">\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n<title>Админка</title>\n</head>\n<body>\n";
    echo "<p>Добро пожаловать, {$login}!</p>\n";
    echo "<form action=\"/logout\" method=\"POST\">\n";
    echo "<button type=\"submit\">Выйти</button>\n";
    echo "</form>\n</body>\n</html>\n";
    exit;
}

if ($uri === '/') {
    redirect('/login');
}

if ($uri === '/login') {
    if ($method === 'GET') {
        renderLoginPage();
    }

    if ($method === 'POST') {
        $login = trim((string) ($_POST['email'] ?? ''));
        $password = trim((string) ($_POST['password'] ?? ''));

        $validUsers = [
            'admin' => password_hash('admin', PASSWORD_DEFAULT),
        ];

        if (isset($validUsers[$login]) && password_verify($password, $validUsers[$login])) {
            $_SESSION['user'] = ['login' => $login];
            redirect('/admin');
        }

        renderLoginPage(['Неверный логин или пароль']);
    }
}

if ($uri === '/logout' && $method === 'POST') {
    unset($_SESSION['user']);
    redirect('/login');
}

if ($uri === '/admin') {
    if (empty($_SESSION['user'])) {
        redirect('/login');
    }

    renderAdminPage($_SESSION['user']);
}

http_response_code(404);
echo '404 Not Found';

