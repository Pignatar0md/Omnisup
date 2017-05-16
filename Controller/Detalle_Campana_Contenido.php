<?php

include $_SERVER['DOCUMENT_ROOT'] . '/Omnisup/config.php';
include controllers . '/Campana.php';
include controllers . '/Agente.php';

include helpers . '/time_helper.php';

$Controller_Campana = new Campana();
$Controller_Agente = new Agente();
$jsonString = '';

if ($_GET['nomcamp']) {
    if($_GET['agstatus']) {
      $resul = $Controller_Campana->traerCampanaDet($_GET['nomcamp']);
      $jsonString .= '[';
      foreach($resul as $Obj) {
          $Qm = $Obj;
          $pausa = $Controller_Agente->traerTipoPausa(trim($Qm->getExten()));
          $horaini = explode(' ', $pausa[3]);
          $tiempo = RestarHoras(date('H:i:s',$horaini[0]), date('H:i:s'));
          if($Qm->getName()) {
              $jsonString .= '{"agente": "' . trim($Qm->getName()) . '",';
              $status = $Qm->getStatus();
              $status = trim($status);
              if($status == "Not in use") {
                  $jsonString .= '"estado": "Libre",';
                  $jsonString .= '"tiempo": "---",';
              } else if($status == "paused") {
                  $jsonString .= '"estado": "Pausa - ' . $pausa[2] . '",';
                  $jsonString .= '"tiempo": "' . trim($tiempo) . '",';
              } else if($status == "In use") {
                  $jsonString .= '"estado": "Llamada",';
                  $jsonString .= '"tiempo": "---",';
              } else {
                  $jsonString .= '"estado": "Desconectado",';
                  $jsonString .= '"tiempo": "---",';
              }
              $jsonString .= '"acciones": "<button type=\'button\' id=\'' . $Qm->getExten() . '\' class=\'btn btn-primary btn-xs chanspy\' placeholder=\'monitorear\'><span class=\'glyphicon glyphicon-eye-open\'></span></button>&nbsp;'
                    . '                  <button type=\'button\' id=\'' . $Qm->getExten() . '\' class=\'btn btn-primary btn-xs chanspywhisper\' placeholder=\'hablar con agente\'><span class=\'glyphicon glyphicon-sunglasses\'></span></button>&nbsp;'
                    . '                  <button type=\'button\' id=\'' . $Qm->getExten() . '\' class=\'btn btn-primary btn-xs conference\' placeholder=\'conferencia\'><span class=\'glyphicon glyphicon-user\'></span></button>&nbsp;'
                    . '                  <button type=\'button\' id=\'' . $Qm->getExten() . '\' class=\'btn btn-primary btn-xs info\' placeholder=\'conferencia\'><span class=\'glyphicon glyphicon-info-sign\'></span></button>&nbsp;'
                    . '                  <button type=\'button\' id=\'' . $Qm->getExten() . '\' class=\'btn btn-primary btn-xs agentlogoff\' placeholder=\'desconectar agente\'><span class=\'glyphicon glyphicon-off\'></span></button>"},';
         }
      }
      $jsonString = substr($jsonString, 0, -1);
      echo $jsonString . ']';
    } else if ($_GET['campstatus']) {
        $resul = $Controller_Campana->traerCampanaDet($_GET['nomcamp']);
        $jsonString .= '[';
        foreach($resul as $Obj) {

        }
    }
}
