<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        form { max-width: 320px; display: grid; gap: 12px; }
        input, button { padding: 8px; }
    </style>
</head>
<body>
    <h1>Employee Management System</h1>
    <form method="post" action="index.php?action=login">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color:red;"><?= e($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</body>
</html>
