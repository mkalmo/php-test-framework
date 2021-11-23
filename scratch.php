<?php

namespace stf;

$url = 'http://aa.com:9090?arg=value#anchor';

//var_dump(parse_url($url, PHP_URL_SCHEME));
//var_dump(parse_url($url, PHP_URL_HOST));
//var_dump(parse_url($url, PHP_URL_PORT));
//var_dump(parse_url($url, PHP_URL_PATH));
//var_dump(parse_url($url, PHP_URL_QUERY));
//var_dump(parse_url($url, PHP_URL_FRAGMENT));
$path = '/a/a-ll.php?';

$hostRegex = '/(.*\/)?(.*)/';
preg_match($hostRegex, $path, $matches);
var_dump($matches);
