<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Omnisup/config.php';
include controllers . '/Campana.php';
include controllers . '/Agente.php';

include helpers . '/time_helper.php';


$Controller_Campana = new Campana();
//$Controller_Agente = new Agente();
$jsonString = '';

if ($_GET['nomcamp']) {

    $resul = $Controller_Campana->traerCampanaDet($_GET['nomcamp']);
    $jsonString .= '[';
    $resul = explode("(", $resul);
    var_dump($resul);
    $jsonString = substr($jsonString, 0, -1);
    //echo $jsonString . ']';
}
