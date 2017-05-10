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
    print_r($resul);
    $jsonString .= '[';
    /*$resul = explode("(", $resul);

    $nombre = $resul[0];

    $sip = explode("/",$resul[1]);
    $sipExt = $sip[1];

    $status = explode("m", $resul[2]);
    echo "nom: ".$nombre." /sip: ".$sipExt." /status: ".$status[1];*/
    $jsonString = substr($jsonString, 0, -1);
    //echo $jsonString . ']';
}
