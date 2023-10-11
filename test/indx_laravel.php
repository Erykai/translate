<?php
use Erykai\Translate\Translate;

require_once "config.php";
require_once "vendor/autoload.php";
$translate = new Translate();


$dir = 'resources/lang/en/';
$files = array_diff(scandir($dir), array('..', '.'));

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
        $validationMessages = include $dir . $file;
        $filenameWithoutExtension = pathinfo($file, PATHINFO_FILENAME);
        $translate->processMessages($validationMessages, $translate, $filenameWithoutExtension,target:"pt_BR");
    }
}
