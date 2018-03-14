<?php
	if(FPN::ajax()) $this->view()->template = 'ajax/main';
	if(FPN::user()->isGuest) return handler::redirect('login');
	return handler::render('logged/chat');
?>