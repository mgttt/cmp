<?php
//require 'cmppx_example.php';

//non proxy mode... TODO cleanup src:
$REQUEST_URI=$_SERVER['REQUEST_URI'];
$PATH_INFO=$_SERVER['PATH_INFO'];

$proxy_url = $PATH_INFO or $REQUEST_URI;
$proxy_url = ltrim($proxy_url,'/');
require 'cmp_root_controller.php';
