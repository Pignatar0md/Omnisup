<?php

include models . '/Campana_Model.php';
include entities. '/QueueMember.php';

class Campana {

    private $Campana_Model;

    function __construct() {
        $this->Campana_Model = new Campana_Model();
    }

    function traerCampanas() {
        $queue = '';
        $agente = '';
        $qnames = $stringData = $qagents = $qagstatus = $qagcuantcalls = array();
        $campanas = $this->Campana_Model->getCampaigns();
        foreach ($campanas as $clave => $valor) {
            if ($clave == "data") {
                $datos = explode(PHP_EOL, $valor);
                foreach ($datos as $cla => $val) {
                    if ($cla > 0 && $cla < count($datos) - 2) {
                        if (strpos($val, "strategy")) {
                            $val = explode(" ", $val);
                            $qnames[] = $val[0];
                            $queue = $val[0];
                        } elseif (strpos($val, "(dynamic)")) {
                            $val = explode(" ", $val);
                            $agente = $val[6];
                            $qagents[$queue] = $val[6];

                            $qagstatus[$agente] = $agent[14];

                            if (is_numeric($agent[20])) {
                                $qagcuantcalls[$agente] = $agent[20];
                            } else {
                                $qagcuantcalls[$agente] = 0;
                            }
                        }
                    }
                }
            }
        }
        $stringData['queues'] = $qnames;
        return $stringData;
    }

    function traerCampanaDet($nomCamp) {
        $campana = $this->Campana_Model->getCampaign($nomCamp);
        return $campana;
      	$rawArrayData = array();
      	$campana = explode(PHP_EOL, $campana);
      	foreach($campana as $clave => $valor) {
      	    $Qm = new QueueMember();
      	    $valor = explode("(", $valor);
      	    $name = str_replace(")","",$valor[0]);
      	    if($name) {
      	        $Qm->setName($name);
      	    }
      	    $val = explode(" ", $valor[1]);
      	    $exten = str_replace("SIP/","",$val[0]);
      	    if($exten) {
      	        $Qm->setExten($exten);
      	    }
      	    $val = str_replace("(","",$valor[2]);
      	    $val = str_replace(")","",$valor[2]);
      	    $status = $val;
      	    if($status) {
      	        $Qm->setStatus($status);
      	    }
      	    $rawArrayData[] = $Qm;
      	}
        return $rawArrayData;
    }

    function traerCampanasPorAgente($agente) {
        $res = $this->Campana_Model->getCampaignsByAgent($agente);
        return $res;
    }

}
