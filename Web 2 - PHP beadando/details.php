<?php
require_once 'init.php';

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? 'view';

$celeb = $id !== null ? $cStorage->findById($id) : null;
$platforms = $pStorage->findAll();
$celeb_types = $ctStorage->findAll();

// BÓNUSZ: CELEB TÖRLÉSI JOGOSULTSÁG ÉS MŰVELET
if ($action === 'delete' && $id !== null && $celeb) {
    check_auth('user');
    $current_user_id = $_SESSION['user']['id'] ?? '';
    
    // Admin bármit törölhet, normál user csak a sajátját
    if (get_role() === 'admin' || (isset($celeb['created_by']) && $celeb['created_by'] === $current_user_id)) {
        $cStorage->delete($id);
        redirect("index.php");
    } else {
        die("Hiba: Csak a saját magad által létrehozott celebet törölheted!");
    }
}

if ($action === 'add' || $action === 'edit') { 
    check_auth('user'); 
}

$errors = $_SESSION['errors'] ?? [];
$retain_values = $_SESSION['values'] ?? null;
unset($_SESSION['errors'], $_SESSION['values']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $chosen_platforms = $_POST['platforms'] ?? [];
    $celeb_type = $_POST['type'] ?? '';

    $local_errors = [];
    if ($name === '') $local_errors[] = "A név megadása kötelező!";
    if (empty($chosen_platforms)) $local_errors[] = "Legalább egy platformot ki kell választani!";

    if (!empty($local_errors)) {
        $_SESSION['errors'] = $local_errors;
        $_SESSION['values'] = (object)[
            'name' => $name,
            'image' => $image,
            'platforms' => $chosen_platforms,
            'type' => $celeb_type
        ];
        redirect("details.php?action=$action" . ($id ? "&id=$id" : ""));
    }

    $data = [
        'name' => $name,
        'image' => $image,
        'platforms' => $chosen_platforms,
        'type' => $celeb_type !== '' ? $celeb_type : null
    ];

    if ($action === 'add') {
        // BÓNUSZ: Létrehozó felhasználó ID rögzítése a törlési jogosultsághoz
        $data['created_by'] = $_SESSION['user']['id'] ?? null;
        $new_id = $cStorage->add($data);
        redirect("details.php?id=" . $new_id);
    } elseif ($action === 'edit' && $id !== null) {
        // Szerkesztéskor megtartjuk az eredeti létrehozót
        if (isset($celeb['created_by'])) {
            $data['created_by'] = $celeb['created_by'];
        }
        $cStorage->update($id, $data);
        redirect("details.php?id=" . $id);
    }
}

require_once 'header.php';
?>
    <p><a href="index.php" style="text-decoration:none; color:#555;">← Vissza a főoldalra</a></p>

    <?php if (!empty($errors)): ?>
        <div class="box" style="border-left: 5px solid #d32f2f;">
            <ul class="error">
                <?php foreach($errors as $error): ?> <li><?= $error ?></li> <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <?php if ($action === 'view' && $celeb): ?>
        <div class="card">
            <h2><?= htmlspecialchars($celeb['name']) ?></h2>
            <?php if (!empty($celeb['image'])): ?>
                <img src="<?= htmlspecialchars($celeb['image']) ?>" alt="celeb fotó" style="max-width: 100%; height:auto; display:block; margin-bottom:15px; border-radius:4px;">
            <?php endif ?>
            <p><strong>Kategória:</strong> 
                <?php 
                $ct = $ctStorage->findById($celeb['type'] ?? '');
                echo $ct ? htmlspecialchars($ct['name']) : 'Nincs megadva';
                ?>
            </p>
            
            <?php if (is_logged_in()): ?>
                <hr>
                <div style="display: flex; gap: 15px; align-items: center;">
                    <a href="details.php?action=edit&id=<?= $id ?>" style="color:#2e7d32; font-weight:bold; text-decoration:none;">Celeb Szerkesztése →</a>
                    
                    <?php 
                    $curr_uid = $_SESSION['user']['id'] ?? '';
                    if (get_role() === 'admin' || (isset($celeb['created_by']) && $celeb['created_by'] === $curr_uid)): ?>
                        <a href="details.php?action=delete&id=<?= $id ?>" onclick="return confirm('Biztosan törölni szeretnéd ezt a celebet?')" style="color:#d32f2f; font-weight:bold; text-decoration:none;">Celeb Törlése 🗑️</a>
                    <?php endif ?>
                </div>
            <?php endif ?>
        </div>

    <?php elseif ($action === 'add' || ($action === 'edit' && $celeb)): ?>
        <?php 
            $v_name = $retain_values->name ?? $celeb['name'] ?? '';
            $v_image = $retain_values->image ?? $celeb['image'] ?? '';
            $v_type = $retain_values->type ?? $celeb['type'] ?? '';
            $v_plats = $retain_values->platforms ?? $celeb['platforms'] ?? [];
        ?>
        <h2><?= $action === 'add' ? 'Új celeb hozzáadása' : 'Celeb szerkesztése' ?></h2>
        <form method="POST" class="card">
            <label>Név *:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($v_name) ?>">

            <label>Kép URL (opcionális):</label>
            <input type="text" name="image" value="<?= htmlspecialchars($v_image) ?>">

            <label>Celeb típusa:</label>
            <select name="type">
                <option value="">-- Nincs kategória --</option>
                <?php foreach ($celeb_types as $ctId => $ct): ?>
                    <option value="<?= $ctId ?>" <?= $v_type === $ctId ? 'selected' : '' ?>><?= htmlspecialchars($ct['name']) ?></option>
                <?php endforeach ?>
            </select>

            <p><strong>Platformok (Legalább egy kötelező) *:</strong></p>
            <?php foreach ($platforms as $pId => $p): ?>
                <label style="display:block; margin-bottom:8px; cursor:pointer;">
                    <input type="checkbox" name="platforms[]" value="<?= $pId ?>" <?= in_array($pId, $v_plats) ? 'checked' : '' ?>>
                    <span style="color: <?= $p['color'] ?>; font-weight: bold;"><?= htmlspecialchars($p['name']) ?></span>
                </label>
            <?php endforeach ?>
            <br>
            <button type="submit">Mentés</button>
        </form>
    <?php else: ?>
        <p>Celeb nem található.</p>
    <?php endif ?>
</body>
</html>