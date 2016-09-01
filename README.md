# What is CMP

CMP = Class.Method (Param)

Official website <a href="http://cmptech.info/" target=_blank>cmpTech.info</a>

## Source Code

https://github.com/cmptech/cmp/tree/master/app_root/webroot/_libs/CMP

## cmp-mini-framework-in-one-file: \CMP\LibCore

https://github.com/cmptech/cmp/tree/master/app_root/webroot/_libs/CMP/bootstrap.php

Example:

* https://github.com/cmptech/cmp/blob/master/app_root/webroot/cmp_demo/example_test_cmp_libcore.php

## [TEST SERVER WITH DOCKER]

```shell
git clone https://github.com/cmptech/cmp.git
cd cmp/
sh ./server_start.sh
echo 
echo now use your browser to open http://localhost:9888/
```

## [TEST IN OTHER WEB SERVER]

* Install into any web server supports PHP >=5.3.X
* Copy "config.switch.override.tmp.example.php" as "config.switch.override.tmp" for switching config-folder.

# TODO

* BPME integration ( github/cmptech/bpme-php )
* composer package (cmptech/cmp)
* Improve the Init() strategy

# Core Dependency

* <a href="http://github.com/faisalman/simple-excel-php" target=_blank>SimpleExcel</a> [0.3.15], for the xls(xml)-php-compilation-for-langpack   //@link QuickFunc getLang()
* dzTemplate: a mini-php-template engine class file  //@link QuickFunc include(TPL());

# Training (CHN)

https://www.gitbook.com/book/cmptech/cmp-training-book/

