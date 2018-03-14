<?php
    class MainAsset extends Asset {
        public static $js = [
            'libs/sAnim.js',
            'libs/tooltips.js',
        ];
        
        public static $css = [
            'base.css',
            'libs/sAnim/base.css',
            'libs/sAnim/style.css',
            'libs/tooltips/style.css',
            'https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i',
            'https://fonts.googleapis.com/icon?family=Material+Icons'
            // 'fonts.css'
        ];
        
        public static $dependencies = [
            'CommonAsset'
        ];

        public static $mobile = [
            'css' => [
                'mobile.css'
            ]
        ];
        
        public static $meta = [
            [
                'name' => 'viewport',
                'content' => 'width=device-width,initial-scale=1'
            ]
        ];

        public static $views = [
            'logged/chat' => [
                'css' => [
                    'logged/chat.css'
                ],
                'js' => [
                    'al/custom/logged/chat.js',
                    '/components/classes/lp/assets/js/longpoll.js'
                ]
            ]
        ];
    }
?>