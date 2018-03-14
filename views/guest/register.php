<?php
	if(!FPN::user()->isGuest) return handler::redirect('');
	GuestAsset::register($this);
	$this->title = 'Регистрация';
?>
<div class="boxed-form lg-form">
	<div class="title">Регистрация</div>
	<div id="rs1">
		<form id="rs1f" onsubmit="return guest.register(this, event, 1)" action="/method/account.register.php" autocomplete="off">
			<div class="fl-ph">
				<input name="un" id="un" required>
				<label>Имя пользователя (от 2 до 64 символов)</label>
			</div>
			<div class="fl-ph">
				<input name="em" id="em" required>
				<label>Электронная почта</label>
			</div>
			<div class="fl-ph">
				<input name="pw" id="pw" type="password" required>
				<label>Пароль (от 8 до 64 символов)</label>
			</div>
			<div class="fl-ph">
				<input name="pwc" id="pwc" type="password" required>
				<label>Повторите пароль</label>
			</div>
			<button type="submit" id="rsb1">Зарегистрироваться</button>
		</form>
	</div>
	<div id="rs2" style="display: none" autocomplete="off">
		<div class="subtext">Код подтверждения был отправлен вам на почту</div>
		<form id="rs2f" onsubmit="return guest.register(this, event, 2)" action="/method/account.register2fa.php">
			<div class="fl-ph">
				<input name="code" id="code" required>
				<label>Код потверждения</label>
			</div>
			<input type="hidden" name="sid" id="sid">
			<button type="submit" id="rsb2">Подтвердить почту</button>
		</form>
	</div>
	<div class="subtext">Уже есть аккаунт? <a class="link" href="/login" onclick="return nav.go(this, event)">Войти</a></div>
</div>