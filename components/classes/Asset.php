<?php
    class Asset {
        public static $ijs, $js, $css, $dependencies, $async, $meta, $mobile, $desktop, $views;
        public static function register(&$view) {
            self::work(get_called_class(), $view);
            $v = static::$views ?? [];
            if(isset($v[handler::$view->_viewfile_])) self::work($v[handler::$view->_viewfile_], $view, true);
        }

        public static function work($obj, &$view, $arr = false) {
            $d = $arr ? $obj['dependencies'] ?? [] : $obj::$dependencies ?? [];
            $ij = $arr ? $obj['ijs'] ?? [] : $obj::$ijs ?? [];
            $j = $arr ? $obj['js'] ?? [] : $obj::$js ?? [];
            $m = $arr ? $obj['meta'] ?? [] : $obj::$meta ?? [];
            $c = $arr ? $obj['css'] ?? [] : $obj::$css ?? [];
            $mob = $arr ? $obj['mobile'] ?? [] : $obj::$mobile ?? [];
            $des = $arr ? $obj['desktop'] ?? [] : $obj::$desktop ?? [];
            foreach($d as $dependence) {
                call_user_func_array([$dependence, 'register'], [&$view]);
            }
            foreach($j as $js) {
                $view->scripts[] = FPN::fileVersion($js, 'js');
            }
            foreach($ij as $js) {
                $view->iscripts[] = FPN::fileVersion($js, 'js');
            }
            foreach($m ?? [] as $meta) {
                $view->meta[] = $meta;
            }
            foreach($c ?? [] as $css) {
                $view->styles[] = FPN::fileVersion($css, 'css');
            }
            if(FPN::isMobile()) {
                foreach($mob as $k => $mobile) {
                    switch($k) {
                        case 'ijs': {
                            foreach($mobile as $js) {
                                $view->iscripts[] = FPN::fileVersion($js, 'js');
                            }
                            break;
                        }   
                        case 'js': {
                            foreach($mobile as $js) {
                                $view->scripts[] = FPN::fileVersion($js, 'js');
                            }
                            break;
                        }   
                        case 'css': {
                            foreach($mobile as $css) {
                                $view->styles[] = FPN::fileVersion($css, 'css');
                            }
                            break;
                        }  
                        case 'meta': {
                            foreach($mobile as $meta) {
                                $view->meta[] = $meta;
                            }
                            break;
                        }
                    }
                }
            } else {
                foreach($des ?? [] as $k => $desktop) {
                    switch($k) {
                        case 'ijs': {
                            foreach($desktop as $js) {
                                $view->iscripts[] = FPN::fileVersion($js, 'js');
                            }
                            break;
                        }   
                        case 'js': {
                            foreach($desktop as $js) {
                                $view->scripts[] = FPN::fileVersion($js, 'js');
                            }
                            break;
                        }   
                        case 'css': {
                            foreach($desktop as $css) {
                                $view->styles[] = FPN::fileVersion($css, 'css');
                            }
                            break;
                        }  
                        case 'meta': {
                            foreach($desktop as $meta) {
                                $view->meta[] = $meta;
                            }
                            break;
                        }
                    }
                }
            }
        }
    }
    if(is_dir($_SERVER['DOCUMENT_ROOT'].'/components/assets')) {
        $assets = array_slice(scandir($_SERVER['DOCUMENT_ROOT'].'/components/assets'), 2);
        foreach($assets as $asset) {
            FPN::registerComponent('assets/'.$asset);
        }
    }
?>