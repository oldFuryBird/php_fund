## 我是什么？
一个个人练习小项目，用来追踪基金的价格,方便自己查看
## 我有什么功能？
目前，可以根据基金的编号查询基金的实时价格，并在控制台打印出来
## 安装
```
    composer require oldfurybird/php_fund
```
## 使用 建立文件 index.php
```php
<?php
    require "vendor/autoload.php";
    $show = Fund\UI\UI::initCommand();
    
?>
```
### php-cli 
```
    php index.php -o 260112 --pretty
    php index.php --help
    
```

## 问题和想法的反馈
任何在使用中的问题以及想法和建议都可以反馈给我，可以通过下面的方式联系我
* email ccwc3@163.com

## 关于作者
`蠢的没朋友`