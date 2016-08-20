# What is CMP
CMP = Class.Method (Param), Official website <a href="http://cmptech.info/" target=_blank>cmpTech.info</a>

### [TEST SERVER WITH DOCKER]

```shell
git clone https://github.com/cmptech/cmp.git
cd cmp/
sh ./server_start.sh
echo 
echo now use your browser to open http://localhost:9888/
```

### [TEST IN OTHER SERVER]
* Install into any web server supports PHP >=5.3.X
* Copy "config.switch.override.tmp.example.php" as "config.switch.override.tmp" for switching config-folder.

# TODO

* BPME integration ( github/cmptech/bpme-php )
* Folder Autoload and namespace consolidate
* Tiny(OneFileBundle)version

# Core Dependency
* <a href="http://github.com/faisalman/simple-excel-php" target=_blank>SimpleExcel</a> [0.3.15], for the xls(xml)-php-compilation-for-langpack   //@link QuickFunc getLang()
* dzTemplate: a mini-php-template engine  //@link QuickFunc eval(TPL());

# External Dependency
* <a href="http://pear.php.net/package/Cache_Lite/download/" target=_blank>Cache_Lite</a> [1.7.2] used for the IO-file-caching
* <a href="http://purecss.io/" target=_blank>purecss.io</a> [0.6] introduced for the mini css (maybe remove in future)

# Training (CHN)
https://www.gitbook.com/book/cmptech/cmp-training-book/

