<?php
	if(!FPN::user()->isGuest && !(FPN::user()->role & U_LOGIN)) User::logout();
?>