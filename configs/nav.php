<?php
	return [

		// guest

		'login' => [
			'worker' => 'StaticWorker',
			'regex' => false,
			'options' => [
				'viewname' => 'guest/login'
			]
		],

		'register' => [
			'worker' => 'StaticWorker',
			'regex' => false,
			'options' => [
				'viewname' => 'guest/register'
			]
		],


		// logged

		'' => [
			'worker' => 'MainWorker',
			'regex' => false
		],

		'logout' => [
			'worker' => 'LogoutWorker',
			'regex' => false,
		],

		// base

		'methods?/(\w+)\.(\w+)\.php' => [
			'worker' => 'ApiWorker',
			'regex' => true,
			'options' => [
				'dir' => '$1',
				'file' => '$2',
				'template' => '',
                'jsSupport' => false
			]
		],

		'modal/(.+)' => [
			'worker' => 'ModalWorker',
			'regex' => true,
			'options' => [
				'modal' => '$1',
                'jsSupport' => false
			]
		]
	];
?>