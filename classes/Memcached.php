<?php
    class mem {
        public static $m, $a = 1;
        
        public static function setActive($active) {
            self::$a = (bool) $active;
        }
        
        public static function addServer($addr, $port) {
            self::$m->addServer($addr, $port);
            $statuses = self::$m->getStats();
            return isset($statuses[$addr.":".$port]);
        }
        
        public static function __callStatic($method, $args) {
            if(!self::$a) return;
            return call_user_func_array([self::$m, $method], $args);
        }
    }

    if($conf = FPN::config()->memcached && class_exists('Memcached')) {
        mem::$m = new Memcached();
        if(!mem::addServer($conf['host'], $conf['port'])) mem::setActive(0);
            else mem::setActive(1);
    } else mem::setActive(0)
?>