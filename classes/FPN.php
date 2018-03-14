<?php 
	class FPN {
		private static $config, $components, $encoder, $language, $db = [];

		private static $errno;

		private static $user;

		private static $defaultConfig = [
			'sitename' => '',
			'author' => '',
			'nav' => [],
			'components' => [],
			'translations' => [
				'dir' => '',
				'defaultLanguage' => ''
			],
			'cookieSoil' => 'CHANGE ME! ITS IMPORTANT'
		];

		public static function init() {
			if(defined('ENV_DEBUG') && ENV_DEBUG) {
				set_error_handler([__CLASS__, 'errorHandler']);
				set_exception_handler([__CLASS__, 'exceptionHandler']);
			}
			foreach(self::config()->components ?? [] as $component) {
				self::registerComponent($component);
			}

			handler::path();
		}
        
        public static function registerComponent($component) {
            if(is_file($_SERVER['DOCUMENT_ROOT'].'/components/'.$component)) {
                self::$components[] = $_SERVER['DOCUMENT_ROOT'].'/components/'.$component;
                ob_start();
                    include($_SERVER['DOCUMENT_ROOT'].'/components/'.$component);
                ob_end_clean();
            } else throw new Exception('Component doesn`t exists: ' . $_SERVER['DOCUMENT_ROOT'].'/components/'.$component);
        }
        public static function fileVersion($file, $type) {
        	$v = !FPN::isExternal($file) && is_file(Assets::getPath($file, $type)) ? '?v=' . filemtime(Assets::getPath($file, $type)) : '';
            return Assets::getPath($file, $type, false) . $v;
        }
        
        public static function headersList() {
            $hlist = headers_list();
            $ret = [];
            foreach($hlist as $h) {
                preg_match('/(.*?):\s(.*)/', $h, $r);
                $ret[mb_strtoupper($r[1])] = $r[2];
            }
            return $ret;
        }

        public static function get() {
        	return strtolower($_SERVER['REQUEST_METHOD']) == 'get';
        }

        public static function post() {
        	return strtolower($_SERVER['REQUEST_METHOD']) == 'post';
        }
        public static function put() {
        	return strtolower($_SERVER['REQUEST_METHOD']) == 'put';
        }
        public static function https() {
        	return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
        }
        public static function siteurl() {
        	return (FPN::https() ? 'https://' : 'http://') . (isset(FPN::config()->url) ? FPN::config()->url : $_SERVER['HTTP_HOST']);
        }
        
        public static function isExternal($lnk) {
            return preg_match('/^https?:\/\//i', $lnk);
        }

		public static function getDB($dbn = 0) {
			if(self::$db[$dbn]) return self::$db[$dbn];
			$db = self::$db[$dbn] = new QB('mysql:dbname='.($dbn ? $dbn : FPN::config()->db['dbname']).';host='.FPN::config()->db['host'].';port='.FPN::config()->db['port'] ?? 3306, FPN::config()->db['user'], FPN::config()->db['password']);
			$db->query('SET CHARSET \'utf8\'')->execute();
			$db->query('SET NAMES \'utf8\'')->execute();
			return $db;
		}

		public static function jsViewName() {
			return '<script id="self_destruct">VIEW_NAME = \'' . addslashes(handler::$view->_viewfile_) . '\'</script>';
		}
        
        public static function isMobile() {
            return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$_SERVER['HTTP_USER_AGENT']) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($_SERVER['HTTP_USER_AGENT'],0,4));
        }

		public static function &config() {
			return self::$config ?? self::$config = (object) array_replace_recursive(self::$defaultConfig, include($_SERVER['DOCUMENT_ROOT'] . '/configs/main.php') ?? []);
		}
        
        public static function setMeta(&$context, $value, $where) {
            $flag = false;
            if(!isset($context->meta)) $context->meta = [];
            
            foreach($context->meta as &$meta) {
                if(isset($meta[$where]) && $meta[$where] == $value[$where]) {
                    $meta = $value;
                    $flag = true;
                }
            }
            if(!$flag) {
                $context->meta[] = $value;
            }
            return true;
        }
        
        public static function meta($context, $type = '') {
            $ret = '';
            switch($type) {
                case 'css': {
                    foreach($context->styles ?? [] as $style) {
                        $ret .= '<link rel="stylesheet" href="'.htmlentities($style).'"'.$async.'>';
                    }
                    break;
                }
                   
                case 'ijs': {
                    foreach($context->iscripts ?? [] as $script) {
                        $ret .= '<script type="text/javascript" src="'.htmlentities($script).'"'.$async.'></script>';
                    }
                    break;
                }

                case 'js': {
                    foreach($context->scripts ?? [] as $script) {
                        $ret .= '<script type="text/javascript" src="'.htmlentities($script).'"'.$async.'></script>';
                    }
                    break;
                }
                    
                default: {
                    foreach($context->meta ?? [] as $meta) {
                        if(!is_array($meta)) $meta = [$meta];
                        foreach($meta as $k => $v) {
                            $meta[$k] = $k . '="' . htmlentities($v) . '"';
                        }
                        $meta = implode(' ', array_values($meta));
                        if($meta) $meta = ' '.$meta;
                        $ret .= '<meta'.$meta.'>';
                    }
                }
            }
            
            return $ret;
        }
        
        public static function setUser(Array $data) {
        	if(!self::$user) self::$user = new UserInstance();
            self::$user->set($data['isGuest'] ?? true, $data['data'] ?? []);
            return true;
        }

		public static function user() {
			return self::$user ?? self::$user = new UserInstance();
		}

		public static function e() {
			return self::encoder();
		}

		public static function encoder() {
			return self::$encoder ?? self::$encoder = new Encoder();
		}

		public static function chunk($data) {
			echo $data;
			flush();
			ob_flush();
		}

		public static function exceptionHandler(Throwable $e) {
			$backtrace = $e->getTrace();
			unset($backtrace[0]);
			$error = ['code' => self::$errno ?? get_class($e), 'file' => $e->getFile(), 'line' => $e->getLine(), 'message' => $e->getMessage()];
			ob_clean();
			require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Exception.php');
			return true;
		}

		public static function errorHandler($errno, $errstr, $errfile, $errline) {
			if (!(error_reporting() & $errno)) {
				return false;
			}
			self::$errno = $errno;
			throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		}

		public static function route() {
			return trim(explode('?', explode('/', $_SERVER['REQUEST_URI'], 2)[1])[0], '/');
		}

		public static function ajax() {
			return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || $_POST['ajax'];
		}
        
        public static function mcache($property, $value = null, $duration = null) {
            if(!mem::$a) return FPN::cache($property, $value, $duration);
            if($value == null && $duration == null) {
                $cache = mem::get($property);
                if(!$cache) return null;
                return $cache;
            } else {
                mem::set($property, $value, $duration == 'inf' ? 0 : time() + $duration);
                return true;
            }
        }

		public static function cache($property, $value = null, $duration = null) {
			if(!isset(FPN::config()->cache)) return;
			if(FPN::config()->cache['type'] == 'file') {
				if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/'.FPN::config()->cache['dir']))
					system('mkdir \''.$_SERVER['DOCUMENT_ROOT'].'/'.FPN::config()->cache['dir'].'\'');
				$property = str_replace('..', '', $property);
				$dirs = explode('/', $property);
				unset($dirs[count($dirs) - 1]);
				$curPath = '';
				foreach ($dirs as $dir) {
					if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/'.FPN::config()->cache['dir'].'/'.$curPath.$dir)) system('mkdir \''.$_SERVER['DOCUMENT_ROOT'].'/'.FPN::config()->cache['dir'].'/'.$curPath.$dir.'\'');
					$curPath .= $dir.'/';
				}
				if(!is_file($_SERVER['DOCUMENT_ROOT'].'/'.FPN::config()->cache['dir'].'/'.$property)) 
					file_put_contents($_SERVER['DOCUMENT_ROOT'].'/'.FPN::config()->cache['dir'].'/'.$property, '');
				$cache = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.FPN::config()->cache['dir'].'/'.$property), true);
				if(is_array($cache) && $value == null && $duration == null) {
					if($cache['expire'] != 'inf' && $cache['expire'] <= time()) {
						unlink($_SERVER['DOCUMENT_ROOT'].'/'.FPN::config()->cache['dir'].'/'.$property);
						return null;
					}
				} else $cache = [];
				if($value == null && $duration == null) return isset($cache['value']) ? unserialize($cache['value']) : null;
				$cache = ['value' => serialize($value), 'expire' => ($duration == 'inf' ? $duration : time() + $duration)];
				file_put_contents($_SERVER['DOCUMENT_ROOT'].'/'.FPN::config()->cache['dir'].'/'.$property, json_encode($cache));
				return true;
			} elseif(FPN::config()->cache['type'] == 'sql') {
				// DB::pquery('CREATE TABLE IF NOT EXISTS '.DB::functions()->escape(FPN::config()->cache['table'], true).' (`name` VARCHAR(255) NOT NULL, `value` LONGTEXT NOT NULL, `expire` VARCHAR(20) NOT NULL, PRIMARY KEY (`name`))');
				
				$tbl = FPN::config()->cache['table'];
				if($value == null && $duration == null) {
					$cache = DB::findOne($tbl, ['name' => $property]);
					if(!$cache) return null;
					if($cache->expire != 'inf' && (int) $cache->expire <= time()) {
						$cache->delete();
						return null;
					}
					return unserialize($cache->value);
				} else {
					DB::replace($tbl)->keys(['name', 'value', 'expire'])->values([$property, serialize($value), $duration == 'inf' ? $duration : time() + $duration])->run();
					return true;
				}
				return null;
			}
		}
	}
?>