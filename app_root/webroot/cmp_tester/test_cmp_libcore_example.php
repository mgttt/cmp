<?php
error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);

//1,
//wget -O CMP_bootstrap.php https://github.com/cmptech/cmp/raw/master/app_root/webroot/_libs/CMP/bootstrap.php

//2, test with the println()/o2s()/s2o()/web()
require_once 'CMP_bootstrap.php';

use \CMP\LibCore as slc;

slc::println( slc::o2s($_SERVER) );

$u="https://api.safe-login-center.com/";
$s=slc::web($u);
slc::println("s=$s");

$o=slc::s2o($s);
var_dump($o);
