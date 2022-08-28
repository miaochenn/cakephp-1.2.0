<?php
/* SVN FILE: $Id$ */
/**
 * Basic Cake functionality.
 *
 * Core functions for including other source files, loading models and so forth.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake
 * @since         CakePHP(tm) v 0.2.9
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Basic defines for timing functions.
 */
	define('SECOND', 1);
	define('MINUTE', 60 * SECOND);
	define('HOUR', 60 * MINUTE);
	define('DAY', 24 * HOUR);
	define('WEEK', 7 * DAY);
	define('MONTH', 30 * DAY);
	define('YEAR', 365 * DAY);
/**
 * Patch for PHP < 5.0
 */
if (!function_exists('clone')) {
    // version_compare($v1, $v2) 如果 $v1 < $v2 返回 -1， 相等返回0，$v1 > $v2 返回1
	if (version_compare(PHP_VERSION, '5.0') < 0) {
		eval ('
		function clone($object)
		{
			return $object;
		}');
		// ? 定义这个clone 方法有个毛用？

		// eval('') 把字符串 code 作为PHP代码执行。
        // 函数eval()语言结构是 非常危险的， 因为它允许执行任意 PHP 代码。 它这样用是很危险的。
        // 如果您仔细的确认过，除了使用此结构以外 别无方法, 请多加注意，不要允许传入任何由用户 提供的、未经完整验证过的数据 。
	}
}
/**
 * Loads configuration files. Receives a set of configuration files
 * to load.
 * Example:
 * <code>
 * config('config1', 'config2');
 * </code>
 *
 * @return boolean Success
 */

// 这里定义的函数因为在框架执行流程里的很靠前的位置，所以整个框架内部都能调用到这些函数。

// 在调用时加载 cakephp-1.2.0/app/config 目录下的指定的配置文件，config('database', 'core', 'inflections', 'routes');
	function config() {
		$args = func_get_args();
		foreach ($args as $arg) {
			if ($arg === 'database' && file_exists(CONFIGS . 'database.php')) {
				include_once(CONFIGS . $arg . '.php'); // 只有database 这个文件名做了强限制并只执行一次
			} elseif (file_exists(CONFIGS . $arg . '.php')) { // 自己另起的配置文件名也能盲加载进来
				include_once(CONFIGS . $arg . '.php');

				if (count($args) == 1) {
					return true;
				}
			} else {
				if (count($args) == 1) {
					return false;
				}
			}
		}
		return true;
	}
/**
 * Loads component/components from LIBS. Takes optional number of parameters.
 *
 * Example:
 * <code>
 * uses('flay', 'time');
 * </code>
 *
 * @param string $name Filename without the .php part
 */
// 从 cake/libs/ 目录加载 组件，uses('file', 'object'); 就会把这些文件加载进来，这里没有做文件是否存在的判断，如果文件不存在会触发致命错误并中断执行
	function uses() {
		$args = func_get_args();
		foreach ($args as $file) {
			require_once(LIBS . strtolower($file) . '.php');
		}
	}
/**
 * Prints out debug information about given variable.
 *
 * Only runs if debug level is greater than zero.
 * 仅在调试级别大于零时运行
 *
 * @param boolean $var Variable to show debug information for.
 * @param boolean $showHtml If set to true, the method prints the debug data in a screen-friendly way.
 * @param boolean $showFrom If set to true, the method prints from where the function was called.
 * @link http://book.cakephp.org/view/458/Basic-Debugging
 */
	function debug($var = false, $showHtml = false, $showFrom = true) {
		// 这里虽然调用了 Configure::read() 方法，但是debug 函数并没有被立即调用，所以不会执行函数体
        // 等debug() 被调用时，此时由于都在入口文件做了引入，所以这里能找到Configure::read()方法
	    if (Configure::read() > 0) { // 不实例化就进行非静态方法的调用，这个非规范性的写法已经被废弃了
	        // 静态方法中不能调用非静态方法，原因很简单，静态方法不需实例化，非静态方法需要实例化
            // 非静态方法中可以self::调用静态方法。
			if ($showFrom) {
				$calledFrom = debug_backtrace(); // 产生一条回溯跟踪
                /*
                var_dump(debug_backtrace());

                array(1) {
                    [0] =>
                    array(4) {
                    ["file"] => string(10) "/tmp/a.php"
                    ["line"] => int(10)
                    ["function"] => string(6) "a_test"
                    ["args"]=>
                        array(1) {
                            [0] => &string(6) "friend"
                        }
                    }
                }
                */
				echo '<strong>' . substr(str_replace(ROOT, '', $calledFrom[0]['file']), 1) . '</strong>';
				echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
			}
			echo "\n<pre class=\"cake-debug\">\n";

			$var = print_r($var, true); // 自己赋值给自己？这有啥意义呢
            /*
            $var = true;
            var_dump($var);
            $var = print_r($var, true);
            var_dump($var);

            bool(true)
            string(1) "1"
            */
            // print_r(mixed $expression, bool $return = false): mixed;
            // 想要获取 print_r() 输出的内容，使用 return 参数。 当此参数为 true，print_r() 会直接返回信息，而不是输出
			if ($showHtml) {
				$var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
			}
			echo $var . "\n</pre>\n";
		}
	}
if (!function_exists('getMicrotime')) { // 对于这种全局函数，要先判断是否存在，不存在再执行声明，很有必要，因为全局函数不做判断如果被重新声明会报错
/**
 * Returns microtime for execution time checking
 *
 * @return float Microtime
 */
	function getMicrotime() {
		list($usec, $sec) = explode(' ', microtime()); // echo microtime(); 0.18088700 1661260210 即 $usec = 0.18088700,$sec=1661260210
        // microtime() 返回当前 Unix 时间戳和微秒数
        // usec是一个时间单位，是0.000001s的意思，即1微秒，usec 即μs， microsecond (μs)，在程序里不能有“μ” 所以用"u"来替代
        // explode 将字符串打散为数组，explode(string $separator, string $string, int $limit = PHP_INT_MAX): array
        // 如果设置了 limit 参数并且是正数，则返回的数组包含最多 limit 个元素，而最后那个元素将包含 string 的剩余部分
		return ((float)$usec + (float)$sec); // 1661260605.5695 转换成浮点数
	}
}
if (!function_exists('sortByKey')) {
/**
 * Sorts given $array by key $sortby.
 *
 * @param array $array Array to sort
 * @param string $sortby Sort by this key
 * @param string $order  Sort order asc/desc (ascending or descending).
 * @param integer $type Type of sorting to perform
 * @return mixed Sorted array
 */
	function sortByKey(&$array, $sortby, $order = 'asc', $type = SORT_NUMERIC) {
		if (!is_array($array)) {
			return null;
		}

		foreach ($array as $key => $val) {
			$sa[$key] = $val[$sortby];
		}
        // 对二维数组按指定字段排序，先将指定的字段抽出来封装成一个一维数组，然后对这个一维数组进行 系统函数调用排序
        // 然后循环这个排好序的数组，然后将愿数组映射到这个封装即可
/*
sort() | 对数组进行升序排列
rsort() | 对数组进行降序排列
asort() | 根据关联数组的值，对数组进行升序排列
arsort() | 根据关联数组的值，对数组进行降序排列
ksort() | 根据关联数组的键，对数组进行升序排列
krsort() | 根据关联数组的键，对数组进行降序排列
*/
		if ($order == 'asc') {
			asort($sa, $type);  // 根据值升序排序 并保持索引关系, 第二个参数控制比较的类型，SORT_NUMERIC  单元被作为数字来比较；SORT_STRING  单元被作为字符串来比较
		} else {
			arsort($sa, $type); // 根据值降序排序
		}

		foreach ($sa as $key => $val) {
			$out[] = $array[$key];
		}
		return $out;
	}
}
if (!function_exists('array_combine')) {
/**
 * Combines given identical arrays by using the first array's values as keys,
 * and the second one's values as values. (Implemented for backwards compatibility with PHP4)
 * 合并两数组，以第一个数组的值为键，以第二个数组的值为值。两数组长度必须相等且大于零
 * combines 组合 合并
 *
 * @param array $a1 Array to use for keys
 * @param array $a2 Array to use for values
 * @return mixed Outputs either combined array or false.
 */
	function array_combine($a1, $a2) {
		$a1 = array_values($a1);
		$a2 = array_values($a2);
		$c1 = count($a1);
		$c2 = count($a2);

		if ($c1 != $c2) {
			return false;
		}
		if ($c1 <= 0) {
			return false;
		}
        // 长度必须相等且大于0
		$output = array();

		for ($i = 0; $i < $c1; $i++) {
			$output[$a1[$i]] = $a2[$i];
		}
		return $output;
	}
}
/**
 * Convenience method for htmlspecialchars.
 * 对 htmlspecialchars() 函数做一个封装使其更简便运用
 *
 * @param string $text Text to wrap through htmlspecialchars
 * @param string $charset Character set to use when escaping.  Defaults to config value in 'App.encoding' or 'UTF-8'
 * @return string Wrapped text
 * @link http://book.cakephp.org/view/703/h
 */
	function h($text, $charset = null) {
		if (is_array($text)) {
            // 递归 + 回调，用的高级
			return array_map('h', $text); // 为数组的每个元素应用回调函数。在这里这个回调函数还会产生递归，但是不会是个死循环，
		}
        /*
        function cube($n)
        {
            return ($n * $n * $n);
        }
        $a = [1, 2, 3];
        $b = array_map('cube', $a);
        print_r($b);
        Array
        (
            [0] => 1
            [1] => 8
            [2] => 27
        )
        */
		if (empty($charset)) {
			$charset = Configure::read('App.encoding');
		}
		if (empty($charset)) {
			$charset = 'UTF-8';
		}
		return htmlspecialchars($text, ENT_QUOTES, $charset);
        //htmlspecialchars() 将特殊字符转换为 HTML 实体; 第二个参数 ENT_QUOTES 既转换双引号也转换单引号
        // $new = htmlspecialchars("<a href='test'>Test</a>", ENT_QUOTES);
        // echo $new; // &lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;  只对特殊字符进行转换。
	}

/*
$text = [
"<a href='test'>Test</a>",
"<a href='baidu.com'>百度</a>",
"<a href='weibo.com'>微博</a>",
];
var_dump(h($text));

array(3) {
[0]=>
string(45) "&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;"
[1]=>
string(52) "&lt;a href=&#039;baidu.com&#039;&gt;百度&lt;/a&gt;"
[2]=>
string(52) "&lt;a href=&#039;weibo.com&#039;&gt;微博&lt;/a&gt;"
}*/

/*
/**
 * Returns an array of all the given parameters.
 *
 * Example:
 * <code>
 * a('a', 'b')
 * </code>
 *
 * Would return:
 * <code>
 * array('a', 'b')
 * </code>
 *
 * @return array Array of given parameters
 * @link http://book.cakephp.org/view/694/a
 */
	function a() {
		$args = func_get_args(); // 如果一个函数系统调用的比较多，可以把它升级为自定义全局函数并简化函数名方便调用
        // 他返回的是最内层函数传进来的参数 也就是a()函数
        /*function b() {
            $args = a(); // 不能这么用，什么也接收不到，它接收的是a(1,2,3) 传入的参数。
            return $args;
        }
        var_dump(b(1,2,3));
        array(0) {

        }*/

		return $args;
	}
/**
 * Constructs associative array from pairs of arguments.
 * 从参数对 构造关联数组
 *
 * Example:
 * <code>
 * aa('a',1,'b',2,'c') 是偶数对才好才更有意义
 * </code>
 *
 * Would return:
 * <code>
 * array(3) {
 *  ["a"]=>
 *  int(1)
 *  ["b"]=>
 *  int(2)
 *  ["c"]=>
 *  NULL
 * }
 * </code>
 *
 * @return array Associative array
 * @link http://book.cakephp.org/view/695/aa
 */

// 这个方法很巧妙，利用现有的系统函数构造出很实用的 自定义函数
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
/**
 * Convenience method for echo().
 *
 * @param string $text String to echo
 * @link http://book.cakephp.org/view/700/e
 */
	function e($text) {
		echo $text;
	}
/**
 * Convenience method for strtolower().
 *
 * @param string $str String to lowercase
 * @return string Lowercased string
 * @link http://book.cakephp.org/view/705/low
 */
	function low($str) {
		return strtolower($str);
	}
/**
 * Convenience method for strtoupper().
 *
 * @param string $str String to uppercase
 * @return string Uppercased string
 * @link http://book.cakephp.org/view/710/up
 */
	function up($str) {
		return strtoupper($str);
	}
/**
 * Convenience method for str_replace().
 *
 * @param string $search String to be replaced
 * @param string $replace String to insert
 * @param string $subject String to search
 * @return string Replaced string
 * @link http://book.cakephp.org/view/708/r
 */
	function r($search, $replace, $subject) {
		return str_replace($search, $replace, $subject);
	}
/**
 * Print_r convenience function, which prints out <PRE> tags around
 * the output of given array. Similar to debug().
 *
 * @see	debug()
 * @param array $var Variable to print out
 * @param boolean $showFrom If set to true, the method prints from where the function was called
 * @link http://book.cakephp.org/view/707/pr
 */
	function pr($var) {
		if (Configure::read() > 0) {
			echo '<pre>';
			print_r($var);
			echo '</pre>';
		}
	}
/**
 * Display parameters.
 *
 * @param mixed $p Parameter as string or array
 * @return string
 */
	function params($p) {
		if (!is_array($p) || count($p) == 0) {
			return null;
		}
		if (is_array($p[0]) && count($p) == 1) {
			return $p[0];
		}
		return $p;
	}
/**
 * Merge a group of arrays
 *
 * @param array First array
 * @param array Second array
 * @param array Third array
 * @param array Etc...
 * @return array All array parameters merged into one
 * @link http://book.cakephp.org/view/696/am
 */
	function am() {
		$r = array();
		$args = func_get_args();
		foreach ($args as $a) {
			if (!is_array($a)) {
				$a = array($a);
			}
			$r = array_merge($r, $a);
		}
		return $r;
	}
/**
 * Gets an environment variable from available sources, and provides emulation
 * for unsupported or inconsistent environment variables (i.e. DOCUMENT_ROOT on
 * IIS, or SCRIPT_NAME in CGI mode).  Also exposes some additional custom
 * environment information.
 *
 * @param  string $key Environment variable name.
 * @return string Environment variable setting.
 * @link http://book.cakephp.org/view/701/env
 */
	function env($key) {
		if ($key == 'HTTPS') {
			if (isset($_SERVER) && !empty($_SERVER)) {
				return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
			}
			return (strpos(env('SCRIPT_URI'), 'https://') === 0);
		}

		if ($key == 'SCRIPT_NAME') {
			if (env('CGI_MODE') && isset($_ENV['SCRIPT_URL'])) {
				$key = 'SCRIPT_URL';
			}
		}

		$val = null;
		if (isset($_SERVER[$key])) {
			$val = $_SERVER[$key];
		} elseif (isset($_ENV[$key])) {
			$val = $_ENV[$key];
		} elseif (getenv($key) !== false) {
			$val = getenv($key);
		}

		if ($key === 'REMOTE_ADDR' && $val === env('SERVER_ADDR')) {
			$addr = env('HTTP_PC_REMOTE_ADDR');
			if ($addr !== null) {
				$val = $addr;
			}
		}

		if ($val !== null) {
			return $val;
		}

		switch ($key) {
			case 'SCRIPT_FILENAME':
				if (defined('SERVER_IIS') && SERVER_IIS === true) {
					return str_replace('\\\\', '\\', env('PATH_TRANSLATED'));
				}
			break;
			case 'DOCUMENT_ROOT':
				$name = env('SCRIPT_NAME');
				$filename = env('SCRIPT_FILENAME');
				$offset = 0;
				if (!strpos($name, '.php')) {
					$offset = 4;
				}
				return substr($filename, 0, strlen($filename) - (strlen($name) + $offset));
			break;
			case 'PHP_SELF':
				return str_replace(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
			break;
			case 'CGI_MODE':
				return (PHP_SAPI === 'cgi');
			break;
			case 'HTTP_BASE':
				$host = env('HTTP_HOST');
				if (substr_count($host, '.') !== 1) {
					return preg_replace('/^([^.])*/i', null, env('HTTP_HOST'));
				}
			return '.' . $host;
			break;
		}
		return null;
	}
if (!function_exists('file_put_contents')) {
/**
 * Writes data into file.
 *
 * If file exists, it will be overwritten. If data is an array, it will be join()ed with an empty string.
 *
 * @param string $fileName File name.
 * @param mixed  $data String or array.
 * @return boolean Success
 */
	function file_put_contents($fileName, $data) {
		if (is_array($data)) {
			$data = join('', $data);
		}
		$res = @fopen($fileName, 'w+b');

		if ($res) {
			$write = @fwrite($res, $data);
			if ($write === false) {
				return false;
			} else {
				@fclose($res);
				return $write;
			}
		}
		return false;
	}
}
/**
 * Reads/writes temporary data to cache files or session.
 *
 * @param  string $path	File path within /tmp to save the file.
 * @param  mixed  $data	The data to save to the temporary file.
 * @param  mixed  $expires A valid strtotime string when the data expires.
 * @param  string $target  The target of the cached data; either 'cache' or 'public'.
 * @return mixed  The contents of the temporary file.
 * @deprecated Please use Cache::write() instead
 */
	function cache($path, $data = null, $expires = '+1 day', $target = 'cache') {
		if (Configure::read('Cache.disable')) {
			return null;
		}
		$now = time();

		if (!is_numeric($expires)) {
			$expires = strtotime($expires, $now);
		}

		switch (low($target)) {
			case 'cache':
				$filename = CACHE . $path;
			break;
			case 'public':
				$filename = WWW_ROOT . $path;
			break;
			case 'tmp':
				$filename = TMP . $path;
			break;
		}
		$timediff = $expires - $now;
		$filetime = false;

		if (file_exists($filename)) {
			$filetime = @filemtime($filename);
		}

		if ($data === null) {
			if (file_exists($filename) && $filetime !== false) {
				if ($filetime + $timediff < $now) {
					@unlink($filename);
				} else {
					$data = @file_get_contents($filename);
				}
			}
		} elseif (is_writable(dirname($filename))) {
			@file_put_contents($filename, $data);
		}
		return $data;
	}
/**
 * Used to delete files in the cache directories, or clear contents of cache directories
 *
 * @param mixed $params As String name to be searched for deletion, if name is a directory all files in directory will be deleted.
 *              If array, names to be searched for deletion.
 *              If clearCache() without params, all files in app/tmp/cache/views will be deleted
 *
 * @param string $type Directory in tmp/cache defaults to view directory
 * @param string $ext The file extension you are deleting
 * @return true if files found and deleted false otherwise
 */
	function clearCache($params = null, $type = 'views', $ext = '.php') {
		if (is_string($params) || $params === null) {
			$params = preg_replace('/\/\//', '/', $params);
			$cache = CACHE . $type . DS . $params;

			if (is_file($cache . $ext)) {
				@unlink($cache . $ext);
				return true;
			} elseif (is_dir($cache)) {
				$files = glob($cache . '*');

				if ($files === false) {
					return false;
				}

				foreach ($files as $file) {
					if (is_file($file)) {
						@unlink($file);
					}
				}
				return true;
			} else {
				$cache = array(
					CACHE . $type . DS . '*' . $params . $ext,
					CACHE . $type . DS . '*' . $params . '_*' . $ext
				);
				$files = array();
				while ($search = array_shift($cache)) {
					$results = glob($search);
					if ($results !== false) {
						$files = array_merge($files, $results);
					}
				}
				if (empty($files)) {
					return false;
				}
				foreach ($files as $file) {
					if (is_file($file)) {
						@unlink($file);
					}
				}
				return true;
			}
		} elseif (is_array($params)) {
			foreach ($params as $file) {
				clearCache($file, $type, $ext);
			}
			return true;
		}
		return false;
	}
/**
 * Recursively strips slashes from all values in an array
 *
 * @param array $values Array of values to strip slashes
 * @return mixed What is returned from calling stripslashes
 * @link http://book.cakephp.org/view/709/stripslashes_deep
 */
	function stripslashes_deep($values) {
		if (is_array($values)) {
			foreach ($values as $key => $value) {
				$values[$key] = stripslashes_deep($value);
			}
		} else {
			$values = stripslashes($values);
		}
		return $values;
	}
/**
 * Returns a translated string if one is found; Otherwise, the submitted message.
 *
 * @param string $singular Text to translate
 * @param boolean $return Set to true to return translated string, or false to echo
 * @return mixed translated string if $return is false string will be echoed
 * @link http://book.cakephp.org/view/693/__
 */
	function __($singular, $return = false) {
		if (!$singular) {
			return;
		}
		if (!class_exists('I18n')) {
			App::import('Core', 'i18n');
		}

		if ($return === false) {
			echo I18n::translate($singular);
		} else {
			return I18n::translate($singular);
		}
	}
/**
 * Returns correct plural form of message identified by $singular and $plural for count $count.
 * Some languages have more than one form for plural messages dependent on the count.
 *
 * @param string $singular Singular text to translate
 * @param string $plural Plural text
 * @param integer $count Count
 * @param boolean $return true to return, false to echo
 * @return mixed plural form of translated string if $return is false string will be echoed
 */
	function __n($singular, $plural, $count, $return = false) {
		if (!$singular) {
			return;
		}
		if (!class_exists('I18n')) {
			App::import('Core', 'i18n');
		}

		if ($return === false) {
			echo I18n::translate($singular, $plural, null, 5, $count);
		} else {
			return I18n::translate($singular, $plural, null, 5, $count);
		}
	}
/**
 * Allows you to override the current domain for a single message lookup.
 *
 * @param string $domain Domain
 * @param string $msg String to translate
 * @param string $return true to return, false to echo
 * @return translated string if $return is false string will be echoed
 */
	function __d($domain, $msg, $return = false) {
		if (!$msg) {
			return;
		}
		if (!class_exists('I18n')) {
			App::import('Core', 'i18n');
		}

		if ($return === false) {
			echo I18n::translate($msg, null, $domain);
		} else {
			return I18n::translate($msg, null, $domain);
		}
	}
/**
 * Allows you to override the current domain for a single plural message lookup.
 * Returns correct plural form of message identified by $singular and $plural for count $count
 * from domain $domain.
 *
 * @param string $domain Domain
 * @param string $singular Singular string to translate
 * @param string $plural Plural
 * @param integer $count Count
 * @param boolean $return true to return, false to echo
 * @return plural form of translated string if $return is false string will be echoed
 */
	function __dn($domain, $singular, $plural, $count, $return = false) {
		if (!$singular) {
			return;
		}
		if (!class_exists('I18n')) {
			App::import('Core', 'i18n');
		}

		if ($return === false) {
			echo I18n::translate($singular, $plural, $domain, 5, $count);
		} else {
			return I18n::translate($singular, $plural, $domain, 5, $count);
		}
	}
/**
 * Allows you to override the current domain for a single message lookup.
 * It also allows you to specify a category.
 *
 * The category argument allows a specific category of the locale settings to be used for fetching a message.
 * Valid categories are: LC_CTYPE, LC_NUMERIC, LC_TIME, LC_COLLATE, LC_MONETARY, LC_MESSAGES and LC_ALL.
 *
 * Note that the category must be specified with a numeric value, instead of the constant name.  The values are:
 * LC_CTYPE     0
 * LC_NUMERIC   1
 * LC_TIME      2
 * LC_COLLATE   3
 * LC_MONETARY  4
 * LC_MESSAGES  5
 * LC_ALL       6
 *
 * @param string $domain Domain
 * @param string $msg Message to translate
 * @param integer $category Category
 * @param boolean $return true to return, false to echo
 * @return translated string if $return is false string will be echoed
 */
	function __dc($domain, $msg, $category, $return = false) {
		if (!$msg) {
			return;
		}
		if (!class_exists('I18n')) {
			App::import('Core', 'i18n');
		}

		if ($return === false) {
			echo I18n::translate($msg, null, $domain, $category);
		} else {
			return I18n::translate($msg, null, $domain, $category);
		}
	}
/**
 * Allows you to override the current domain for a single plural message lookup.
 * It also allows you to specify a category.
 * Returns correct plural form of message identified by $singular and $plural for count $count
 * from domain $domain.
 *
 * The category argument allows a specific category of the locale settings to be used for fetching a message.
 * Valid categories are: LC_CTYPE, LC_NUMERIC, LC_TIME, LC_COLLATE, LC_MONETARY, LC_MESSAGES and LC_ALL.
 *
 * Note that the category must be specified with a numeric value, instead of the constant name.  The values are:
 * LC_CTYPE     0
 * LC_NUMERIC   1
 * LC_TIME      2
 * LC_COLLATE   3
 * LC_MONETARY  4
 * LC_MESSAGES  5
 * LC_ALL       6
 *
 * @param string $domain Domain
 * @param string $singular Singular string to translate
 * @param string $plural Plural
 * @param integer $count Count
 * @param integer $category Category
 * @param boolean $return true to return, false to echo
 * @return plural form of translated string if $return is false string will be echoed
 */
	function __dcn($domain, $singular, $plural, $count, $category, $return = false) {
		if (!$singular) {
			return;
		}
		if (!class_exists('I18n')) {
			App::import('Core', 'i18n');
		}

		if ($return === false) {
			echo I18n::translate($singular, $plural, $domain, $category, $count);
		} else {
			return I18n::translate($singular, $plural, $domain, $category, $count);
		}
	}
/**
 * The category argument allows a specific category of the locale settings to be used for fetching a message.
 * Valid categories are: LC_CTYPE, LC_NUMERIC, LC_TIME, LC_COLLATE, LC_MONETARY, LC_MESSAGES and LC_ALL.
 *
 * Note that the category must be specified with a numeric value, instead of the constant name.  The values are:
 * LC_CTYPE     0
 * LC_NUMERIC   1
 * LC_TIME      2
 * LC_COLLATE   3
 * LC_MONETARY  4
 * LC_MESSAGES  5
 * LC_ALL       6
 *
 * @param string $msg String to translate
 * @param integer $category Category
 * @param string $return true to return, false to echo
 * @return translated string if $return is false string will be echoed
 */
	function __c($msg, $category, $return = false) {
		if (!$msg) {
			return;
		}
		if (!class_exists('I18n')) {
			App::import('Core', 'i18n');
		}

		if ($return === false) {
			echo I18n::translate($msg, null, null, $category);
		} else {
			return I18n::translate($msg, null, null, $category);
		}
	}
/**
 * Computes the difference of arrays using keys for comparison.
 *
 * @param array First array
 * @param array Second array
 * @return array Array with different keys
 */
	if (!function_exists('array_diff_key')) {
		function array_diff_key() {
			$valuesDiff = array();

			$argc = func_num_args();
			if ($argc < 2) {
				return false;
			}

			$args = func_get_args();
			foreach ($args as $param) {
				if (!is_array($param)) {
					return false;
				}
			}

			foreach ($args[0] as $valueKey => $valueData) {
				for ($i = 1; $i < $argc; $i++) {
					if (isset($args[$i][$valueKey])) {
						continue 2;
					}
				}
				$valuesDiff[$valueKey] = $valueData;
			}
			return $valuesDiff;
		}
	}
/**
 * Computes the intersection of arrays using keys for comparison
 *
 * @param array First array
 * @param array Second array
 * @return array Array with interesected keys
 */
	if (!function_exists('array_intersect_key')) {
		function array_intersect_key($arr1, $arr2) {
			$res = array();
			foreach ($arr1 as $key => $value) {
				if (isset($arr2[$key])) {
					$res[$key] = $arr1[$key];
				}
			}
			return $res;
		}
	}
/**
 * Shortcut to Log::write.
 *
 * @param string $message Message to write to log
 */
	function LogError($message) {
		if (!class_exists('CakeLog')) {
			App::import('Core', 'CakeLog');
		}
		$bad = array("\n", "\r", "\t");
		$good = ' ';
		CakeLog::write('error', str_replace($bad, $good, $message));
	}
/**
 * Searches include path for files.
 *
 * @param string $file File to look for
 * @return Full path to file if exists, otherwise false
 * @link http://book.cakephp.org/view/702/fileExistsInPath
 */
	function fileExistsInPath($file) {
		$paths = explode(PATH_SEPARATOR, ini_get('include_path'));
		foreach ($paths as $path) {
			$fullPath = $path . DS . $file;

			if (file_exists($fullPath)) {
				return $fullPath;
			} elseif (file_exists($file)) {
				return $file;
			}
		}
		return false;
	}
/**
 * Convert forward slashes to underscores and removes first and last underscores in a string
 *
 * @param string String to convert
 * @return string with underscore remove from start and end of string
 * @link http://book.cakephp.org/view/697/convertSlash
 */
	function convertSlash($string) {
		$string = trim($string, '/');
		$string = preg_replace('/\/\//', '/', $string);
		$string = str_replace('/', '_', $string);
		return $string;
	}
/**
 * Implements http_build_query for PHP4.
 *
 * @param string $data Data to set in query string
 * @param string $prefix If numeric indices, prepend this to index for elements in base array.
 * @param string $argSep String used to separate arguments
 * @param string $baseKey Base key
 * @return string URL encoded query string
 * @see http://php.net/http_build_query
 */
	if (!function_exists('http_build_query')) {
		function http_build_query($data, $prefix = null, $argSep = null, $baseKey = null) {
			if (empty($argSep)) {
				$argSep = ini_get('arg_separator.output');
			}
			if (is_object($data)) {
				$data = get_object_vars($data);
			}
			$out = array();

			foreach ((array)$data as $key => $v) {
				if (is_numeric($key) && !empty($prefix)) {
					$key = $prefix . $key;
				}
				$key = urlencode($key);

				if (!empty($baseKey)) {
					$key = $baseKey . '[' . $key . ']';
				}

				if (is_array($v) || is_object($v)) {
					$out[] = http_build_query($v, $prefix, $argSep, $key);
				} else {
					$out[] = $key . '=' . urlencode($v);
				}
			}
			return implode($argSep, $out);
		}
	}
/**
 * Wraps ternary operations. If $condition is a non-empty value, $val1 is returned, otherwise $val2.
 * Don't use for isset() conditions, or wrap your variable with @ operator:
 * Example:
 * <code>
 * ife(isset($variable), @$variable, 'default');
 * </code>
 *
 * @param mixed $condition Conditional expression
 * @param mixed $val1 Value to return in case condition matches
 * @param mixed $val2 Value to return if condition doesn't match
 * @return mixed $val1 or $val2, depending on whether $condition evaluates to a non-empty expression.
 * @link http://book.cakephp.org/view/704/ife
 */
	function ife($condition, $val1 = null, $val2 = null) {
		if (!empty($condition)) {
			return $val1;
		}
		return $val2;
	}
?>