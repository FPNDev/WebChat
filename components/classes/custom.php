<?php

	class dom {
		static function error($text, $class = '', $view = null) {
			$view = $view ?? 'error';
			return handler::renderPartial('dom/'.$view, compact('text', 'class'));
		}
	}

?>