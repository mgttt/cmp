# What is CMP

CMP = Class.Method (Param)

A tiny PHP "framework" that help to build programs, from small console tools to business website. 

## Official website

<a href="http://cmptech.info/" target=_blank>cmpTech.info</a>

## Source Code

https://github.com/cmptech/cmp/tree/master/app_root/webroot/_libs/CMP

# Quick Mode for small tool: (cmp-mini-framework-in-one-file \CMP\LibCore)

Lib:

* https://github.com/cmptech/cmp/tree/master/app_root/webroot/_libs/CMP/bootstrap.php

Example:

* https://github.com/cmptech/cmp/blob/master/app_root/webroot/cmp_demo/example_test_cmp_libcore.php

or

```php
($f='CMP_bootstrap.php')&&class_exists('\CMP\LibCore')||(file_exists($f)||
file_put_contents($f,file_get_contents('https://github.com/cmptech/cmp/raw/master/app_root/webroot/_libs/CMP/bootstrap.php'))
)&&require_once($f);

//ClassLoader config:
spl_autoload_register(function($class_name){
	if( defined("_APP_DIR_") && file_exists(_APP_DIR_."/$class_name.php") ){
		require_once _APP_DIR_."/$class_name.php";
	}elseif(file_exists("$class_name.php")){
		require_once "$class_name.php";
	}elseif(file_exists(basename($class_name).".php")){
		require_once basename($class_name).".php";
	}
});

use \CMP\LibCore;

LibCore::println( $_SERVER );
```

# TO build website:

## [TINY-WEB-SERVER WITH DOCKER]

we build a docker image that included a PHP7+swoole Environment to run up a server:

```shell
git clone https://github.com/cmptech/cmp.git
cd cmp/
sh ./server_start.sh
echo 
echo now use your browser to open http://localhost:9888/
```

## [IN OTHER WEB SERVER]

* Copy to any web server supports PHP5.4+
* Copy "config.switch.override.tmp.example.php" as "config.switch.override.tmp" for switching config-folder.

# Examples

* https://github.com/cmptech/cmp/tree/master/app_root/webroot/cmp_demo/
* https://github.com/cmptech/cmp/tree/master/app_root/webroot/cmp_tester/

# TODO

* BPME integration ( github/cmptech/bpme-php )
* composer package (cmptech/cmp)

# Core Dependency

* <a href="http://github.com/faisalman/simple-excel-php" target=_blank>SimpleExcel</a> [0.3.15], for the xls(xml)-php-compilation-for-langpack (NOTES: may be replaced in future)   //@link QuickFunc getLang()
* dzTemplate: a modified mini-php-page-template engine class file  //@link QuickFunc include(TPL());

# Training

https://www.gitbook.com/book/cmptech/cmp-training-book/

https://cmptech.gitbooks.io/cmp-training-book/content/


