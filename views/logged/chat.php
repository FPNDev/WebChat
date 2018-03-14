<?php
	MainAsset::register($this);
	$this->title = 'Чат';
	$u = FPN::user()->data->asArray();
?>
<div class="top-panel">
	<div><?=htmlentities($u['username'])?></div>
	<a onclick="return nav.go(this, event)" href="/logout">Выход</a>
</div>
<div class="chat-messages btn-spinner" id="ml"></div>
<div class="chat-send">
	<div class="message-area-outer">
		<div id="cm" class="message-area" onkeydown="return chat.send(event)" contenteditable></div>
		<label>Сообщение</label>
		<a class="send-btn" onclick="return chat.send(event)"><img src="/assets/img/cs.png"/></a>
	</div>
</div>
<script id="self_destruct">ad = <?=json_encode(['id' => $u['id'], 'username' => $u['username']])?>;</script>