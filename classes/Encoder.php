<?php
	class Encoder {
		private static $iv = '*OVI/P39fopY0Z:.';
		public function encode($data, $salt = null) {
           	$salt = $salt ?? FPN::config()->cookieSalt;
			return openssl_encrypt(serialize($data), 'AES-256-CBC', $salt, false, self::$iv);
		}

		public function decode($data, $salt = null) {
			$salt = $salt ?? FPN::config()->cookieSalt;
			return unserialize(openssl_decrypt($data, 'AES-256-CBC', $salt, false, self::$iv));
		}
	}

	class ecookie {
		public static function get($name) {
			return isset($_COOKIE[$name]) ? FPN::encoder()->decode($_COOKIE[$name], FPN::config()->cookieSalt) : null;
		}

		public static function set($name, $data, $time) {
			return ($set = FPN::encoder()->encode($data, FPN::config()->cookieSalt)) == $set && setcookie($name, $set, time() + $time, '/') && $_COOKIE[$name] = $set;
		}
	}
?>