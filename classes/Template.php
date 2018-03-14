<?php
	class Template {
		private static $p = [], $t = [];
		public function run($template, $content, $params) {
			foreach($params as $k => $v) {
				$this->{$k} = $v;
			}
			$this->template = (string) $template;
			unset($params); unset($template);
			handler::$template = &$this;
			if(is_file(handler::fullPath($this->template, 'templates'))) {
				include(handler::fullPath($this->template, 'templates'));
			} else {
				echo $content;
			}
		}
		public static function coverView($template, &$view, $params = []) {
			$view->_cover_[] = [$template, $params];
		}
		public static function cover($template, $content, $params = []) {
			self::$p = $params;
			self::$t = $template;
			unset($params);
			foreach(self::$p as $k => $v) {
				$$k = $v;
			}
			if(is_file(handler::fullPath(self::$t, 'templates'))) {
				ob_start();
				include(handler::fullPath(self::$t, 'templates'));
				return ob_get_clean();
			} else {
				return $content;
			}
		}
	}
?>