<?php
header('Content-Type: application/json');
$userText = $_POST['config'];
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
    function randName()
    {
        $str = 'abcdefghijkmnpqrstwxyz23456789';
        return substr(str_shuffle($str), 0, 6);
    }
    $path = '/' . mk_dir() . '/' . randName() . '.' . 'ini';
    function writeText($str, $fileName)
    {
        $userFile = fopen($fileName, "w+");
        fwrite($userFile, $str);
        fclose($userFile);
    }
    writeText($userText, "./" . $path);
    $arr = array('code' => 0, 'msg' => "success", 'data' => "https://subapi.v1.mk$path");
    echo json_encode($arr, 320);
}
?>
