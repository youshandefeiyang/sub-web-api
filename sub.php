<?php
header('Content-Type: application/json');
$userText = $_POST['config'] ?? null;
if (empty($userText)) {
    $arr = array('msg' => "failed", 'data' => "empty value");
    echo json_encode($arr, 320);
    exit();
} else {
    function mk_dir()
    {
        $dir = 'subconverter/' . date('Y/m/md', time());
        if (is_dir('./' . $dir)) {
            return $dir;
        } else {
            mkdir('./' . $dir, 0777, true);
            return $dir;
        }
    }
    $path = '/' . mk_dir() . '/' . md5($userText) . '.' . 'ini';
    file_put_contents(".$path",$userText);
    $arr = array('code' => 0, 'msg' => "success", 'data' => "https://subapi.v1.mk$path");
    echo json_encode($arr, 320);
}
