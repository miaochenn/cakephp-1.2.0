<?php

include_once './config/b.php';

function aa() {
    $args = func_get_args();
    $argc = count($args);
    for ($i = 0; $i < $argc; $i++) {
        if ($i + 1 < $argc) {
            $a[$args[$i]] = $args[$i + 1];
        } else {
            $a[$args[$i]] = null;
        }
        $i++;
    }
    return $a;
}

$res = aa('a',1,'b',2,'c');
var_dump($res);