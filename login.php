<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — TravelLine Tech</title>
    <link rel="stylesheet" href="css/simple-copy.css">
</head>
<body>
    <main class="container registration-page">
        <section class="card registration-card">
            <h1 class="hero_h1">Вход</h1>
            <?php if (!empty($errors)): ?>
                <div class="error-banner">
                    <?php echo htmlspecialchars($errors[0], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            <form class="registration-form" action="/login" method="POST">
                <label>
                    Login
                    <input type="text" name="login" placeholder="Введите логин"
                           value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
                </label>
                <label>
                    Пароль
                    <input type="password" name="password" placeholder="Введите пароль" required>
                </label>
                <button type="submit" class="button-primary">Войти</button>
            </form>
        </section>
    </main>
</body>
</html>
