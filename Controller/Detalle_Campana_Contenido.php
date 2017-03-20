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
            $pausa = $Controller_Agente->traerTipoPausa($QM->getExten());
            $horaini = explode(' ', $pausa[3]);
            $tiempo = RestarHoras(date('H:i:s',$horaini[0]), date('H:i:s'));
            if ($status == 'paused') {
              /*$pausa = $Controller_Agente->traerTipoPausa($QM->getExten());
                $horaini = explode(' ', $pausa[3]);
                $tiempo = RestarHoras(date('H:i:s',$horaini[0]), date('H:i:s'));*/
                $jsonString .= '"estado": "' . $status . ' - ' . $pausa[2] . '",';
            $jsonString .= '"tiempo": "' . $tiempo . '",';
          } elseif ($status == "Unavailable" || $status == "Invalid") {
                //procedimiento para obtener las colas donde exista el agente
                $arrData = $Controller_Campana->traerCampanasPorAgente($QM->getName());
                $arrQueueNames = array();
                foreach ($arrData as $key => $value) {
                    foreach ($value as $cla => $val) {
                        $arrQueueNames[] = $val;
                    }
                }
                $Controller_Agente->Desregistrar($QM->getExten(),$arrQueueNames);
          } else {
                $jsonString .= '"estado": "' . $status . '",';
                $jsonString .= '"tiempo": "'. $tiempo .'",';
            }
            $jsonString .= '"acciones": "<button type=\'button\' id=\'' . $QM->getExten() . '\' class=\'btn btn-primary btn-xs chanspy\' placeholder=\'monitorear\'><span class=\'glyphicon glyphicon-eye-open\'></span></button>&nbsp;'
                    . '                  <button type=\'button\' id=\'' . $QM->getExten() . '\' class=\'btn btn-primary btn-xs chanspywhisper\' placeholder=\'hablar con agente\'><span class=\'glyphicon glyphicon-sunglasses\'></span></button>&nbsp;'
                    . '                  <button type=\'button\' id=\'' . $QM->getExten() . '\' class=\'btn btn-primary btn-xs conference\' placeholder=\'conferencia\'><span class=\'glyphicon glyphicon-user\'></span></button>&nbsp;'
                    . '                  <button type=\'button\' id=\'' . $QM->getExten() . '\' class=\'btn btn-primary btn-xs info\' placeholder=\'conferencia\'><span class=\'glyphicon glyphicon-info-sign\'></span></button>&nbsp;'
                    . '                  <button type=\'button\' id=\'' . $QM->getExten() . '\' class=\'btn btn-primary btn-xs agentlogoff\' placeholder=\'desconectar agente\'><span class=\'glyphicon glyphicon-off\'></span></button>"},';
        }
    }
    $jsonString = substr($jsonString, 0, -1);
    echo $jsonString . ']';
}
