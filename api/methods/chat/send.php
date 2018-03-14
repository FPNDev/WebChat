<?php

	API::trim($_GET);
	
	$text = preg_replace("/[\n\r]{2,}/", "\n\n", htmlentities(substr(trim($_GET['text']), 0, 1500)));
	$th = (string) $_GET['th'];

	if(!$text) 
		return json::OUT(['error' => ['msg' => 'Введите сообщение...']]);

	if(strlen($th) < 9 || strlen($th) > 12) 
		return json::OUT(['error' => ['msg' => 'Произошла техническая ошибка']]);

	if(!$sec = FPN::e()->decode($_GET['sec'])) 
		return json::OUT(['error' => ['msg' => 'Ошибка доступа (1)']]);

	if(!$u = DB::select('*')->from('users')->where(['id' => $sec['user_id']])->one())
		return json::OUT(['error' => ['msg' => 'Произошла техническая ошибка']]);

	if(!DB::insert('chat')->keys('uid', 'text')->values([$sec['user_id'], $text])->run())
		return json::OUT(['error' => ['msg' => 'Произошла техническая ошибка']]);

	$id = DB::lastInsertId();

	LP::add('chat#public', [
		'id' => $id,
		'th' => $th,
		'user' => [
			'id' => $u['id'],
			'username' => $u['username']
		],
		'text' => $text
	]);

	return json::OUT(['success' => $id]);
	
?>