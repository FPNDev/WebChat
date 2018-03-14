<?php
	class View {
		public $_cover_;
		public function run($path, $params, $variables) {
			$this->_viewfile_ = $path;
			unset($path); 
			$this->_variables_ = $variables;
			unset($variables);
			foreach($params as $k => $v) {
				$this->{$k} = $v;
			}
			unset($params);
			foreach($this->_variables_ as $k => $v) {
				$$k = $v;
			}
			ob_start();
			$this->_viewpath_ = $_SERVER['DOCUMENT_ROOT'].'/views/'.$this->_viewfile_.'.php';
			if($this->_viewfile_[0] == '/') $this->_viewpath_ = $_SERVER['DOCUMENT_ROOT'].$this->_viewfile_.'.php';
			include($this->_viewpath_);
			$content = ob_get_clean();
			foreach($this->_cover_ ?? [] as $c) {
				$content = Template::cover($c[0], $content, $c[1]);
			}
            if($this->jsSupport) {
            	$content .= FPN::jsViewName();
            	if($this->baseurl) $content .= '<script id="self_destruct">ADMIN_BASE_URL = "'.htmlspecialchars($this->baseurl).'"</script>';
            }
			$template = new Template();
			return $template->run($this->template ?? '', $content, $this->getParams());
		}

		public function partial($path, $params, $variables) {
			$this->_viewfile_ = $path;
			unset($path); 
			$this->_variables_ = $variables;
			unset($variables);
			foreach($params as $k => $v) {
				$this->{$k} = $v;
			}
			unset($params);
			foreach($this->_variables_ as $k => $v) {
				$$k = $v;
			}
			$this->_viewpath_ = $_SERVER['DOCUMENT_ROOT'].'/views/'.$this->_viewfile_.'.php';
			if($this->_viewfile_[0] == '/') $this->_viewpath_ = $_SERVER['DOCUMENT_ROOT'].$this->_viewfile_.'.php';
			ob_start();
			include($this->_viewpath_);
			return ob_get_clean();
		}

		public function getParams() {
			$ret = [];
			foreach($this as $k => $val) {
				$ret[$k] = $val;
			}
			return $ret;
		}

		public function setParams($params) {
			foreach($params as $k => $val) {
				$this->{$k} = $val;
			}
			return $this;
		}
	}
?>