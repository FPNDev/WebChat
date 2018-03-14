<!DOCTYPE html>
<html>
	<head>
		<title><?=$this->title?></title>
        <?=FPN::meta($this, 'css')?>
        <?=FPN::meta($this, 'ijs')?>
		<?=FPN::meta($this)?>
	</head>
	<body>
        <div id="scroll_fix_wrap">
            <div id="modal_bg"></div>
            <div id="modal_wrap"></div>
            <div class="chat-window-outer">
                <div class="chat-window container"><div id="page-wrapper"><?=$content?></div></div>
            </div>
        </div>
        <?=FPN::meta($this, 'js')?>
	</body>
</html>