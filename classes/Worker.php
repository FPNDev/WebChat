<?php
	class Worker {
		private $_view_;
		public function run($path) {
			global $_PAGE;
			foreach($_PAGE as $k => $v) {
				$$k = $v;
			}
			include($path);
			return $this;
		}

		public function view() {
			return $this->_view_ ?? $this->_view_ = new View();
		}
	}
?>