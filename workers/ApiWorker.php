<?php
    $p = handler::viewExists('/api/methods/'.$dir.'/'.$file);
    $p2 = handler::viewExists('/api/methods/'.$dir.'.'.$file);    
	if($p || $p2) {
		header('Content-Type: application/json');
		return $p ? handler::render('/api/methods/'.$dir.'/'.$file) : handler::render('/api/methods/'.$dir.'.'.$file);
	} else {
		return handler::renderError();
	}
?>