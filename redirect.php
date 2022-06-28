<?php

namespace subconverter;
$token = urldecode($_GET['token']) ?? null;
if (empty($token)) {
    $arr = array('msg' => "failed", 'data' => "empty value");
    echo json_encode($arr, 320);
    exit();
} else {
    require __DIR__ . '/config/connect.php';
    $sql = 'SELECT `jslist` FROM `mdfive` WHERE `inilist` = ?';
    $stmt = $db->prepare($sql);
    $inilist = $token;
    $stmt->execute([$inilist]);
    $jsname = end($stmt->fetchAll())['jslist'];
    $reg = "/profiles\/(.)+.js/i";
    $replacement = "profiles/script/$jsname.js";
    $pref = "../pref.toml";
    $newpref = preg_replace($reg, $replacement, file_get_contents($pref));
    file_put_contents($pref, $newpref);
    header("Location: https://api.d1.mk/getprofile?name=profiles/subconverter/$token.ini&token=subconverter");
}