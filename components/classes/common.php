<?php
	class json {
		public static function OUT($json) {
			echo json_encode($json);
			exit;
		}

		public static function GET($identifier) {
			$path = $_SERVER['DOCUMENT_ROOT'].'/assets/json/'.$identifier.'.json';
			return file_exists($path) ? (json_decode(file_get_contents($path)) ? json_decode(file_get_contents($path), true) : false) : false;
		}

		public static function SET($identifier, $data) {
			if(!is_array($data) && !is_object($data) && !json_decode($data)) return false;
			if(is_array($data) || is_object($data)) $data = json_encode($data);
			file_put_contents($_SERVER['DOCUMENT_ROOT'].'/assets/json/'.$identifier.'.json', $data);
			return true;
		}
	}
	
	class API {
		public static $errors;

		public static function validate() {
			$rules = func_get_args();
			$arr = $rules[0];
			unset($rules[0]);
			if(is_array($rules[1]) && (count($rules[1] == 1) || is_array($rules[1][1]))) $rules = $rules[1];
			$errors = [];
			$attributes = [];
			foreach($rules as $rule) {
				if(!is_array($rule)) continue;
				if(!method_exists(__CLASS__, $rule[1])) continue;
				if(!call_user_func_array([__CLASS__, $rule[1]], [$rule, $arr])) {
					if(!isset($errors[$rule[0]])) $errors[$rule[0]] = [];
					$errors[$rule[0]][] = $rule[1];
					$attributes[$rule[0]] = $rule[0];
				}
			}

			if($errors) {
				self::$errors = ['json' => json_encode(['error' => ['error_msg' => 'Некоторые параметры были указаны неверно: '.implode($attributes), 'errors' => $errors]]), 'errors' => $errors];
				return false;
			} 
			self::$errors = [];
			return true;
		}

		public static function trim($var) {
			if(!$var) return $var;
			foreach($var as &$p) {
				if(is_string($p)) $p = trim($p);
			}
		}

		// Validators

		private static function required($attr, $arr) {
			if(!isset($arr[$attr[0]]) || strlen((string) $arr[$attr[0]]) == 0) {
				return false;
			}
			return true;
		}
		private static function string($attr, $arr) {
			if(isset($attr['min']) && mb_strlen($arr[$attr[0]]) < $attr['min']) return false;
            if(isset($attr['max']) && mb_strlen($arr[$attr[0]]) > $attr['max']) return false;
			return true;
		}

		private static function pattern($attr, $arr) {
			if(isset($arr[$attr[0]]) && !preg_match($attr[2], $arr[$attr[0]])) {
				return false;
			}
			return true;
		}
        
        private static function boolean($attr, $arr) {
            if(isset($arr[$attr[0]]) && !filter_var($arr[$attr[0]], FILTER_VALIDATE_BOOLEAN)) {
                return false;
            }
            return true;
        }
        
        private static function integer($attr, $arr) {
            if(isset($arr[$attr[0]]) && !filter_var($arr[$attr[0]], FILTER_VALIDATE_INTEGER)) {
                return false;
            }
            $arr[$attr[0]] = (int) $arr[$attr[0]];
            if(isset($attr['min']) && $arr[$attr[0]] < $attr['min']) return false;
            if(isset($attr['max']) && $arr[$attr[0]] > $attr['max']) return false;
            return true;
        }
        
        private static function float($attr, $arr) {
            if(isset($arr[$attr[0]]) && !filter_var($arr[$attr[0]], FILTER_VALIDATE_FLOAT)) {
                return false;
            }
            $arr[$attr[0]] = (int) $arr[$attr[0]];
            if(isset($attr['min']) && $arr[$attr[0]] < $attr['min']) return false;
            if(isset($attr['max']) && $arr[$attr[0]] > $attr['max']) return false;
            return true;
        }
        
        private static function ip($attr, $arr) {
            if(isset($arr[$attr[0]]) && !filter_var($arr[$attr[0]], FILTER_VALIDATE_IP)) {
                return false;
            }
            return true;
        }
        
        private static function url($attr, $arr) {
            if(isset($arr[$attr[0]]) && !filter_var($arr[$attr[0]], FILTER_VALIDATE_URL)) {
                return false;
            }
            return true;
        }
        
        private static function email($attr, $arr) {
            if(isset($arr[$attr[0]]) && !filter_var($arr[$attr[0]], FILTER_VALIDATE_EMAIL)) {
                return false;
            }
            return true;
        }
	}
?>