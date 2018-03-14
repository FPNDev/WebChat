<?php

	class DB {
		public static function __callStatic($name, $arguments) {
			return call_user_func_array([FPN::getDB(), $name], $arguments);
		}

		public static function use($dbn) {
			return FPN::getDB($dbn);
		}
	}

	class QB extends PDO {

		public function find($tbl)
		{
			$args = func_get_args();
			unset($args[0]);
			$primary_key = $this->pquery('SHOW KEYS FROM '.$this->functions()->escape($tbl, true).' WHERE Key_name = \'PRIMARY\' OR Key_name = \'UNIQUE\'')->fetch(PDO::FETCH_ASSOC);
			if($primary_key) $primary_key = $primary_key['Column_name'];
			$response = call_user_func_array(array($this->select('*')->from($tbl), 'where'), $args)->all();
			$upd = [];
			foreach ($response as $key => $val) {
				if($primary_key) $upd[] = [$primary_key => $val[$primary_key]];
					else $upd[] = $val;
			}
			return $response ? new QBSave($this, $tbl, false, $upd, $response) : null;
		}

		public function findOne($tbl)
		{
			$args = func_get_args();
			unset($args[0]);
			$primary_key = $this->pquery('SHOW KEYS FROM '.$this->functions()->escape($tbl, true).' WHERE Key_name = \'PRIMARY\' OR Key_name = \'UNIQUE\'')->fetch(PDO::FETCH_ASSOC);
			if($primary_key) $primary_key = $primary_key['Column_name'];
			$response = call_user_func_array(array($this->select('*')->from($tbl), 'where'), $args)->one();
			return $response ? new QBSave($this, $tbl, false, [$primary_key ? [$primary_key => $response[$primary_key]] : $response], $response) : null;
		}

		public function createEntry($tbl) {
			return new QBSave($this, $tbl, true, [], []);
		}

		public function pquery($query, $data = [], $s = true) {
			$prepare = $this->prepare($query);

			$sent = $prepare->execute($data);
			return $s ? $prepare : $sent;
		}


		function __call($name, $arguments) {
			return new QBQuery($this, $name, $arguments);
		}
		
	}

	class QBQuery {

		private $__qb;

		private $__query = '', $__params = [];
		private $__functions;

		function __construct(QB $db, $name, $arguments) {
			$this->setDB($db);
			$this->addFunctionsHandler();
			return method_exists($this->functions(), $name) ? call_user_func_array([$this->functions(), $name], $arguments) : null;
		}

		private function setDB(QB $db) {
			return $this->__qb = $db;
		}

		private function addFunctionsHandler() {
			return $this->__functions = new QBFunctions($this);
		}

		public function getDB() {
			return $this->__qb;
		}

		public function setQuery($attribute, $query = '') {
			$this->__query = $query . $attribute;
		}

		public function addParam($val) {
			$this->__params[':q'.count($this->__params)] = $val;
		}

		public function getQueryString() {
			return $this->__query;
		}

		public function getQueryParams() {
			return $this->__params;
		}

		private function functions() {
			return $this->__functions;
		}

		public function run($params = []) {
			return $this->getDB()->pquery($this->getQueryString(), array_merge($params, $this->getQueryParams()), false);
		}

		public function all($params = []) {
			return $this->getDB()->pquery($this->getQueryString(), array_merge($params, $this->getQueryParams()))->fetchAll(PDO::FETCH_ASSOC);
		}

		public function one($params = []) {
			return $this->getDB()->pquery($this->limit(1)->getQueryString(), array_merge($params, $this->getQueryParams()))->fetch(PDO::FETCH_ASSOC);
		}

		public function column($n = 0, $params = []) {
			return $this->getDB()->pquery($this->getQueryString(), array_merge($params, $this->getQueryParams()))->fetchColumn($n);
		}

		function __call($name, $arguments) {
			return method_exists($this->functions(), $name) ? call_user_func_array([$this->functions(), $name], $arguments) : null; 
		}
	}

	class QBFunctions {

		private $__qb;

		function __construct($qb) {
			$this->__qb = $qb;
		}

		public function select() {
			$args = func_get_args();
			$select = [];
			foreach($args as $arg) {
				if(gettype($arg) == 'string') {
					$tbl = explode(' ', (string) $arg);
					$as = isset($tbl[1]) ? $tbl[1] : '';
					$select[] = $this->escape($tbl[0], true) . ($as ? ' AS ' . $this->escape($tbl[1], true) : '');
				} else if(gettype($arg) == 'array') {
						$s = [];
						foreach($arg as $key => $value) {
							if(gettype($key) == 'integer') {
								$tbl = explode(' ', $value);
								$as = isset($tbl[1]) ? $tbl[1] : '';
								$s[] = $this->escape($tbl[0], true) . ($as ? ' AS ' . $this->escape($tbl[1], true) : '');
							}
						}
						$select[] = implode(',', $s);
					}
			}
			$attribute = implode(',', $select);
			$this->__qb->setQuery('SELECT '.$attribute.' ');
			return $this->__qb;
		}

		public function delete() {
			$this->__qb->setQuery('DELETE ');
			return $this->__qb;
		}

		public function update($tbl) {

			$tbl = $this->escape($tbl, true);
			$this->__qb->setQuery('UPDATE '.$tbl.' ');
			return $this->__qb;
		}

		public function insert($tbl) {
			$tbl = $this->escape($tbl, true);
			$this->__qb->setQuery('INSERT INTO '.$tbl.' ');
			return $this->__qb;
		}

		public function replace($tbl) {
			$tbl = $this->escape($tbl, true);
			$this->__qb->setQuery('REPLACE INTO '.$tbl.' ');
			return $this->__qb;
		}

		public function keys() {
			$args = func_get_args();
			$keys = [];
			foreach ($args as $arg) {
				if(gettype($arg) == 'string') { $keys[] = $this->escape($arg, true); }
					else if(gettype($arg) == 'array') {
						foreach ($arg as $value) {
							$keys[] = $this->escape($value, true);

						}
					}
			}
			$keys = '('.implode(', ', $keys).')';
			$this->__qb->setQuery($keys.' ', $this->__qb->getQueryString());
			return $this->__qb;
		}

		public function values() {
			$args = func_get_args();
			$values = [];
			foreach ($args as $arg) {
				if(gettype($arg) == 'array') {
					$v = [];
					foreach($arg as $value) {
						if(gettype($value) != 'array') {
							$v[] = ':q'.count($this->__qb->getQueryParams());
							$this->__qb->addParam($value);
						} else {
							$vv = [];
							foreach($value as $v2) {
								$vv[] = ':q'.count($this->__qb->getQueryParams());
								$this->__qb->addParam($v2);
							}
							if($vv) $values[] = '('.implode(', ', $vv).')';
						}
					}
					if($v) $values[] = '('.implode(', ', $v).')';
				}
			}
			$values = implode(', ', $values);
			$this->__qb->setQuery('VALUES '.$values.' ', $this->__qb->getQueryString());
			return $this->__qb;
		}

		public function from($tbl) {
			$tbl = explode(' ', $tbl);
			$tbl_name = $tbl[0];
			$as = isset($tbl[1]) ? $tbl[1] : '';
			$this->__qb->setQuery('FROM ' . $this->escape($tbl_name, true) . ($as ? 'AS '. $this->escape($as, true) : '') . ' ', $this->__qb->getQueryString());
			return $this->__qb;
		}

		public function set($params) {
			if(gettype($params) == 'string') {
				$this->_qb->setQuery('SET '.$params.' ', $this->__qb->getQueryString());
				return $this->__qb;
			} else if(gettype($params) == 'array') {
				$s = [];
				foreach($params as $k => $value) {
					$s[] = $this->escape($k, true).'=:q'.(count($this->__qb->getQueryParams()));
					$this->__qb->addParam($value);
				}
				$s = implode(', ', $s);
				$this->__qb->setQuery('SET '.$s.' ', $this->__qb->getQueryString());
			} return $this->__qb;
		}

		public function leftJoin($tbl, $on) {
			$tbl = explode(' ', $tbl);
			$tbl_name = $tbl[0];
			$as = isset($tbl[1]) ? $tbl[1] : '';
			$this->__qb->setQuery('LEFT JOIN '.$this->escape($tbl_name, true) . ($as ? 'AS '. $this->escape($as, true) : '') . ' ON '.$on.' ', $this->__qb->getQueryString());
			return $this->__qb;
		}

		public function innerJoin($tbl, $on) {
			$tbl = explode(' ', $tbl);
			$tbl_name = $tbl[0];
			$as = isset($tbl[1]) ? $tbl[1] : '';
			$this->__qb->setQuery('INNER JOIN '.$this->escape($tbl_name, true) . ($as ? 'AS '. $this->escape($as, true) : '') . ' ON '.$on.' ', $this->__qb->getQueryString());
			return $this->__qb;
		}

		public function rightJoin($tbl, $on) {
			$tbl = explode(' ', $tbl);
			$tbl_name = $tbl[0];
			$as = isset($tbl[1]) ? $tbl[1] : '';
			$this->__qb->setQuery('RIGHT JOIN '.$this->escape($tbl_name, true) . ($as ? 'AS '. $this->escape($as, true) : '') . ' ON '.$on.' ', $this->__qb->getQueryString());
			return $this->__qb;
		}

		public function limit($limit, $offset = 0) {
			$limit = (int) $offset . ', ' . (int) $limit;
			$this->__qb->setQuery('LIMIT '.$limit.' ', $this->__qb->getQueryString());
			return $this->__qb;
		}

		public function order($field, $type = 'DESC') {
			$this->__qb->setQuery('ORDER BY '.$this->escape($field, true).' '.$type.' ', $this->__qb->getQueryString());
			return $this->__qb;
		}

		public function orderBy() {
			return call_user_func_array([$this, 'order'], func_get_args());
		}

		public function group($field) {
			$this->__qb->setQuery('GROUP BY '.$this->escape($field, true).' ', $this->__qb->getQueryString());
			return $this->__qb;
		}

		public function groupBy() {
			return call_user_func_array([$this, 'group'], func_get_args());
		}

		public function like($key, $val) {
			return $this->escape($key, true).'=\''.$this->escape($val).'\'';
		}

		public function where() {
			
			$where = [];
			$args = func_get_args();
			foreach($args as $arg) {
				switch(gettype($arg)) {
					case 'string':
					case 'integer':
						$where[] = $arg;
						break;
					case 'array':
						$w = [];
						foreach($arg as $k => $value) {
							if(gettype($k) != 'integer') 
							{
								$w[] = $this->escape($k, true).'=:q'.(count($this->__qb->getQueryParams()));
								$this->__qb->addParam($value);
							} else {
								if(gettype($value) == 'array' && count($value) == 3 && isset($value[0]) && isset($value[1]) && isset($value[2])) {
									$w[] = $this->escape($value[1], true).' '.$value[0].' :q'.(count($this->__qb->getQueryParams()));
									$this->__qb->addParam($value[2]);
								} else $w[] = $value;
							}
						}
						$where[] = implode(' AND ', $w);
				}
			}
			if($where) {
				$where = '(' . implode(') OR (', $where) . ')';
				$this->__qb->setQuery('WHERE '.$where.' ', $this->__qb->getQueryString());
			}
			return $this->__qb;
		}

		public function getQueryString() {
			return $this->__qb->getQueryString();
		}

		public function escape($v, $name = false) {
			if($name && preg_match('/^[\w-_]*$/i', $v)) $v = '`'.$v.'`';
			return $name ? preg_replace("/[~!@#$%^&\\\\\-+=\|\\/';:,]/i", '', $v) : substr($this->__qb->getDB()->quote($v), 1, strlen($v));
		}
	}


	class QBSave {
		private $__newEntry;
		private $__response = [];
		private $__tblName;
		private $__updFields;
		private $__qb;

		function __construct($db, $tblName, $newEntry, $updFields, $response) {
			$this->__qb = $db;
			$this->__tblName = $tblName;
			$this->__newEntry = $newEntry;
			$this->__updFields = $updFields;
			$this->__response = $response;
		}

		public function delete() {
			if(!$this->__newEntry) {
				return call_user_func_array(array($this->__qb->delete()->from($this->__tblName), 'where'), $this->__updFields)->run();
			} return null;
		}
        
        public function asArray() {
            return $this->__response;
        }

		public function save() {
			if(!$this->__newEntry) {
				if($this->__updFields) {
					$set = [];
					$upd_fields = $this->__updFields;
					$resp = $this->__response;
					foreach($this as $key => $val) {
						if(substr($key, 0, 2) != '__') {
							$set[$key] = $val;
							foreach ($upd_fields as $k => $value) {
								$upd_fields[$k][$key] = $val;
							}
							if(isset($resp[0]) && is_array($resp[0])) {
								foreach ($resp as $k => $value) {
									if(isset($resp[$k][$key])) $resp[$k][$key] = $val;
								}
							} else {
								if(isset($resp[$key])) $resp[$key] = $val;
							}
							unset($this->{$key});
						}
					}
					$response = call_user_func_array(array($this->__qb->update($this->__tblName)->set($set), 'where'), $this->__updFields)->run();
					if($response) { 
						$this->__updFields = $upd_fields;
						$this->__response = $resp;
					}
					return $response;
				} return null;
			} else {
				$insert = []; $keys = [];
				foreach($this as $key => $val) {
					if(substr($key, 0, 2) != '__') {
						$keys[] = $key;
						if(is_array($val)) {
							$iterator = 0;
							foreach ($val as $field) {
								$insert[$iterator][] = $field;
								$iterator++;
							}
						} else {
							$insert[0][] = $val;
						}
					}
				}
				return call_user_func_array(array($this->__qb->insert($this->__tblName)->keys($keys), 'values'), $insert)->run();
			}
		}

		public function __get($key) {
			return $this->__response[$key];
		}
	}

?>