<?php

	API::trim($_GET);
	
	if(!API::validate($_GET, [
		['un', 'required'],
		['pw', 'required'],
		['pwc', 'required'],
		['em', 'required'],
		['un', 'string', 'max' => 64, 'min' => 2],
		['pw', 'string', 'max' => 64, 'min' => 8],
		['em', 'string', 'max' => 255],
		['em', 'email']
	])) return json::OUT(['error' => ['errors' => API::$errors['errors'], 'type' => 'inputs', 'form' => 'rs1f']]);

	if($_GET['pw'] !== $_GET['pwc'])  
		return json::OUT(['error' => ['errors' => ['pw' => true, 'pwc' => true], 'type' => 'inputs', 'form' => 'rs1f']]);

	$em = $_GET['em'];

	if($u = DB::select('em', 'username')->from('users')->where(['username' => $_GET['un']], ['em' => $em])->one()) {
		$el = [];
		if($u['username'] == $_GET['un']) $el['un'] = true;
		if($u['em'] == $em) $el['em'] = true;

		return json::OUT(['error' => ['errors' => $el, 'type' => 'inputs', 'form' => 'rs1f']]);
	}

	session_start();

	$_SESSION['user_data'] = [];
	$_SESSION['user_data']['un'] = $_GET['un'];
	$_SESSION['user_data']['pw'] = $_GET['pw'];
	$_SESSION['user_data']['em'] = $_GET['em'];

	$sid = bin2hex(openssl_random_pseudo_bytes(32));
	$ec = json_encode(floor(hexdec(bin2hex(openssl_random_pseudo_bytes(2)))));

	mail($em, 'Код подтверждения на chat.ru', 'Ваш код подтверждения: '.$ec);
	DB::insert('codes')->keys('em', 'sid', 'c')->values([$em, sha1($sid), sha1($ec)])->run();
	
	return json::OUT(['success' => true, 'sid' => $sid]);

?>