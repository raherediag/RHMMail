<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Classes\misc;

use App\Classes\misc\base2base_class as cB2B;
use Illuminate\Support\Facades\Facade;

/**
 * Description of helper_class
 *
 * @author Admin
 */
class helper_class extends Facade {

    protected static function getFacadeAccessor() {
        return 'helper';
    }

    public static function price_compact($number) {
        $result = "";
        if ($number < 10000) {
            $result = "$" . number_format($number);
        } else if ($number < 1000000) {
            $number = $number / 1000;
            $result = "$" . round($number, 1) . "K";
        } else if ($number >= 1000000) {
            $number = $number / 1000000;
            $result = "$" . round($number, 1) . "M";
        }
        return $result;
    }

    public static function formatDateDiff($start, $end = null) {
        if (!($start instanceof \DateTime)) {
            $start = new \DateTime($start);
        }

        if ($end === null) {
            $end = new \DateTime();
        }

        if (!($end instanceof \DateTime)) {
            $end = new \DateTime($start);
        }

        $doPlural = function ($nb, $str) {  // adds plurals
            return ($nb > 1) ? $str . 's' : $str;
        };
        $interval = $end->diff($start);

        $format = array();
        if ($interval->y > 0) { // december 8,2011
            return $start->format('M j, Y');
        }
        if ($interval->m > 0 || $interval->d > 13) {// december 8
            return $start->format('M j');
        }

        if ($interval->d !== 0) {
            $format[] = "%d " . $doPlural($interval->d, "day");
        }
        if ($interval->h !== 0) {
            $format[] = "%h " . $doPlural($interval->h, "hour");
        }
        if ($interval->i !== 0) {
            $format[] = "%i " . $doPlural($interval->i, "minute");
        }
        if ($interval->s !== 0) {
            if (!count($format)) {
                return "now";
            } else {
                $format[] = "%s " . $doPlural($interval->s, "second");
            }
        }


        // We use the two biggest parts
        if (count($format) > 1) {
            $format = array_shift($format) . " ago";
        } else {
            $format = array_pop($format);
        }

        return $interval->format($format);
    }

    public static function randomCode($length = 6) {
        $str = "0";
        if ($length > 0) {
            for ($i = 0; $i < $length; $i++) {
                if ($i == 0) {
                    $str .= rand(1, 9);
                } else {
                    $str .= rand(0, 9);
                }
            }
        }
        return intval($str);
    }

    public static function detectDevice() {
        $userAgent = $_SERVER["HTTP_USER_AGENT"];
        $devicesTypes = array(
            "desktop" => array("msie 10", "msie 9", "msie 8", "windows.*firefox", "windows.*chrome", "x11.*chrome", "x11.*firefox", "macintosh.*chrome", "macintosh.*firefox", "opera"),
            "tablet" => array("tablet", "android", "ipad", "tablet.*firefox"),
            "mobile" => array("mobile ", "android.*mobile", "iphone", "ipod", "opera mobi", "opera mini"),
            "bot" => array("googlebot", "mediapartners-google", "adsbot-google", "duckduckbot", "msnbot", "bingbot", "ask", "facebook", "yahoo", "addthis")
        );
        $deviceName = 'unknown';
        foreach ($devicesTypes as $deviceType => $devices) {
            foreach ($devices as $device) {
                if (preg_match("/" . $device . "/i", $userAgent)) {
                    $deviceName = $deviceType;
                }
            }
        }
        return ucfirst($deviceName);
    }

    public static function ipAddress() {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function getClientInfo() {
        $response = json_decode(self::apiCall('http://ipcenter.info/', ['ip' => self::ipAddress(), 'user_agent' => $_SERVER["HTTP_USER_AGENT"]], 'GET'), true);
        $response['device'] = self::detectDevice();
        $response['user_agent'] = $_SERVER["HTTP_USER_AGENT"];
        return $response;
    }

    public static function apiCall($url, $data = [], $method = "POST", $headers = []) {
        $curl = curl_init();

        switch (strtoupper($method)) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if (is_array($data) && count($data) > 0) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                }
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                $headers[] = 'Connection: close;';
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                $headers[] = 'Connection: close;';
                break;
            default:
                if ($data) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        return curl_exec($curl);
    }

    public static function base2base($iNum, $iBase, $oBase, $iScale = 0) {
        $base2base = new cB2B();
        return $base2base->base_base2base($iNum, $iBase, $oBase, $iScale);
    }

    public static function isAssoc($arr) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public static function rate2group($rate) {
        $res = 'tin';
        if ($rate <= 24.9)
            $res = 'platinum';
        else if ($rate <= 29.9)
            $res = 'gold';
        else if ($rate <= 34.9)
            $res = 'silver';
        return $res;
    }

    public static function generateVideoThumbnail($video_path, $thumb_output_dir) {
        if (File::exists($video_path)) {
            $filename = File::name($video_path);
            $filename .= '.jpg';
            $thm_path = $thumb_output_dir . 'thm_' . $filename;
            if (!File::exists($thm_path)) {
                Log::info($thm_path);
                Flavy::thumbnail($video_path, $thm_path, 1);
                return $thm_path;
            }
        }
        return false;
    }

    public static function getLocalMediaPath($file_path) {
        $path = str_replace('storage/', 'app/public/', $file_path);
        return storage_path($path);
    }

    public static function score2fico($score) {
        return ($score * 550) + 300;
    }

    public static function fico2score($fico) {
        return ($fico - 300 ) / 550;
    }

    public static function scores2rate($base, $social, $fico) {
        return 19.9 + (15 - 15 * ((($base * 0.6 + $social * 0.4) * 0.75 + (($fico - 300) / 550) * 0.25) - 0.59) * 2.4390);
    }

    public static function getJWTUser() {
        try {
            if (!$user = \JWTAuth::parseToken()->authenticate()->toArray()) {
                return [];
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return [];
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return [];
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return [];
        }
        return $user;
    }

    public static function checkJWTUser() {
        $user = self::getJWTUser();
        if (is_array($user) && count($user) > 0) {
            return true;
        }
        return false;
    }

    public static function literalBoolean(bool $boolean) {
        if ($boolean) {
            return 'Yes';
        }
        return 'No';
    }

    public static function keygen($n, $l = 16) {
        $str = decbin($n);
        if (strlen($str) < $l) {
            $str = str_repeat("A", $l - strlen($str)) . $str;
        } else {
            $str = substr($str, 0, $l);
        }
        return $str;
    }

    public static function encode($str, $id) {
        $crypt = new \Illuminate\Encryption\Encrypter(self::keygen($id));
        return $crypt->encrypt($str);
    }

    public static function decode($str, $id) {
        $crypt = new \Illuminate\Encryption\Encrypter(self::keygen($id));
        try {
            return $crypt->decrypt($str);
        } catch (\Exception $e) {
            return "invalid key";
        }
    }

    public static function phoneFormat($number) {
        return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '+1 $1-$2-$3', $number);
    }

    public static function fillable($data, $fillable) {
        return array_intersect_key(array_merge($fillable, $data), $fillable);
    }

    public static function getHeaders($keys = []) {
        $headers = [];
        if(is_array($keys) && count($keys)){
            foreach ($keys as $key) {
                $headers[preg_replace('/(_|-|\s+)/', '-', strtolower($key))] = self::getHeader($key);
            }
        }else{
            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) <> 'HTTP_') {
                    continue;
                }
                $header = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    public static function getHeader($key = '') {
        if ($key == '') {
            return '';
        }
        $header = 'HTTP_' . preg_replace('/(_|-|\s+)/', '_', strtoupper($key));
        return self::getValue($_SERVER, $header);
    }

    public static function getValue($array, $key, $return = '') {
        $keys = explode('.', $key);
        $count_keys = count($keys);
        if($count_keys > 1){
            if (isset($array[$keys[0]])) {
                $n_key = '';
                for($i = 1; $i < $count_keys; $i++){
                    $n_key .= $keys[$i] . '.';
                }
                return self::getValue($array[$keys[0]], rtrim($n_key,'.'), $return);
            }
        }elseif ($count_keys > 0) {
            if (isset($array[$keys[0]]) && $array[$keys[0]] != '') {
                return $array[$keys[0]];
            }
        }

        return $return;

    }

    public static function objectToArray($d) {
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            /*
            * Return array converted to object
            * Using __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return array_map(__FUNCTION__, $d);
        }
        else {
            // Return array
            return $d;
        }
    }

    public static function arrayToObject($d) {
        if (is_array($d)) {
            /*
            * Return array converted to object
            * Using __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return (object) array_map(__FUNCTION__, $d);
        }
        else {
            // Return object
            return $d;
        }
    }

}
