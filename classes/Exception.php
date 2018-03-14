<?php
    $c = FPN::headersList();
    $c = isset($c['CONTENT-TYPE']) ? explode(';', $c['CONTENT-TYPE'])[0] : '';
    switch($c) {
        case 'text/xml':
        case 'application/xml':
            return require_once($_SERVER['DOCUMENT_ROOT'].'/classes/views/exception/xml.php');
            
        case 'application/json':
            return require_once($_SERVER['DOCUMENT_ROOT'].'/classes/views/exception/json.php');
            
        default:
            return require_once($_SERVER['DOCUMENT_ROOT'].'/classes/views/exception/html.php');
    }
?> 