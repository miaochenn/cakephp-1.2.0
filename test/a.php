<?php
define("APP", '/app');

//echo APPS; // 注意引入的位置，这个输出得在引入以后输出

ini_set('include_path', '/Users/miaojingchao/HomeShare/'); // 最后加不加 '/' 都行,
// /config/ 或者 /config 都可以


include 'cakephp-1.2.0/test/config/c.php';
require 'cakephp-1.2.0/test/config/b.php';
//require 'b.php';
//include 'c.php';


echo ROOT;
echo PHP_EOL;

config(1,2,3);
cConfig();

function foo(&$var)
{
    $var++;
    echo $var;
}
function &bar()
{
    $a = 5;
    return $a;
}
foo(bar());

