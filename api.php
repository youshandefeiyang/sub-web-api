<?php
$inputcontent = $_POST['url'] ?? null;
if (empty($inputcontent)) {
    $arr = array('msg' => "failed", 'data' => "empty value");
    echo json_encode($arr, 320);
    exit();
} else {
    $diyscript = urldecode($_POST['sortscript']);
    $target = urldecode($_POST['target']);
    $url = urldecode($_POST['url']);
    $config = urldecode($_POST['config']);
    $exclude = urldecode($_POST['exclude']);
    $include = urldecode($_POST['include']);
    $filename = urldecode($_POST['filename']);
    $rename = urldecode($_POST['rename']);
    $append_type = urldecode($_POST['append_type']);
    $emoji = urldecode($_POST['emoji']);
    $list = urldecode($_POST['list']);
    $udp = urldecode($_POST['udp']);
    $tfo = urldecode($_POST['tfo']);
    $expand = urldecode($_POST['expand']);
    $scv = urldecode($_POST['scv']);
    $fdn = urldecode($_POST['fdn']);
    $newname = urldecode($_POST['newname']);
    $str = <<<EOD
[Profile]
target=$target
url=$url
config=$config
exclude=$exclude
include=$include
filename=$filename
rename=$rename
append_type=$append_type
emoji=$emoji
list=$list
udp=$udp
tfo=$tfo
expand=$expand
scv=$scv
fdn=$fdn
newname=$newname
EOD;
    function mk_dir()
    {
        $dir = 'script';
        if (is_dir('./' . $dir)) {
            return $dir;
        } else {
            mkdir('./' . $dir, 0777, true);
            return $dir;
        }
    }

    function mk_inidir()
    {
        $inidir = 'subconverter';
        if (is_dir('./' . $inidir)) {
            return $inidir;
        } else {
            mkdir('./' . $inidir, 0777, true);
            return $inidir;
        }
    }

    $jspath = '/' . mk_dir() . '/' . md5($diyscript) . '.' . 'js';
    file_put_contents(".$jspath", $diyscript);
    $reg = "/profiles\/(.)+.js/i";
    $replacement = "profiles" . "$jspath";
    $pref = "../pref.toml";
    $newpref = preg_replace($reg, $replacement, file_get_contents($pref));
    file_put_contents($pref, $newpref);
    $inipath = '/' . mk_inidir() . '/' . md5($str) . '.' . 'ini';
    file_put_contents(".$inipath", $str);
    $arr = array('code' => 0, 'msg' => "success", 'data' => "https://api.d1.mk/getprofile?name=profiles$inipath&token=subconverter");
    echo json_encode($arr, 320);
}