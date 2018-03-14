<?php

	API::trim($_GET);

	if(!API::validate($_GET, [
		['un', 'required'],
		['pw', 'required'],
		['un', 'string', 'max' => 64, 'min' => 2],
		['pw', 'string', 'max' => 64, 'min' => 8]
	])) return json::OUT(['error' => ['errors' => API::$errors['errors'], 'type' => 'inputs', 'form' => 'lf']]);

	if(!User::login(['username' => $_GET['un'], 'password' => md5($_GET['pw'])])) 
		return json::OUT(['error' => ['msg' => 'Данные введены неверно']]);

	if(bm::check(U_LOGIN)) {
		User::logout();
		return json::OUT(['error' => ['msg' => 'Ваш аккаунт заблокирован<br><br>Если вы считаете, что это ошибка - обратитесь в поддержку сайта']]);
	}

	 return json::OUT(['success' => true]);
	 
?>