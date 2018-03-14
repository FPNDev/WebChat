<?php

	$nav = require_once($_SERVER['DOCUMENT_ROOT'].'/configs/nav.php');
	$db = require_once($_SERVER['DOCUMENT_ROOT'].'/configs/db.php');

	return [
		'sitename' => 'EasyDonate',	
		'nav' => $nav,
		'components' => [
            'classes/Asset.php',
            'classes/Assets.php',
			'classes/common.php',
			'classes/curl.php',
            'classes/custom.php',
            'classes/BitMask.php',
            'classes/lp/LP.php',
			'Request.php'
		],
        'assets' => [
            'js' => '/assets/js',
            'css' => '/assets/css',
            'img' => '/assets/img'
        ],

        'cache' => [
            'table' => 'cache',
            'type' => 'sql'
        ],
        'user' => [
            'token_table' => 'sessions',
            'users_table' => 'users',
            'userid_tokens_column' => 'user_id',
            'token_column' => 'session_hash',
            'token_salt' => '"d*{,D/Xxv8\'ED,qSlZ*peUTAxA)uaE'
        ],

        'lps' => ['chat'],

        // 'memcached' => [
        //     'host' => 'localhost',
        //     'port' => 11211
        // ],

		'db' => $db,
		'cookieSalt' => 'B-#g_TJ6Jit9d4/O0f2[_pb0)ILsclE'
	];

?>