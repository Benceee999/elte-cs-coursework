<?php
require_once 'functions_storage.php';

function redirect($target) {
    header("Location: $target");
    die; // mivel a header location nem állítja le a script futását, nekünk meg kell ezt még tenni
}

function new_storage($filename)
{
    $json_kezelo = new JsonIO("$filename.json");
    return new Storage($json_kezelo);
}