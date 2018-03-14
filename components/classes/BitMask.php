<?php

	include($_SERVER['DOCUMENT_ROOT'].'/configs/bm.php');

	class bm {
		static function remove($service, $id = null) {
			if($id !== null) {
				$utbl = FPN::config()->user['users_table'] ?? 'users';
				$cr = DB::findOne($utbl, ['id' => $id]);
				if(!$cr) return false;
				$cr->role = $cr->role & ~$service;
				return $cr->save();
			}

			FPN::user()->role = FPN::user()->role ^ $service;

			return true;
		}

		static function ban($service, $time, $reason, $id = null) {
			$id = $id ?? FPN::user()->id;
			return DB::replace('bans')->keys('time', 'reason', 'admin_id', 'h')->values([time() + $time, $reason, FPN::user()->id, sha1($id.':'.$service)])->run();
		}

		static function check($service, $id = null) {
			if($id === null) $u = FPN::user()->data;
			else $u = (object) DB::select('*')->from(FPN::config()->user['users_table'] ?? 'users')->where(['id' => $id])->one();

			if(!$u)
				return false;

			if(!($u->role & $service)) 
				return true;

			return DB::select('*')->from('bans')->where(['h' => sha1($u->id.':'.$service), ['>', 'time', time()]])->one();
		}
	}

?>