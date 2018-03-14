<?php
	class handler {
		private static $defaultNav = [
			'worker' => 'MainWorker',
			'regex' => false,
			'options' => [
				'template' => 'base',
                'jsSupport' => true
			],
		];

		public static $worker, $view, $template;
        private static function getNav($page) {
        	if(isset(FPN::config()->defaultTemplate)) self::$defaultNav['options']['template'] = FPN::config()->defaultTemplate;
            $page = trim(explode('?', explode('#', $page)[0])[0], '/');
            $nav = FPN::config()->nav;
            if(isset($nav[$page]) && !$nav[$page]['regex']) {
				$nav[$page] = array_replace_recursive(self::$defaultNav, $nav[$page]);
				if(!isset($nav[$page]['options'])) $nav[$page]['options'] = [];
				if(is_file(handler::fullPath($nav[$page]['worker'], 'workers')))  {
					return $nav[$page];
				} else throw new Exception('Worker not found: ' . handler::fullPath($nav[$page]['worker'], 'workers'));
			} else {
				foreach($nav as $k => $val) {
					if(!isset($val['regex']) || !$val['regex']) continue;
					$k = str_replace('/', '\/', $k);
					if(preg_match('/^'.$k.'$/', $page)) {
						$val = array_replace_recursive(self::$defaultNav, $val);
						foreach ($val['options'] as $pk => $pval) if(is_string($pval)) $val['options'][$pk] = preg_replace('/^'.$k.'$/', $pval, $page);
						if(is_file(handler::fullPath($val['worker'], 'workers')))  {
							return $val;
						} else throw new Exception('Worker not found: ' . handler::fullPath($val['worker'], 'workers'));
					}
				}
			}
            
            return null;
        }
		public static function path() {
			if(!$nav = self::getNav($_SERVER['REQUEST_URI'])) return self::renderError();
            global $_PAGE, $_CURL;
            $_PAGE = array_merge($nav['options'], $_PAGE);
            self::$worker = new Worker();
            self::$worker->view()->setParams($_PAGE);
            return self::$worker->run(handler::fullPath($nav['worker'], 'workers'));
		}
		public static function fullPath($path, $folder = 'views') {
			$gpath = $_SERVER['DOCUMENT_ROOT'].'/'.$folder.'/'.$path.'.php';
			if($path[0] == '/') $gpath = $_SERVER['DOCUMENT_ROOT'].$path.'.php'; 
			return $gpath;
		}

		public static function viewExists($viewname, $full = false) {
			if(!$full) $viewname = handler::fullPath($viewname);
			return is_file($viewname);
		}

		public static function render($path, $params = []) {
			global $_PAGE, $_CURL;
			$gpath = handler::fullPath($path);
			if(handler::viewExists($gpath, true)) {
				$view = self::$worker ? self::$worker->view() : (new View())->setParams(self::$defaultNav['options'] ?? []);
				handler::$view = &$view;
				return $view->run($path, $view->getParams(), $params);
			}
			else throw new Exception('View not found: ' . $gpath);
		}

		public static function renderPartial($path, $params = []) {
			global $_PAGE, $_CURL;
			$gpath = handler::fullPath($path);
			if(handler::viewExists($gpath, true)) {
				$view = new View();
				return $view->partial($path, $view->getParams(), $params);
			}
			else throw new Exception('View not found: ' . $gpath);
		}

		public static function renderError($type = 404, $params = []) {
            if($type == 404 && !FPN::ajax()) header("HTTP/1.0 404 Not Found");
			return handler::render('error', array_merge($params, ['type' => $type]));
		}
        
        public static function redirect($path, Array $get = []) {
            if($get) $get = '?' . http_build_query($get);
            header('Location: /' . $path);
            return true;
        }
	}
?>