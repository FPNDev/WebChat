<?php
    class GuestAsset extends Asset {  
        public static $js = [
            'al/custom/guest.js'
        ];
        
        public static $dependencies = [
            'MainAsset'
        ];
    }
?>