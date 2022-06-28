<?php
$token = urldecode($_GET['token']) ?? null;
if (empty($token)) {
    $arr = array('msg' => "failed", 'data' => "empty value");
    echo json_encode($arr, 320);
    exit();
} else {
    $reg = "/profiles\/(.)+.js/i";
    $replacement = "profiles/script/$token.js";
    $pref = "../pref.toml";
    $newpref = preg_replace($reg, $replacement, file_get_contents($pref));
    file_put_contents($pref, $newpref);
    header("Location: https://api.d1.mk/getprofile?name=profiles/subconverter/$token.ini&token=subconverter");
}