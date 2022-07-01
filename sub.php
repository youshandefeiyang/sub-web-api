<?php

namespace subconverter;
require __DIR__ . '/config/common.php';

use config\common;

header('Content-Type: application/json');
$param = $_POST['config'] ?? null;
if (empty($param)) {
    $arr = array('msg' => "failed", 'data' => "empty value");
    echo json_encode($arr, 320);
    exit();
} else {
    $userText = urldecode($_POST['config']);
    $remote = new Commonfunction;
    $path = '/' . $remote->mk_dir('remoteconfig/' . date('Y/m/md', time())) . '/' . md5($userText) . '.' . 'ini';
    file_put_contents(".$path", $userText);
    $arr = array('code' => 0, 'msg' => "success", 'data' => "https://subapi.v1.mk$path");
    echo json_encode($arr, 320);
}