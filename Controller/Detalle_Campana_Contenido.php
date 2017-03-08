<?php

include $_SERVER['DOCUMENT_ROOT'] . '/Omnisup/config.php';
include controllers . '/Campana.php';
include controllers . '/Agente.php';
include helpers . '/time_helper.php';

$QM = new QueueMember();
$Controller_Campana = new Campana();
$Controller_Agente = new Agente();
$jsonString = '';

if ($_GET['nomcamp']) {
    
    $resul = $Controller_Campana->traerCampanaDet($_GET['nomcamp']);
    $jsonString .= '[';
    foreach ($resul as $clave => $valor) {
        if (!is_null($valor) && $valor->getExten() != NULL) {
            $QM = $valor;
            $status = $QM->getStatus();
            $status = str_replace('(', '', $status);
            $status = str_replace(')', '', $status);
            $jsonString .= '{"agente": "' . $QM->getName() . '",';
            if ($status == 'paused') {
                $pausa = $Controller_Agente->traerTipoPausa($QM->getExten());
                $horaini = explode(' ', $pausa[3]);
                $tiempo = RestarHoras(date('H:i:s',$horaini[0]), date('H:i:s'));
                $jsonString .= '"estado": "' . $status . ' - ' . $pausa[2] . '",';
            $jsonString .= '"tiempo": "'.$tiempo.'",';
            } else {
                $jsonString .= '"estado": "' . $status . '",';
                $jsonString .= '"tiempo": "'.date('H:i:s').'",';
            }
            $jsonString .= '"acciones": "<button type=\'button\' id=\'' . $QM->getExten() . '\' class=\'btn btn-primary btn-xs chanspy\' placeholder=\'monitorear\'><span class=\'glyphicon glyphicon-eye-open\'></span></button>&nbsp;<button type=\'button\' id=\'' . $QM->getExten() . '\' class=\'btn btn-primary btn-xs chanspywhisper\' placeholder=\'hablar con agente\'><span class=\'glyphicon glyphicon-sunglasses\'></span></button>"},';
        }
    }
    $jsonString = substr($jsonString, 0, -1);
    echo $jsonString . ']';
}