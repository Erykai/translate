<?php

use Erykai\Translate\Translate;

require_once "test/config.php";
require_once "vendor/autoload.php";

$translate = new Translate();
$data = new stdClass();
$data->file = "route";
$data->text = "/send/{id}/{slug}";
$data->dynamic = "/{id}/{slug}";
print_r($package->data($data)->target("es")->response());