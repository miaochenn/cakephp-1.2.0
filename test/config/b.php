<?php

//define("APPS", APP . '/apps');


function config() {
    $numArgs = func_num_args();
    $argList = func_get_args();
    for ($i = 0; $i < $numArgs; $i++) {
        echo "Argument $i is: " . $argList[$i] . PHP_EOL;
    }
}