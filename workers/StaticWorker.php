<?php
	if(FPN::ajax()) $this->view()->template = 'ajax/main';
	$v = isset($viewname) ? $viewname : FPN::route();
	return handler::render($v);
?>