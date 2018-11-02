<?php

// 函数库

/**
 * 获取请求类实例
 * @return boolean|\Top\Request
 */
function request() {
    static $instance = false;
    if (!$instance) {
        $instance = new \Top\Request();
    }
    return $instance;
}

/**
 * 拼接URL
 * @param string $url
 * @return string
 */
function u($url = '', $params = []) {
    $url = \Top\Url::build($url);
    $paramsString = implode(URL_DELIMIT, $params);
    if ($paramsString) {
        $paramsString = URL_DELIMIT . $paramsString;
    }
    return URL_DELIMIT . $url . $paramsString;
}

function p($params, $dump = false) {
    echo '<pre>';
    ($dump === true) ? var_dump($params) : print_r($params);
    echo '</pre>';
}

function addons($name, $params = []) {
    /*static $instance = [];
    if (!isset($instance[$name])) {
        $object = 'Addons\\' . $name . '\Index';
        $instance['name'] = new $object;
    }
    return $instance['name']->run($params);*/
    $object = 'Addons\\' . $name . '\Index';
    return \Top\Loader::get($object)->run($params);
}


/**
 * SESSION设置及获取
 * @param string $name
 * @param boolean $value
 * @return boolean|array|string
 */
function session($name = '', $value = false) {
    $flag = \Top\Config::get('session_flag');
    $flag = (!empty($flag)) ? $flag : 'top';
    if ($value === false) {
        if ($name) {
            return (isset($_SESSION[$flag][$name])) ? $_SESSION[$flag][$name] : false;
        } else {
            return (isset($_SESSION[$flag])) ? $_SESSION[$flag][$name] : false;
        }
    } else {
        return $_SESSION[$flag][$name] = $value;
    }
}

/**
 * 删除目录（包括子目录）
 * @param string $dirName
 */
function remove_dir($dirName) {
    if ($handle = @opendir($dirName)) {
        while (false !== ($item = readdir($handle))) {
            if ($item != "." && $item != "..") {
                if (is_dir($dirName . '/' . $item)) {
                    remove_dir($dirName . '/' . $item);
                } else {
                    unlink($dirName . '/' . $item);
                }
            }
        }
        closedir($handle);
        rmdir($dirName);
    }
}

/**
 * 过滤字符串
 * @param string $str
 * @return string
 */
function filter($str) {
    $replaceArr = array(
        "/select\b|insert\b|update\b|delete\b|drop\b|;|\"|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is"
    );
    $str = preg_replace($replaceArr, '', $str);
    $str = htmlspecialchars($str);
    return $str;
}

/**
 * 格式化时间
 * @param int $time
 * @return string
 */
function format_time($time) {
    $timer = $time;
    $diff = $_SERVER['REQUEST_TIME'] - $timer;
    $day = floor($diff / 86400);
    $free = $diff % 86400;
    if ($day > 0) {
        return date('Y-m-d H:i:s', $time);
    } else {
        if ($free > 0) {
            $hour = floor($free / 3600);
            $free = $free % 3600;
            if ($hour > 0) {
                return $hour . "小时前";
            } else {
                if ($free > 0) {
                    $min = floor($free / 60);
                    $free = $free % 60;
                    if ($min > 0) {
                        return $min . "分钟前";
                    } else {
                        if ($free > 0) {
                            return $free . "秒前";
                        } else {
                            return '刚刚';
                        }
                    }
                } else {
                    return '刚刚';
                }
            }
        } else {
            return '刚刚';
        }
    }
}

/**
 * CURL创建请求
 * @param string $url
 * @param array $data
 * @param array $header
 * @return mixed|boolean
 */
function curl($url, $data = [], $header = []) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, $header);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    $res = curl_exec($curl);
    curl_close($curl);
    if ($res) {
        return $res;
    }
    return false;
}

/**
 * 获取客户端IP
 * @param number $type
 * @param boolean $client
 * @return NULL|string|number|NULL
 */
function get_client_ip($type = 0, $client = true) {
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL)
        return $ip[$type];
    if ($client) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos)
                unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 修改图片大小
 * @param $imgSrc
 * @param $resize_width
 * @param $resize_height
 * @param string $newName
 * @param bool $isCut
 * @return string
 */
function resize_image($imgSrc, $resize_width, $resize_height, $newName = '', $isCut = false) {
    $imgSrc = '.' . $imgSrc;
    //图片的类型
    $type = substr(strrchr($imgSrc, "."), 1);
    //初始化图象
    if ($type == "jpg" || $type == 'JPG' || $type == 'jpeg' || $type == 'JPEG') {
        $im = imagecreatefromjpeg($imgSrc);
    }
    if ($type == "gif" || $type == "GIF") {
        $im = imagecreatefromgif($imgSrc);
    }
    if ($type == "png" || $type == "PNG") {
        $im = imagecreatefrompng($imgSrc);
    }
    //目标图象地址
    $dstimg = (!$newName) ? $imgSrc : $newName . '.' . $type;
    $width = imagesx($im);
    $height = imagesy($im);
    //生成图象
    //改变后的图象的比例
    $resize_ratio = ($resize_width) / ($resize_height);
    //实际图象的比例
    $ratio = ($width) / ($height);
    if (($isCut) == 1) { //裁图
        if ($ratio >= $resize_ratio) { //高度优先
            $newimg = imagecreatetruecolor($resize_width, $resize_height);
            imagecopyresampled($newimg, $im, 0, 0, 0, 0, $resize_width, $resize_height, (($height) * $resize_ratio), $height);
            ImageJpeg($newimg, $dstimg);
        }
        if ($ratio < $resize_ratio) { //宽度优先
            $newimg = imagecreatetruecolor($resize_width, $resize_height);
            imagecopyresampled($newimg, $im, 0, 0, 0, 0, $resize_width, $resize_height, $width, (($width) / $resize_ratio));
            ImageJpeg($newimg, $dstimg);
        }
    } else { //不裁图
        if ($ratio >= $resize_ratio) {
            $newimg = imagecreatetruecolor($resize_width, ($resize_width) / $ratio);
            imagecopyresampled($newimg, $im, 0, 0, 0, 0, $resize_width, ($resize_width) / $ratio, $width, $height);
            ImageJpeg($newimg, $dstimg);
        }
        if ($ratio < $resize_ratio) {
            $newimg = imagecreatetruecolor(($resize_height) * $ratio, $resize_height);
            imagecopyresampled($newimg, $im, 0, 0, 0, 0, ($resize_height) * $ratio, $resize_height, $width, $height);
            ImageJpeg($newimg, $dstimg);
        }
    }
    ImageDestroy($im);
    //imgturn($dstimg, 1);
    return ltrim($dstimg, '.');
}
