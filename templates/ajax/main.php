<?php
	if(!$this->anim) {
		if($_POST['direction'] > 0)
			$this->anim = [
				'zIndex' => 1,
				'new' => [
					'name' => 'fadeIn',
					'duration' => 0.4
				],
			];
		else
			$this->anim = [
				'zIndex' => 0,
				'old' => [
					'name' => 'fadeOut',
					'duration' => 0.4
				],
			];

	}
	echo json_encode(['body' => $content, 'title' => $this->title, 'scripts' => array_merge($this->iscripts ?? [], $this->scripts ?? []), 'styles' => $this->styles ?? [], 'meta' => $this->meta ?? [], 'anim' => $this->anim ?? []] );
?>