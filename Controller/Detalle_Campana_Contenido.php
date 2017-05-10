<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Omnisup/config.php';
include controllers . '/Campana.php';
include controllers . '/Agente.php';
include entities . 'QueueMember.php';
include helpers . '/time_helper.php';

$QM = new QueueMember();
$Controller_Campana = new Campana();
//$Controller_Agente = new Agente();
$jsonString = '';

if ($_GET['nomcamp']) {

    $resul = $Controller_Campana->traerCampanaDet($_GET['nomcamp']);
    $jsonString .= '[';
    var_dump($resul);
    $jsonString = substr($jsonString, 0, -1);
    //echo $jsonString . ']';
}
