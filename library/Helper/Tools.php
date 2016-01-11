<?php
/**
 * 工具类;
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/6
 * Time: 16:35
 */

namespace Helper;


use eYaf\Cache;
use eYaf\Db;
use eYaf\Logger;
use Yaf\Application;
use Yaf\Exception;
use Yaf\Loader;
use Yaf\Registry;

class Tools
{
    public static function getConfig($name = null, $default = null)
    {
        static $config;
        if (!is_object($config)) {
            $config = call_user_func(Registry::get('config'));
            if (is_object($config) && Registry::has('config')) {
                Registry::del('config');
            }
        }
        if (empty($name)) {
            return $config;
        }
        if ($config->valid($name)) {
            return $config->get($name);
        }
        return $default;
    }

    /**
     * 缓存管理
     * @param mixed $name 缓存配置名称
     * @param bool $flag
     * @return mixed
     */
    public static function cache($name = '', $flag = false)
    {
        if (empty($name)) {
            $name = '_cache';
        }

        if (is_array($name)) {
            $config = (array)$name + array('adapter' => 'File', 'params' => array());
            return Cache::factory($config);
        }

        if ($flag && ($cache = Registry::get($name))) return $cache;

        $config = static::getConfig($name)->toArray() + array('adapter' => 'File', 'params' => array());
        $cache = Cache::factory($config);
        Registry::set($name, $cache);
        return $cache;
    }

    public static function db($name = '', $flag = false)
    {
        if (empty($name)) {
            $name = '_db';
        }
        if (is_array($name)) {
            $config = (array)$name + array('adapter' => 'Pdo\Mysql', 'params' => array());
            return Db::factory($config);
        }

        if ($flag && $db = Registry::get($name)) return $db;

        $config = static::getConfig($name)->toArray() + array('adapter' => 'Pdo\Mysql', 'params' => array());
        $db = Db::factory($config);
        Registry::set($name, $db);
        return $db;
    }


    public static function model($name = null, $dir = null)
    {
        if (empty($name)) $name = Application::app()->getDispatcher()->getRequest()->getControllerName();
        $class = ucfirst($name) . 'Model';
        if (null === $dir) {
            return new $class();
        }
        $file = rtrim($dir, '/') . '/' . $name . '.php';

        if (is_file($file) && Loader::import($file)) {
            return new $class();
        }

        throw new Exception("Can't load model '{$class}' from '{$dir}'");
    }


    //如果是调试debug=1继续执行
    public static function echoPrint($str, $is_debug = 1)
    {
        echo $str . '<br />';
        if (!$is_debug) {
            exit();
        }
    }

    /**
     *
     * url GET请求方法
     * @param str $url
     * @param bool $isPost
     * @return bool|mixed
     */
    public static function requestUrl($url, $isPost = false, $timeout = 10)
    {
        $curl = curl_init();
        if ($isPost) {
            $paramArr = explode('?', $url);
            if (empty ($paramArr))
                return false;
            $url = $paramArr [0];
            $dataArr = explode('&', $paramArr [1]);
            if (empty ($dataArr))
                return false;
            $postData = array();
            foreach ($dataArr as $val) {
                $tmp = explode('=', $val);
                $postData [$tmp [0]] = $tmp [1];
            }
            curl_setopt($curl, CURLOPT_POST, 1); //是否是post传递
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData); //设置POST提交的字符串
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false); //是否设置header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上.
//        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);


        $output = curl_exec($curl);
        $info = curl_getinfo($curl);
        if ($output === false || $info ['http_code'] != 200) {
            return false;
        } else {
            return $output;
        }
    }

    /*
    * 截取字符串
    *
    * @param $string 要截取的字符串
    * @param $length 截取长度
    * @param $dot
    * @return 取得到的结果集
    */
    function cutstr($string, $length, $dot = ' ...', $charset = 'utf-8')
    {
        if (strlen($string) <= $length) {
            return $string;
        }

        $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);

        $strcut = '';
        if (strtolower($charset) == 'utf-8') {
            $n = $tn = $noc = 0;
            while ($n < strlen($string)) {

                $t = ord($string [$n]);
                if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                    $tn = 1;
                    $n++;
                    $noc++;
                } elseif (194 <= $t && $t <= 223) {
                    $tn = 2;
                    $n += 2;
                    $noc += 2;
                } elseif (224 <= $t && $t < 239) {
                    $tn = 3;
                    $n += 3;
                    $noc += 2;
                } elseif (240 <= $t && $t <= 247) {
                    $tn = 4;
                    $n += 4;
                    $noc += 2;
                } elseif (248 <= $t && $t <= 251) {
                    $tn = 5;
                    $n += 5;
                    $noc += 2;
                } elseif ($t == 252 || $t == 253) {
                    $tn = 6;
                    $n += 6;
                    $noc += 2;
                } else {
                    $n++;
                }

                if ($noc >= $length) {
                    break;
                }

            }
            if ($noc > $length) {
                $n -= $tn;
            }

            $strcut = substr($string, 0, $n);

        } else {
            for ($i = 0; $i < $length - 3; $i++) {
                $strcut .= ord($string [$i]) > 127 ? $string [$i] . $string [++$i] : $string [$i];
            }
        }

        $strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

        return $strcut . $dot;
    }

    /**
     *
     * 写日志文件
     * @param string $filename 日志文件名
     * @param string $loginfo 日志内容
     * @param string $path 路径
     *
     */
    public static function logfile($filename, $loginfo, $path = '/tmp/')
    {
        Logger::getLogger($filename, "a+", $path)
            ->log("[" . date("Ymd H:i:s") . "] " . preg_replace('/[\r\n]/', '', $loginfo) . "\r\n");
    }


    //得到当前用户Ip地址
    public static function getRealIp()
    {
        $pattern = '/(\d{1,3}\.){3}\d{1,3}/';
        if (isset ($_SERVER ["HTTP_X_FORWARDED_FOR"]) && preg_match_all($pattern, $_SERVER ['HTTP_X_FORWARDED_FOR'], $mat)) {
            foreach ($mat [0] as $ip) {
                //得到第一个非内网的IP地址
                if ((0 != strpos($ip, '192.168.')) && (0 != strpos($ip, '10.')) && (0 != strpos($ip, '172.16.'))) {
                    return $ip;
                }
            }
            return $ip;
        } else {
            if (isset ($_SERVER ["HTTP_CLIENT_IP"]) && preg_match($pattern, $_SERVER ["HTTP_CLIENT_IP"])) {
                return $_SERVER ["HTTP_CLIENT_IP"];
            } else {
                return $_SERVER ['REMOTE_ADDR'];
            }
        }
    }

    //得到无符号整数表示的ip地址
    public static function getIntIp()
    {
        return sprintf('%u', ip2long(self::getRealIp()));
    }

    /**
     * 按时间获取表
     * y、m、d |对应年月日
     * @param $table
     * @param string $order
     * @param int $l
     * @return string
     */
    public static function getTableByDate($table, $order = '', $l = 0)
    {
        if (empty($order)) {
            return $table;
        }
        if ($order == 'y') {
            $end = $l != 0 ? date('Y') + ($l) : date('Y');
            return $table . '_' . $end;
        }
        if ($order == 'm') {
            $end = $l != 0 ? date('Ym') + ($l) : date('Ym');
            return $table . '_' . $end;
        }
        if ($order == 'd') {
            $end = $l != 0 ? date('Ymd') + ($l) : date('Ymd');
            return $table . '_' . $end;
        }
    }

    /**
     * 生成随机数/字符串
     *
     * @param int $length 长度
     * @param boolean $numeric 是否为数字 false=字符串 true=数字
     * @return string
     */
    public static function random($length, $numeric = false)
    {
        mt_srand(( double )microtime() * 1000000);
        if (( boolean )$numeric) {
            $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
        } else {
            $hash = '';
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
            $max = strlen($chars) - 1;
            for ($i = 0; $i < $length; $i++) {
                $hash .= $chars [mt_rand(0, $max)];
            }
        }
        return $hash;
    }

    //根据账号分表算法
    public function calc_hash_tbl($u, $n = 20, $m = 16)
    {
        $h = sprintf("%u", crc32($u));
        $h1 = intval($h / $n);
        $h2 = $h1 % $n;
        return $h2;
    }

    /**
     * 根据PHP各种类型变量生成唯一标识号
     * @param mixed $mix 变量
     * @return string
     */
    public static function toGuidString($mix)
    {
        if (is_object($mix)) {
            return spl_object_hash($mix);
        } elseif (is_resource($mix)) {
            $mix = get_resource_type($mix) . strval($mix);
        } else {
            $mix = serialize($mix);
        }
        return md5($mix);
    }

}