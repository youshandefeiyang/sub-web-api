<?php

namespace subconverter;
class Commonfunction
{
    public function mk_dir($newdir)
    {
        $dir = $newdir;
        if (is_dir('./' . $dir)) {
            return $dir;
        } else {
            mkdir('./' . $dir, 0777, true);
            return $dir;
        }
    }
}