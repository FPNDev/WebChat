<?php

	$th = (string) $_GET['th'];
	$id = (int) $_GET['id'];
	
	if(strlen($th) < 9 || strlen($th) > 12) 
		return json::OUT(['error' => ['msg' => 'Произошла техническая ошибка']]);

	if(!$sec = FPN::e()->decode($_GET['sec'])) 
		return json::OUT(['error' => ['msg' => 'Ошибка доступа (1)']]);

	if(!$u = DB::select('*')->from('users')->where(['id' => $sec['user_id']])->one())
		return json::OUT(['error' => ['msg' => 'Ошибка доступа (2)']]);

	if(!$entry = DB::findOne('chat', ['id' => $id]))
		return json::OUT(['success' => true]);

	if($entry->uid != $sec['user_id'])
		return json::OUT(['error' => ['msg' => 'Ошибка доступа (3)']]);

	if($entry->delete()) {
		LP::add('chat#public', [
			'id' => $id,
			'th' => $th
		], false, 'delete');
	}

	return json::OUT(['success' => true]);

?>