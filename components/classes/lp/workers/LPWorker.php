<?php
	$channels = FPN::config()->lps ?? [];
	$ts = $_GET['ts'] ? explode('_', $_GET['ts']) : array_fill(0, count($channels), microtime(true));
	if(count($ts) > count($channels)) return json::OUT([]);
	if(count($ts) < count($channels)) $ts = array_merge($ts, array_fill(0, count($channels) - count($ts), microtime(true)));
	for($i = 0; $i < 20; $i++) {
		$md = FPN::mcache('lps');
		$ret = [];
		$return = false;
		foreach($channels as $c => $channel) {
			if(!$ret[$c]) $ret[$c] = [0];
			if(!$md[$channel]) continue;
			foreach($md[$channel] as $k => $mde) {
				if($mde['ts'] <= $ts[$c]) continue;
				$t = $mde['type'];
				$ids = $mde['ids'];
				unset($mde['type']);
				unset($mde['ids']);
				switch($t) {
					case 'public': 
						$ret[$c][0] = max($ret[$c][0], $mde['ts']) + 0.0001;
						$ret[$c][] = array_merge(['id' => $k], $mde); 
						$return = true;
						break;
					case 'private':
						if(FPN::user()->isGuest) break;
						if(!$ids || in_array(FPN::user()->data->id, $ids)) {
							$ret[$c][0] = max($ret[$c][0], $mde['ts']) + 0.0001;
							$ret[$c][] = array_merge(['id' => $k], $mde); 
							$return = true;
						}
				}
			}
		}
		if($return) return json::OUT($ret);
		sleep(1);
	}
	return json::OUT([]);
	exit;
?>