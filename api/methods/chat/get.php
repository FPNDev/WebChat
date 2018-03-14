<?php

	$of = (int) $_GET['of'];
	$sec = $_GET['sec'];

	$c = DB::select('COUNT(*)')->from('chat')->column() ?? 0;

	$cl = DB::select('chat.*', 'users.username')->from('chat')->innerJoin('users', 'users.id = chat.uid')->order('id', 'DESC')->limit(20, $of)->all();

	$e = $of + count($cl) >= $c;

	return json::OUT(['success' => ['e' => $e, 'u' => $cl]]);
	
?>