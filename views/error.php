<?php if(FPN::ajax()) $this->template = 'ajax/main'; ?>
<?php MainAsset::register($this); ?>
<?php
	$this->template = 'base';
	$this->title = 'Страница не найдена';
	$message = $this->title;
?>
<?php
	switch($type) {
		case 404:
			$message = 'Страница не найдена';
			?>
			<?php
			break;
	}
?>

<?=dom::error($message, 'page')?>
