<?php

use Erykai\Translate\Translate;

require_once "config.php";
require_once "vendor/autoload.php";

$translate = new Translate();
$data = new stdClass();
$data->file = "route";
$data->text = "/send/{id}/{slug}"; // $data->text = "Hello"; if remove line 12
$data->dynamic = "/{id}/{slug}"; // off dynamic remove line
$translate->data($data)->target("es")->response();