<?php
/* SVN FILE: $Id$ */
/**
 * Get Cake's root directory
 * echo __FILE__; // /Users/miaojingchao/HomeShare/cakephp-1.2.0/index.php
 * echo dirname(__FILE__); // /Users/miaojingchao/HomeShare/cakephp-1.2.0
 *
 * ROOT => /Users/miaojingchao/HomeShare/cakephp-1.2.0
 * WWW_ROOT => /Users/miaojingchao/HomeShare/cakephp-1.2.0/app/webroot/
 *
 */
	define('APP_DIR', 'app');
	define('DS', DIRECTORY_SEPARATOR);
	define('ROOT', dirname(__FILE__)); // dirname(string $path, int $levels = 1) 本函数返回去掉文件名后的目录名
	define('WEBROOT_DIR', 'webroot'); // __FILE__文件的完整路径和文件名。如果用在被包含文件中，则返回被包含的文件名
	define('WWW_ROOT', ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS); // cakephp-1.2.0/app/webroot/

/**
 * This only needs to be changed if the cake installed libs are located
 * outside of the distributed directory structure.
 */
	if (!defined('CAKE_CORE_INCLUDE_PATH')) {
		//define ('CAKE_CORE_INCLUDE_PATH', FULL PATH TO DIRECTORY WHERE CAKE CORE IS INSTALLED DO NOT ADD A TRAILING DIRECTORY SEPARATOR';
		define('CAKE_CORE_INCLUDE_PATH', ROOT); // /Users/miaojingchao/HomeShare/cakephp-1.2.0
	}
	if (function_exists('ini_set')) {
		ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . CAKE_CORE_INCLUDE_PATH . PATH_SEPARATOR . ROOT . DS . APP_DIR . DS);
        // ini_set('include_path', '') 在脚本里动态地对PHP.ini中include_path进行修改
        // 而这个include_path呢，它可以针对下面的include和require的路径范围进行限定，或者说是预定义一下
		// /Users/miaojingchao/HomeShare/cakephp-1.2.0
        // /Users/miaojingchao/HomeShare/cakephp-1.2.0/app/
        define('APP_PATH', null);
		define('CORE_PATH', null);
	} else {
		define('APP_PATH', ROOT . DS . APP_DIR . DS);
		define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
	}

    // /Users/miaojingchao/HomeShare/cakephp-1.2.0/cake/basice.php
	require CORE_PATH . 'cake' . DS . 'basics.php'; // CORE_PATH 为null，因为设置了 include_path，所以会加上前缀再拼接后面的路径。
    $TIME_START = getMicrotime();
	// /Users/miaojingchao/HomeShare/cakephp-1.2.0/cake/config/paths.php
    require CORE_PATH . 'cake' . DS . 'config' . DS . 'paths.php';
    //定义php文件 <?php 要想使用短标签 <? 得开启php.ini short_open_tag; 这个不能动态指定，建议不要使用短标签语法
    // LIBS => cake/libs/
    require LIBS . 'object.php'; // require 就相当于把多个文件的常量定义整合到了一个文件里，只要前面有定义就能直接使用。
	require LIBS . 'inflector.php';
	require LIBS . 'configure.php';

	$bootstrap = true;
	$url = null;
	require APP_DIR . DS . WEBROOT_DIR . DS . 'index.php';
?>