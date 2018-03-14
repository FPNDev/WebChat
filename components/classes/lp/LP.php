<?php
	class LP {
		public static function add($channel, $data, $ids = false, $evType = 'add') {
			$channel = explode('#', $channel, 2);
			if(!$channel[1]) return false;
			if(!FPN::config()->lps || !in_array($channel[0], FPN::config()->lps)) return false;
			if(gettype($ids) == 'integer') $ids = [$ids];
			elseif(is_array($ids) && !$ids && $channel[1] != 'public') return true;
			else $ids = false;
			$c = FPN::mcache('lps') ?? [];
			if(!$c[$channel[0]]) $c[$channel[0]] = [];
			if(!isset($data['id'])) {
				$c[$channel[0]][] = [
					'data' => $data,
					'ids' => $ids,
					'type' => $channel[1],
					'event' => $evType,
					'ts' => microtime(true)
				];
			} else {
				$c[$channel[0]][$data['id']] = [
					'data' => $data,
					'ids' => $ids,
					'type' => $channel[1],
					'event' => $evType,
					'ts' => microtime(true)
				];
			}

			FPN::mcache('lps', $c, 10);
			return true;
		}
	}

	$nav = &FPN::config()->nav;
	$nav['server/lp.listen'] = [
		'worker' => '/components/classes/lp/workers/LPWorker',
		'regex' => false,
		'options' => [
			'jsSupport' => false
		]
	];
?>