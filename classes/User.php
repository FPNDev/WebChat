<?php
    $session = ecookie::get(FPN::config()->user['cookie_name'] ?? 'sec');
    if(!$session) return;
    $session = $session['t'];
    $tbl = FPN::config()->user['token_table'] ?? 'token';
    $utbl = FPN::config()->user['users_table'] ?? 'users';
    $ucol = FPN::config()->user['userid_tokens_column'] ?? 'user_id';
    $tcol = FPN::config()->user['token_column'] ?? 'access_token';
    $data = DB::select($utbl.'.*')->from($tbl)->leftJoin($utbl, $utbl.'.id = '.$tbl.'.'.$ucol)->where([$tcol => $session])->one();
    if($data) {
        FPN::setUser([
            'isGuest' => false,
            'data' => $data
        ]);
    }

    class User {
        public static function login($where, Array $insert = []) {
            $data = self::getData($where);
            if(!$data) return false;
            $tbl = FPN::config()->user['token_table'] ?? 'token';
            $ucol = FPN::config()->user['userid_tokens_column'] ?? 'user_id';
            $tcol = FPN::config()->user['token_column'] ?? 'access_token';
            $insert = array_merge($insert, [$ucol => $data['id'], $tcol => self::generateToken()]);
            return DB::insert($tbl)->keys(array_keys($insert))->values(array_values($insert))->run() && ecookie::set(FPN::config()->user['cookie_name'] ?? 'sec', ['user_id' => $data['id'], 't' => $insert[$tcol]], 86400 * 365) && FPN::setUser([
                'isGuest' => false,
                'data' => $data
            ]);
        }
        
        private static function getData($where) {
            if(gettype($where) == 'integer') $where = ['id' => $where];
            $utbl = FPN::config()->user['users_table'] ?? 'users';
            $u = DB::findOne($utbl, $where);
            if(!$u) return null;
            return $u->asArray();
        }
        
        public static function logout() {
            return ecookie::set(FPN::config()->user['cookie_name'] ?? 'sec', -1, -1) && FPN::setUser([
                'isGuest' => true,
                'data' => []
            ]);
        }
        
        private static function generateToken() {
            return bin2hex(openssl_random_pseudo_bytes(64));
        }
    }

    class UserInstance {
        public $isGuest = true, $data = null;
        function __construct() {
            $this->data = (object) [];
        }
        public function set(Bool $guest = true, Array $data = []) {
            $this->data = new UserData($data);
            $this->isGuest = $guest;
        }
        function __set($attribute, $value) {
            if(array_key_exists($attribute, $this->data->asArray())) {
                $this->data->{$attribute} = $value;
            }
            return $value;
        }
        function __get($attribute) {
            if(array_key_exists($attribute, $this->data->asArray())) {
                return $this->data->{$attribute};
            }
        }
        
    }

    class UserData {
        private $userDataSet = [];
        function __construct($data) {
            $this->userDataSet = $data;
        }
        function __get($attribute) {
            if(array_key_exists($attribute, $this->userDataSet)) return $this->userDataSet[$attribute];
            return null;
        }
        function __set($attribute, $value) {
            if(array_key_exists($attribute, $this->userDataSet)) {
                $this->userDataSet[$attribute] = $value;
                $utbl = FPN::config()->user['users_table'] ?? 'users';
                DB::update($utbl)->set([$attribute => $value])->run();
            }
            return $value;
        }
        public function asArray() {
            return $this->userDataSet;
        }
    }
?>