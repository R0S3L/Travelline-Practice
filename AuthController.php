<?php

// Вспомогательные функции

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function htmlEscape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}


// База данных

function dbConnect(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=127.0.0.1;port=3306;dbname=travelline;charset=utf8mb4',
            'root',  
            '1012',     
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    return $pdo;
}

function findUserByLogin(string $login): ?array
{
    $pdo  = dbConnect();
    $stmt = $pdo->prepare(
        'SELECT idadmin, admin_login, admin_password
           FROM admin
          WHERE admin_login = :login
          LIMIT 1'
    );
    $stmt->execute(['login' => $login]);
    $user = $stmt->fetch();

    return $user ?: null;
}

// Аутентификация

function isLoggedIn(): bool
{
    return !empty($_SESSION['user']);
}

function login(string $login, string $password): void
{
    try {
        $user = findUserByLogin($login);
    } catch (PDOException $e) {
        // Не показываем детали ошибки БД пользователю
        error_log('DB error: ' . $e->getMessage());
        throw new RuntimeException('Ошибка сервера. Попробуйте позже.');
    }
    // Одно сообщение на оба случая — не раскрываем, что именно неверно
    if ($user === null || !password_verify($password, $user['admin_password'])) {
        throw new RuntimeException('Неверный логин или пароль');
    }

    // Защита от session fixation — обновляем ID сессии после входа
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id'    => $user['idadmin'],
        'login' => $user['admin_login'],
    ];
}

function logout(): void
{
    unset($_SESSION['user']);
    session_regenerate_id(true);
}