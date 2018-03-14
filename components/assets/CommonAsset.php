<?php
    class CommonAsset extends Asset {
        public static $ijs = [
            'al/common/common.js'
        ];
        
        public static $js = [
            'al/custom/init.js',
            'al/custom/events.js'
        ];
        
        public static $css = [
            'common.css',
            'libs/notifier/style.css',
            'libs/sAnim/base.css',
            'libs/sAnim/style.css',
        ]; 
        
        public static $meta = [
            [
                'charset' => 'utf-8',
            ]
        ];
    }
?>