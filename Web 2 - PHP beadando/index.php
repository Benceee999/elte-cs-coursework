<?php
require_once 'init.php';

$search = $_GET['search'] ?? '';
$filter_platform = $_GET['filter_platform'] ?? '';
$filter_ptype = $_GET['filter_ptype'] ?? '';

$celebs = $cStorage->findAll();
$platforms = $pStorage->findAll();
$platform_types = $ptStorage->findAll();
$celeb_types = $ctStorage->findAll();

$filtered_celebs = array_filter($celebs, function($celeb) use ($search, $filter_platform, $filter_ptype, $pStorage) {
    // JAVÍTVA: mb_strtolower az ékezetes karakterekhez
    if ($search !== '' && !str_contains(mb_strtolower($celeb['name']), mb_strtolower($search))) {
        return false;
    }

    if ($filter_platform === '' && $filter_ptype === '') {
        return true;
    }

    $platform_match = false;
    $ptype_match = false;

    if ($filter_platform !== '') {
        if (in_array($filter_platform, $celeb['platforms'] ?? [])) {
            $platform_match = true;
        }
    }

    if ($filter_ptype !== '') {
        foreach ($celeb['platforms'] ?? [] as $pid) {
            $p = $pStorage->findById($pid);
            if ($p && $p['type'] === $filter_ptype) {
                $ptype_match = true;
                break;
            }
        }
    }

    if ($filter_platform !== '' && $filter_ptype !== '') {
        return $platform_match || $ptype_match;
    }

    if ($filter_platform !== '') return $platform_match;
    if ($filter_ptype !== '') return $ptype_match;

    return true;
});

require_once 'header.php';
?>

    <h2>Celebek keresése és szűrése</h2>
    <div class="box" style="max-width: 100%;">
        <form method="GET" action="index.php" style="display: flex; gap: 15px; align-items: center; width: 100%; flex-wrap: wrap;">
            
            <div style="flex: 2; min-width: 200px;">
                <input type="text" name="search" placeholder="Keresés névre..." value="<?= htmlspecialchars($search) ?>" style="margin: 0;">
            </div>
            
            <div style="flex: 1; min-width: 180px;">
                <select name="filter_platform" style="margin: 0;">
                    <option value="">-- Minden Platform --</option>
                    <?php foreach($platforms as $pId => $p): ?>
                        <option value="<?= $pId ?>" <?= $filter_platform === $pId ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="flex: 1; min-width: 180px;">
                <select name="filter_ptype" style="margin: 0;">
                    <option value="">-- Minden Platform Típus --</option>
                    <?php foreach($platform_types as $ptId => $pt): ?>
                        <option value="<?= $ptId ?>" <?= $filter_ptype === $ptId ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pt['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="flex-shrink: 0; display: flex; gap: 10px; align-items: center;">
                <button type="submit" style="margin: 0;">Szűrés</button>
                <a href="index.php" style="text-decoration: none; color: #666; font-size: 14px; font-weight: bold;">Alaphelyzet</a>
            </div>
        </form>
    </div>

    <h2>Celeb Lista</h2>
    <?php if (empty($filtered_celebs)): ?>
        <p style="font-style: italic; color: #666;">Nincs a szűrési feltételeknek megfelelő celeb.</p>
    <?php endif ?>

    <?php foreach ($filtered_celebs as $cId => $celeb): ?>
        <div class="celeb-card">
            <h3>
                <a href="details.php?id=<?= $cId ?>" style="color: #222; text-decoration: none;"><?= htmlspecialchars($celeb['name']) ?></a>
                <?php 
                $ct = $ctStorage->findById($celeb['type'] ?? '');
                if ($ct): ?>
                    <span class="type-badge"><?= htmlspecialchars($ct['name']) ?></span>
                <?php endif ?>
            </h3>
            <p>Platformok: 
                <?php 
                foreach ($celeb['platforms'] ?? [] as $pid) {
                    $p = $pStorage->findById($pid);
                    if ($p) {
                        // BÓNUSZ: Keresés kattintással (a platform badge linkként viselkedik)
                        echo "<a href='index.php?filter_platform=".urlencode($pid)."' class='badge' style='background-color: {$p['color']}; text-decoration: none;' title='Kattints a szűréshez!'>".htmlspecialchars($p['name'])."</a> ";
                    }
                }
                ?>
            </p>
        </div>
    <?php endforeach ?>

    <hr style="margin: 40px 0; border: 0; border-top: 1px solid #ccc;">

    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
        <div class="box" style="flex: 1; min-width: 280px; max-width: 100%;">
            <h3>Rendszerben lévő Platformok</h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php foreach ($platforms as $p): ?>
                    <?php $ptName = $ptStorage->findById($p['type'])['name'] ?? 'Ismeretlen'; ?>
                    <li style="margin-bottom: 10px; padding: 8px; background: #fafafa; border-radius: 4px; border-left: 4px solid <?= $p['color'] ?>;">
                        <span class="badge" style="background-color: <?= $p['color'] ?>; margin-left: 0;"><?= htmlspecialchars($p['name']) ?></span>
                        <span style="font-size: 13px; color: #666; margin-left: 5px;">— Típus: <?= htmlspecialchars($ptName) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="box" style="flex: 1; min-width: 280px; max-width: 100%;">
            <h3>Rendszerben lévő Platform Típusok</h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php foreach ($platform_types as $pt): ?>
                    <li style="margin-bottom: 10px; padding: 10px; background: #fafafa; border-radius: 4px; color: #444; font-weight: bold; font-size: 14px; border-left: 4px solid #777;">
                        <?= htmlspecialchars($pt['name']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>