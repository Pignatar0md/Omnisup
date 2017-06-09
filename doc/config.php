<?php
date_default_timezone_set("America/Argentina/Cordoba");

define('AMI_USERNAME','');
define('AMI_PASWORD','');
define('AMI_HOST','');
$arrConfig = array('PG_USERNAME' => '',
                   'PG_PASSWORD' => '',
                   'PG_HOST' => '');

define("entities", $_SERVER['DOCUMENT_ROOT'].'/Omnisup/entities');
define("helpers", $_SERVER['DOCUMENT_ROOT'].'/Omnisup/helpers');
define("models", $_SERVER['DOCUMENT_ROOT'].'/Omnisup/Model');
define("controllers", $_SERVER['DOCUMENT_ROOT'].'/Omnisup/Controller');
define("views", $_SERVER['DOCUMENT_ROOT'].'/Omnisup/View');
