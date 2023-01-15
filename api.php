<?php

namespace subconverter;
require __DIR__ . '/config/common.php';

use config\common;

$inputcontent = $_POST['url'] ?? null;
if (empty($inputcontent)) {
    $arr = array('msg' => "failed", 'data' => "empty value");
    echo json_encode($arr, 320);
    exit();
} else {
    $diyscript = urldecode($_POST['sortscript']);
    $filterscript = urldecode($_POST['filterscript']);
    $target = urldecode($_POST['target']);
    $url = urldecode($_POST['url']);
    $config = urldecode($_POST['config']);
    $exclude = urldecode($_POST['exclude']);
    $include = urldecode($_POST['include']);
    $tls13 = urldecode($_POST['tls13']);
    $rename = urldecode($_POST['rename']);
    $xudp = urldecode($_POST['xudp']);
    $emoji = urldecode($_POST['emoji']);
    $list = urldecode($_POST['list']);
    $udp = urldecode($_POST['udp']);
    $tfo = urldecode($_POST['tfo']);
    $expand = urldecode($_POST['expand']);
    $scv = urldecode($_POST['scv']);
    $fdn = urldecode($_POST['fdn']);
    $sdoh = urldecode($_POST['sdoh']);
    $cdoh = urldecode($_POST['cdoh']);
    $newname = urldecode($_POST['newname']);
    $panduan = explode("surge", $target);
    if (count($panduan) > 1) {
        $num = substr($target, -1);
        $str = <<<EOD
[Profile]
target=surge
surge_ver=$num
url=$url
config=$config
exclude=$exclude
include=$include
tls13=$tls13
rename=$rename
strict=$surgeForce
emoji=$emoji
list=$list
xudp=$xudp
udp=$udp
tfo=$tfo
expand=$expand
scv=$scv
fdn=$fdn
surge.doh=$sdoh
EOD;
    } else {
        $str = <<<EOD
[Profile]
target=$target
url=$url
config=$config
exclude=$exclude
include=$include
tls13=$tls13
rename=$rename
emoji=$emoji
list=$list
xudp=$xudp
udp=$udp
tfo=$tfo
expand=$expand
scv=$scv
fdn=$fdn
clash.doh=$cdoh
newname=$newname
EOD;
    }
    $scriptdir = new Commonfunction;
    $md5jscontent = md5($diyscript);
    $jspath = '/' . $scriptdir->mk_dir('script') . '/' . $md5jscontent . '.' . 'js';
    file_put_contents(".$jspath", $diyscript);
    $md5filtercontent = md5($filterscript);
    $filterpath = '/' . $scriptdir->mk_dir('filter') . '/' . $md5filtercontent . '.' . 'js';
    file_put_contents(".$filterpath", $filterscript);
    $md5inicontent = md5($str);
    $inipath = '/' . $scriptdir->mk_dir('subconverter') . '/' . $md5inicontent . '.' . 'ini';
    file_put_contents(".$inipath", $str);
    require __DIR__ . '/config/connect.php';
    $sql = 'INSERT `mdfive` SET `inilist` = ?,`jslist` = ?,`filterlist` = ?';
    $stmt = $db->prepare($sql);
    $stmt->execute([$md5inicontent, $md5jscontent, $md5filtercontent]);
    $md5encode = urlencode(md5($str));
    $arr = array('code' => 0, 'msg' => "success", 'data' => "https://subapi.v1.mk/redirect.php?token=$md5encode");
    echo json_encode($arr, 320);
}
