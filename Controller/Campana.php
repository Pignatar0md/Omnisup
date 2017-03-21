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
        $stringData = array();
        $campana = $this->Campana_Model->getCampaign($nomCamp);
        foreach ($campana as $clave => $valor) {
            if ($clave == "data") {
                $datos = explode(PHP_EOL, $valor);
                foreach ($datos as $key => $value) {
                    if ($key > 2 && $value != "") {
                        $objQM = new QueueMember();
                        $value = trim($value);
                        $value = explode(" ", $value);
                        $exten = substr($value[2], 5);
                        $objQM->setExten($exten);
                        $objQM->setName($value[0] . ' ' . $value[1]);
                        if($value[8] == "(in") {
                            $objQM->setStatus($value[8].' '.$value[9]);
                        } elseif ($value[8] == "(Not" && $value[9] == "in") {
                            $objQM->setStatus($value[8].' '.$value[9].' '.$value[10]);
                        } elseif ($value[8] == "(In") {
                            $objQM->setStatus($value[8].' '.$value[9]);
                        } elseif ($value[8] == "(paused)") {
                            if ($value[9] == "(Unavailable)" || $value[9] == "(Invalid)") {
                              $objQM->setStatus($value[8]);
                              $objQM->setLogoff(true);
                            } else {
                              $objQM->setStatus($value[8]);
                            }
                        } else {
                            $objQM->setStatus($value[8]);
                        }
                    }
                    $stringData[] = $objQM;
                }
            }
        }
        return $stringData;
    }

    function traerCampanasPorAgente($agente) {
        $res = $this->Campana_Model->getCampaignsByAgent($agente);
        return $res;
    }

}
