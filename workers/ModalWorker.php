<?php
	$this->view()->template = 'ajax/main';
    ModalAsset::register($this->view());
	$modal = str_replace(['../', '..\\'], '', $modal);
	if(!is_file($_SERVER['DOCUMENT_ROOT'].'/views/modal/'.$modal.'.php')) return handler::render('modal/error');
		
	return handler::render('modal/'.$modal, compact('data'));
?>