<?php
session_start(); 
require_once 'functions.php'; 

$files = ['data_platform_types.json', 'data_platforms.json', 'data_celeb_types.json', 'data_celebs.json', 'data_users.json'];
foreach ($files as $file) {
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }
}

$ptStorage = new_storage('data_platform_types');
$pStorage  = new_storage('data_platforms');
$ctStorage = new_storage('data_celeb_types');
$cStorage  = new_storage('data_celebs');
$uStorage  = new_storage('data_users');

if (empty($ptStorage->findAll())) {
    
    $pt_short  = $ptStorage->add(['name' => 'Short-form content']);
    $pt_trad   = $ptStorage->add(['name' => 'Tradicionális közösségi média']);
    $pt_tv     = $ptStorage->add(['name' => 'Televízió']);
    $pt_stream = $ptStorage->add(['name' => 'Streaming and Life']);

    $p_kitkot    = $pStorage->add(['name' => 'KitKot', 'color' => '#fc03db', 'type' => $pt_short]);
    $p_fans      = $pStorage->add(['name' => 'CsakVentillátorok', 'color' => '#03dbfc', 'type' => $pt_short]);
    $p_iwiw      = $pStorage->add(['name' => 'Iwiw', 'color' => '#c7d2d4', 'type' => $pt_trad]);
    $p_facetoot  = $pStorage->add(['name' => 'Facetoot', 'color' => '#0b24de', 'type' => $pt_trad]);
    $p_ticcccs   = $pStorage->add(['name' => 'Ticcccs', 'color' => '#e100ff', 'type' => $pt_stream]);

    $ct_inf  = $ctStorage->add(['name' => 'Influenszer']);
    $ct_sing = $ctStorage->add(['name' => 'Énekes']);
    $ct_god  = $ctStorage->add(['name' => 'SzexIsten']);

    $cStorage->add([
        'name' => 'Bajusz Patrik', 
        'platforms' => [$p_kitkot, $p_facetoot], 
        'image' => 'https://avatars.akamai.steamstatic.com/7f44effe25d38f6ed25b563272e51f0ad7395354_full.jpg', 
        'type' => $ct_inf
    ]);

    $cStorage->add([
        'name' => 'LordKyron', 
        'platforms' => [$p_fans, $p_iwiw], 
        'image' => 'https://avatars.akamai.steamstatic.com/62c43db1999718163e9817d92adfd7d53feeef71_full.jpg', 
        'type' => $ct_sing
    ]);

    $cStorage->add([
        'name' => 'B3RN4TH', 
        'platforms' => [$p_kitkot, $p_fans, $p_ticcccs], 
        'image' => 'https://avatars.akamai.steamstatic.com/49fc6e01b123169d55044dff59add1047557bf82_full.jpg', 
        'type' => $ct_god
    ]);

    $cStorage->add([
        'name' => 'Rajosan', 
        'platforms' => [$p_kitkot, $p_facetoot], 
        'image' => 'https://avatars.akamai.steamstatic.com/175b69ec3103a04dd91c8c12f48645c891438112_full.jpg', 
        'type' => $ct_sing
    ]);
}

if (!$uStorage->findOne(['username' => 'admin'])) {
    $uStorage->add([
        'username' => 'admin',
        'email' => 'admin@partyzone.hu',
        'password' => password_hash('admin', PASSWORD_DEFAULT),
        'role' => 'admin'
    ]);
}

function get_role() {
    return $_SESSION['user']['role'] ?? 'guest';
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function check_auth($min_role) {
    $role = get_role();
    if ($min_role === 'admin' && $role !== 'admin') {
        die('Ehhez a művelethez admin jogosultság szükséges!');
    }
    if ($min_role === 'user' && $role === 'guest') {
        die('Ehhez a művelethez be kell jelentkezned!');
    }
}