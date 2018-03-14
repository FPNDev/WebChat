<?php

	define('U_LOGIN', 1 << 0);
	define('U_CHAT', 1 << 1);
	define('U_ALL', U_LOGIN | U_CHAT);
	
	define('A_LOGIN', 1 << 2);
	define('A_DELETE', 1 << 2);
	define('A_EDIT', 1 << 3);
	define('A_BAN', 1 << 4);
	define('A_REMOVE_ACCESS', 1 << 5);
	define('A_MANAGE', 1 << 6);

	define('A_ALL', A_LOGIN | A_DELETE | A_EDIT | A_BAN | A_REMOVE_ACCESS | A_MANAGE);

?>