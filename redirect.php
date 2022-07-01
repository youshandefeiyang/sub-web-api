<?php

namespace subconverter;
$param = $_GET['token'] ?? null;
if (empty($param)) {
    $arr = array('msg' => "failed", 'data' => "empty value");
    echo json_encode($arr, 320);
    exit();
} else {
    $token = urldecode($_GET['token']);
    require __DIR__ . '/config/connect.php';
    $sql = 'SELECT `jslist`,`filterlist` FROM `mdfive` WHERE `inilist` = ?';
    $stmt = $db->prepare($sql);
    $inilist = $token;
    $stmt->execute([$inilist]);
    $arr = end($stmt->fetchAll());
    $jsname = $arr['jslist'];
    $filtername = $arr['filterlist'];
    $jsreg = "/script\/(.)*.js/i";
    $replacement = "script/$jsname.js";
    $pref = "../pref.toml";
    $newpref = preg_replace($jsreg, $replacement, file_get_contents($pref));
    file_put_contents($pref, $newpref);
    $filterreg = "/filter\/(.)*.js/i";
    $freplace = "filter/$filtername.js";
    $secondpref = preg_replace($filterreg, $freplace, file_get_contents($pref));
    file_put_contents($pref, $secondpref);
    header("Location: https://api.d1.mk/getprofile?name=profiles/subconverter/$token.ini&token=subconverter");
}