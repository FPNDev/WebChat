<?php
	if(!FPN::user()->isGuest) return handler::redirect('');
	GuestAsset::register($this);
	$this->title = 'Вход в аккаунт';
?>
<div class="boxed-form lg-form">
	<div class="title">Вход в аккаунт</div>
	<form onsubmit="return guest.login(this, event)" action="/method/account.login.php" id="lf">
		<div class="fl-ph">
			<input name="un" id="un" autocomplete="off" required>
			<label>Имя пользователя</label>
		</div>
		<div class="fl-ph">
			<input name="pw" id="pw" type="password" required>
			<label>Пароль</label>
		</div>
		<button type="submit" id="lb">Войти</button>
	</form>
	<div class="subtext">Нету аккаунта? <a class="link" href="/register" onclick="return nav.go(this, event)">Зарегистрироваться</a></div>
</div>