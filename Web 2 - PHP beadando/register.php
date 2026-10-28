<?php
require_once 'init.php';
if (is_logged_in()) {
    redirect("index.php");
    exit;
}
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (strlen($username) < 4) $errors[] = "A felhasználónévnek legalább 4 karakternek kell lennie!";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Érvénytelen e-mail formátum!";
    if (strlen($password) < 4) $errors[] = "A jelszónak legalább 4 karakternek kell lennie!";
    if ($uStorage->findOne(['username' => $username])) $errors[] = "Ez a felhasználónév már foglalt!";

    if (empty($errors)) {
        $userData = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'user'
        ];
        
        $new_id = $uStorage->add($userData); 
        $userData['id'] = $new_id;
        
        $_SESSION['user'] = $userData;
    
        redirect("index.php"); 
        exit;
    }
}

require_once 'header.php';
?>
    <div class="card" style="margin: 40px auto;">
        <h2>Regisztráció</h2>
        <?php if(!empty($errors)) foreach($errors as $e) echo "<p class='error'>$e</p>"; ?>
        <form method="POST">
            <label>Felhasználónév (min. 4 karakter):</label>
            <input type="text" name="username" required>
            <label>E-mail cím:</label>
            <input type="email" name="email" required>
            <label>Jelszó (min. 4 karakter):</label>
            <input type="password" name="password" required>
            <button type="submit">Regisztráció</button>
        </form>
        <p style="font-size:13px; margin-top:15px;">Van már fiókod? <a href="login.php">Jelentkezz be</a></p>
    </div>
</body>
</html>