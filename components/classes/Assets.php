<?php
    class Assets {
        public static function getPath($filename, $type, $full = true) {
            if(FPN::isExternal($filename)) return $filename;
            if(!$type || !$filename) return false;
            if($filename[0] != '/' && !isset(FPN::config()->assets[$type])) throw new Exception('Asset dir is not configured');
            return ($full ? $_SERVER['DOCUMENT_ROOT'] : '') . ($filename[0] == '/' ? $filename : FPN::config()->assets[$type] . '/' . $filename);
        }
        
        public static function get($filename, $type) {
            return file_get_contents(self::getPath($filename, $type));
        }
    }
?>