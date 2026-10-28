<?php
require_once 'init.php';

if (is_logged_in()) {
    redirect("index.php");
    exit;
}
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = $uStorage->findOne(['username' => $username]);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        redirect("index.php"); exit;
    } else {
        $error = "Hibás felhasználónév vagy jelszó!";
    }
}

require_once 'header.php';
?>
    <div class="card" style="margin: 40px auto;">
        <h2>Bejelentkezés</h2>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <label>Felhasználónév:</label>
            <input type="text" name="username" required>
            <label>Jelszó:</label>
            <input type="password" name="password" required>
            <button type="submit">Belépés</button>
        </form>
        <p style="font-size:13px; margin-top:15px;">Nincs még fiókod? <a href="register.php">Regisztrálj itt</a></p>
    </div>
</body>
</html>