<?php


$options = getopt('abc::d:',array());

if (empty($options))
{
    echo "Usage: {$argv[0]}\n";
    return;
}

var_dump($options);

var_dump(__NAMESPACE__);

require_once dirname(__FILE__). '/../www/application/third_party/crontab.php';