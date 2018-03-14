<?php
	class Request {
		public static function send($type = 'GET', $url, $headers = [], $data = []) {
			$ch = self::build($type, $url, $headers, $data);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			$response = curl_exec($ch);
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			global $_CURL;
			$_CURL['headers'] = explode("\r\n", substr($response, 0, $header_size));
			$_CURL['content'] = substr($response, $header_size);
			return $_CURL['content'];
		}
        
        public static function multirequest(Array $handles) {
            $mh = curl_multi_init();
            $handles = array_values($handles);
            foreach($handles as $handle) curl_multi_add_handle($mh, $handle);
            $active = null;
            $results = [];
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($active);
            $r = [];
            foreach ($handles as $k => $h) {
                $r[$k] = curl_multi_getcontent($h);
                curl_multi_remove_handle($mh, $h);
            }
            
            return $r;
        }

		public static function build($type = 'GET', $url, $headers = [], $data = []) {
			$ch = curl_init();
			$data = http_build_query($data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, self::h2a($headers));
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            if(substr($url, 0, 8) == 'https://') curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			switch($type) {
				case 'GET':
					curl_setopt($ch, CURLOPT_URL, $url . (strpos($url, '?') ? '&'.$data : '?'.$data));
					break;
				case 'PUT':
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					break;
				case 'DELETE':
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
					break;
				case 'POST':
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					break;
			}

			return $ch;
		}

		public static function h2a($headers) {
			$result = [];
			foreach($headers as $k => $h) $result[] = is_numeric($k) ? $h : $k.': '.$h;
			return $result;
		}
	}
?>