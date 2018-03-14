<?php
    class HTTPCache {
        public static function register($time = 3600) {
            if($time) {
                $expires = json_decode($_COOKIE['expire'], true) ?? [];
                $ru = $_SERVER['REQUEST_URI'].':'.json_encode($_POST);
                $expires[$ru] = $expires[$ru] > time() ? $expires[$ru] : time() + $time;
                setcookie('expire', json_encode($expires), time() + 86400 * 365, '/');
            }
        }
        
        public static function check() {
            $headers = apache_request_headers();
            $expires = json_decode($_COOKIE['expire'], true) ?? [];
            $ru = $_SERVER['REQUEST_URI'].':'.json_encode($_POST);
            if(!$expires[$ru]) return;
            $ts = gmdate('D, d M Y H:i:s ', max(time(), $expires[$ru])) . 'GMT';
            $if_modified_since = isset($headers['If-Modified-Since']) ? $headers['If-Modified-Since'] : false;
            if ($if_modified_since && $if_modified_since == $ts) {
                header('HTTP/1.1 304 Not Modified');
                exit();
            } else {
                header('Last-Modified: ' . $ts);
            }
        }
    }
    HTTPCache::check();
?>