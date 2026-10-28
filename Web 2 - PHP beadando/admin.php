<?php
require_once 'init.php';
check_auth('admin'); 

$errors = [];
$success = "";

// BÓNUSZ: TÖRLÉSI MŰVELETEK INTEGRITÁS-ELLENŐRZÉSSEL
$delete_type = $_GET['delete_type'] ?? null;
$delete_id = $_GET['delete_id'] ?? null;

if ($delete_type && $delete_id !== null) {
    if ($delete_type === 'ptype') {
        $platforms_using = $pStorage->findMany(fn($p) => $p['type'] === $delete_id);
        if (!empty($platforms_using)) {
            $errors[] = "Hiba: A platformtípus nem törölhető, mert tartozik hozzá platform!";
        } else {
            $ptStorage->delete($delete_id);
            redirect("admin.php#ptype-section");
        }
    }

    if ($delete_type === 'platform') {
        $celebs_using = $cStorage->findMany(fn($c) => in_array($delete_id, $c['platforms'] ?? []));
        if (!empty($celebs_using)) {
            $errors[] = "Hiba: A platform nem törölhető, mert van celeb, aki használja!";
        } else {
            $pStorage->delete($delete_id);
            redirect("admin.php#platform-section");
        }
    }

    if ($delete_type === 'ctype') {
        $celebs_using = $cStorage->findMany(fn($c) => ($c['type'] ?? '') === $delete_id);
        if (!empty($celebs_using)) {
            $errors[] = "Hiba: A celebtípus nem törölhető, mert tartozik hozzá celeb!";
        } else {
            $ctStorage->delete($delete_id);
            redirect("admin.php#ctype-section");
        }
    }
}

$edit_type = $_GET['edit_type'] ?? null;
$edit_id = $_GET['id'] ?? null;
$edit_item = null;

if ($edit_type && $edit_id !== null) {
    if ($edit_type === 'ptype') $edit_item = $ptStorage->findById($edit_id);
    if ($edit_type === 'platform') $edit_item = $pStorage->findById($edit_id);
    if ($edit_type === 'ctype') $edit_item = $ctStorage->findById($edit_id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_action = $_POST['form_action'] ?? 'add';
    $target = $_POST['target'] ?? '';
    $id = $_POST['id'] ?? null;

    if ($target === 'ptype') {
        $name = trim($_POST['name'] ?? '');
        if ($name === '') $errors[] = "A platformtípus neve kötelező!";
        
        $existing = $ptStorage->findOne(['name' => $name]);
        if ($existing && ($form_action === 'add' || $existing['id'] !== $id)) {
            $errors[] = "Már létezik ilyen nevű platformtípus!";
        }
        
        if (empty($errors)) {
            if ($form_action === 'add') {
                $ptStorage->add(['name' => $name]);
            } else {
                $ptStorage->update($id, ['name' => $name]);
            }
            redirect("admin.php#ptype-section");
        }
    }

    if ($target === 'platform') {
        $name = trim($_POST['name'] ?? '');
        $color = trim($_POST['color'] ?? '#000000');
        $type = $_POST['type'] ?? '';

        if ($name === '') $errors[] = "A platform neve kötelező!";
        if ($type === '' || !$ptStorage->findById($type)) $errors[] = "A választott platformtípusnak léteznie kell!";
        
        $existing = $pStorage->findOne(['name' => $name]);
        if ($existing && ($form_action === 'add' || $existing['id'] !== $id)) {
            $errors[] = "Már létezik ilyen nevű platform!";
        }

        if (empty($errors)) {
            $data = ['name' => $name, 'color' => $color, 'type' => $type];
            if ($form_action === 'add') {
                $pStorage->add($data);
            } else {
                $pStorage->update($id, $data);
            }
            redirect("admin.php#platform-section");
        }
    }

    if ($target === 'ctype') {
        $name = trim($_POST['name'] ?? '');
        if ($name === '') $errors[] = "A celebtípus neve kötelező!";
        
        $existing = $ctStorage->findOne(['name' => $name]);
        if ($existing && ($form_action === 'add' || $existing['id'] !== $id)) {
            $errors[] = "Már létezik ilyen nevű celebtípus!";
        }

        if (empty($errors)) {
            if ($form_action === 'add') {
                $ctStorage->add(['name' => $name]);
            } else {
                $ctStorage->update($id, ['name' => $name]);
            }
            redirect("admin.php#ctype-section");
        }
    }
}

$platform_types = $ptStorage->findAll();
$platforms = $pStorage->findAll();
$celeb_types = $ctStorage->findAll();

require_once 'header.php';
?>
    <h2>Rendszerkezelő Adminisztrációs Panel</h2>

    <?php if (!empty($errors)) foreach($errors as $e) echo "<p class='error'>$e</p>"; ?>

    <div style="display: flex; flex-direction: column; gap: 30px;">

        <div class="box" id="ptype-section" style="max-width: 100%;">
            <h3><?= ($edit_type === 'ptype') ? 'Platform Típus Szerkesztése' : 'Platform Típus Hozzáadása' ?></h3>
            <form method="POST" style="display: flex; gap: 10px; align-items: center;">
                <input type="hidden" name="target" value="ptype">
                <input type="hidden" name="form_action" value="<?= ($edit_type === 'ptype') ? 'edit' : 'add' ?>">
                <?php if ($edit_type === 'ptype'): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif ?>

                <input type="text" name="name" placeholder="Típus neve (pl. Streaming)" 
                       value="<?= htmlspecialchars($_POST['name'] ?? $edit_item['name'] ?? '') ?>" style="margin:0;" required>
                
                <button type="submit"><?= ($edit_type === 'ptype') ? 'Módosítás' : 'Mentés' ?></button>
                <?php if ($edit_type === 'ptype'): ?>
                    <a href="admin.php#ptype-section" style="text-decoration:none; color:#666; font-size:13px; font-weight:bold;">Mégse</a>
                <?php endif ?>
            </form>

            <h4>Létező platform típusok:</h4>
            <ul>
                <?php foreach($platform_types as $ptId => $pt): ?>
                    <li style="margin-bottom: 5px;">
                        <?= htmlspecialchars($pt['name']) ?> 
                        <a href="admin.php?edit_type=ptype&id=<?= $ptId ?>#ptype-section" style="font-size:12px; margin-left: 10px; color: #2e7d32;">[Szerkesztés]</a>
                        <a href="admin.php?delete_type=ptype&id=<?= $ptId ?>" onclick="return confirm('Biztosan törlöd?')" style="font-size:12px; margin-left: 8px; color: #d32f2f;">[Törlés]</a>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>

        <div class="box" id="platform-section" style="max-width: 100%;">
            <h3><?= ($edit_type === 'platform') ? 'Platform Szerkesztése' : 'Platform Hozzáadása' ?></h3>
            <form method="POST">
                <input type="hidden" name="target" value="platform">
                <input type="hidden" name="form_action" value="<?= ($edit_type === 'platform') ? 'edit' : 'add' ?>">
                <?php if ($edit_type === 'platform'): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif ?>

                <label>Platform neve *:</label>
                <input type="text" name="name" placeholder="Pl. KitKot" value="<?= htmlspecialchars($_POST['name'] ?? $edit_item['name'] ?? '') ?>" required>
                
                <label>Szín kód *:</label>
                <input type="color" name="color" value="<?= htmlspecialchars($_POST['color'] ?? $edit_item['color'] ?? '#000000') ?>" style="display:block; margin-bottom:15px;">
                
                <label>Platform típusa *:</label>
                <select name="type" required>
                    <option value="">-- Válassz platform típust --</option>
                    <?php foreach($platform_types as $ptId => $pt): ?>
                        <option value="<?= $ptId ?>" <?= (isset($edit_item['type']) && $edit_item['type'] === $ptId) ? 'selected' : '' ?>><?= htmlspecialchars($pt['name']) ?></option>
                    <?php endforeach ?>
                </select>

                <button type="submit"><?= ($edit_type === 'platform') ? 'Módosítás' : 'Mentés' ?></button>
                <?php if ($edit_type === 'platform'): ?>
                    <a href="admin.php#platform-section" style="text-decoration:none; color:#666; font-size:13px; font-weight:bold; margin-left:10px;">Mégse</a>
                <?php endif ?>
            </form>

            <h4>Létező platformok:</h4>
            <ul>
                <?php foreach($platforms as $pId => $p): ?>
                    <?php $ptName = $ptStorage->findById($p['type'])['name'] ?? 'Ismeretlen'; ?>
                    <li style="margin-bottom: 5px;">
                        <span class="badge" style="background-color: <?= $p['color'] ?>; margin-left:0; margin-right:5px;"><?= htmlspecialchars($p['name']) ?></span> 
                        (Típus: <?= htmlspecialchars($ptName) ?>)
                        <a href="admin.php?edit_type=platform&id=<?= $pId ?>#platform-section" style="font-size:12px; margin-left: 10px; color: #2e7d32;">[Szerkesztés]</a>
                        <a href="admin.php?delete_type=platform&id=<?= $pId ?>" onclick="return confirm('Biztosan törlöd?')" style="font-size:12px; margin-left: 8px; color: #d32f2f;">[Törlés]</a>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>

        <div class="box" id="ctype-section" style="max-width: 100%;">
            <h3><?= ($edit_type === 'ctype') ? 'Celeb Típus Szerkesztése' : 'Celeb Típus Hozzáadása' ?></h3>
            <form method="POST" style="display: flex; gap: 10px; align-items: center;">
                <input type="hidden" name="target" value="ctype">
                <input type="hidden" name="form_action" value="<?= ($edit_type === 'ctype') ? 'edit' : 'add' ?>">
                <?php if ($edit_type === 'ctype'): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif ?>

                <input type="text" name="name" placeholder="Új kategória" 
                       value="<?= htmlspecialchars($_POST['name'] ?? $edit_item['name'] ?? '') ?>" style="margin:0;" required>
                
                <button type="submit"><?= ($edit_type === 'ctype') ? 'Módosítás' : 'Mentés' ?></button>
                <?php if ($edit_type === 'ctype'): ?>
                    <a href="admin.php#ctype-section" style="text-decoration:none; color:#666; font-size:13px; font-weight:bold;">Mégse</a>
                <?php endif ?>
            </form>

            <h4>Létező celeb típusok:</h4>
            <ul>
                <?php foreach($celeb_types as $ctId => $ct): ?>
                    <li style="margin-bottom: 5px;">
                        <?= htmlspecialchars($ct['name']) ?> 
                        <a href="admin.php?edit_type=ctype&id=<?= $ctId ?>#ctype-section" style="font-size:12px; margin-left: 10px; color: #2e7d32;">[Szerkesztés]</a>
                        <a href="admin.php?delete_type=ctype&id=<?= $ctId ?>" onclick="return confirm('Biztosan törlöd?')" style="font-size:12px; margin-left: 8px; color: #d32f2f;">[Törlés]</a>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>

    </div>
</body>
</html>