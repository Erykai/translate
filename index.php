<?php

use Erykai\Translate\Translate;

require_once "test/config.php";
require_once "vendor/autoload.php";

$translate = new Translate();
$data = new stdClass();
$data->nameDefault = "route";
$data->translate = "/users/{id}/{slug}";
$data->dynamic = "/{id}/{slug}";
echo $translate->data($data)->lang("en")->getResponse()->translate;