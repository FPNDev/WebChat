<?php

	API::trim($_GET);
	if(!API::validate($_GET, [['code', 'required'], ['code', 'number'], ['sid', 'required']])) 
		return json::OUT(['error' => ['errors' => API::$errors, 'type' => 'inputs', 'form' => 'rs2f']]);

	$sid = DB::findOne('codes', ['sid' => sha1($_GET['sid'])]);
	if(!$sid) 
		return json::OUT(['error' => ['errors' => ['code' => true], 'type' => 'inputs']]);

	$c = $sid->c;
	$em = $sid->em;
	$sid->delete();

	if($c != sha1($_GET['code']))
		return json::OUT(['error' => ['msg' => 'Код введен неверно. Новый код был отправлен вам на почту', 'action' => 'guest.regCodeSend()']]);

	session_start();

	$ud = $_SESSION['user_data'];

	if($u = DB::select('em', 'username')->from('users')->where(['username' => $ud['un']], ['em' => $ud['em']])->one()) {
		if($u['username'] == $ud['un'])
			return json::OUT(['error' => ['msg' => 'Пользователь с таким именем уже зарегистрирован', 'action' => 'guest.regS1()']]);
		if($u['em'] == $ud['em'])
			return json::OUT(['error' => ['msg' => 'Пользователь с такой электронной почтой уже зарегистрирован', 'action' => 'guest.regS1()']]);
	}

	$nu = DB::createEntry('users');
	$nu->username = $ud['un'];
	$nu->password = md5($ud['pw']);
	$nu->em = $ud['em'];
	$nu->role = U_ALL;

	session_destroy();

	if(!$nu->save())
		return json::OUT(['error' => ['msg' => 'Произошла техническая ошибка при создании вашего аккаунта', 'action' => 'guest.regS1()']]);

	User::login(['username' => $ud['un']]);
	return json::OUT(['success' => true]);
	
?>