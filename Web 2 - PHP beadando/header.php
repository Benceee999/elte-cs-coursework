<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>PartyZone Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .nav-container {
            background: #222; padding: 12px 20px; color: white; margin-bottom: 25px; border-radius: 5px;
            display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .nav-info { font-size: 14px; letter-spacing: 0.5px; }
        .nav-info strong { color: #03dbfc; }
        .nav-links a {
            color: white; text-decoration: none; background: #444; padding: 8px 14px;
            border-radius: 4px; font-size: 13px; font-weight: bold; margin-left: 8px; transition: background 0.2s;
        }
        .nav-links a:hover { background: #555; }
        .nav-links a.logout-btn { background: #d32f2f; }
        .nav-links a.logout-btn:hover { background: #f44336; }

        .card, .box { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); max-width: 600px; margin-bottom: 20px; }
        .celeb-card { background: white; padding: 15px; margin-bottom: 10px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .badge { padding: 4px 10px; color: white; border-radius: 3px; font-size: 12px; margin-left: 5px; font-weight: bold; display: inline-block; }
        .type-badge { background: #777; color: white; padding: 2px 6px; font-size: 11px; border-radius: 3px; margin-left: 5px; }
        .error { color: #d32f2f; font-weight: bold; }
        .success { color: #388e3c; font-weight: bold; }
        
        form input[type="text"], form input[type="password"], form input[type="email"], form select {
            width: 100%; padding: 10px; margin: 8px 0 16px 0; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;
        }
        form button { background: #2e7d32; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        form button:hover { background: #1b5e20; }
    </style>
</head>
<body>

    <div class="nav-container">
        <div class="nav-info">
            PartyZone Admin | 
            Szerepkör: <strong><?= get_role() ?></strong> | 
            Felhasználó: <strong><?= is_logged_in() ? htmlspecialchars($_SESSION['user']['username']) : 'Vendég' ?></strong>
        </div>
        
        <div class="nav-links">
            <a href="index.php">Főoldal</a>
            <?php if (!is_logged_in()): ?>
                <a href="login.php">Bejelentkezés</a>
                <a href="register.php">Regisztráció</a>
            <?php else: ?>
                <a href="details.php?action=add">Új Celeb</a>
                <?php if (get_role() === 'admin'): ?>
                    <a href="admin.php">Rendszerkezelés</a>
                <?php endif; ?>
                <a href="logout.php" class="logout-btn">Kijelentkezés</a>
            <?php endif; ?>
        </div>
    </div>