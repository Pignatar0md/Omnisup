<?php

include models . '/Campana_Model.php';
include entities. '/QueueMember.php';
include entities. '/NumState.php';

class Campana {

    private $Campana_Model;
    private $NumState;

    function __construct() {
        $this->Campana_Model = new Campana_Model();
    }

    function traerCampanas($supervId) {
        $arrData = array();
        $campanas = $this->Campana_Model->getCampaigns($supervId);
        foreach ($campanas as $clave => $valor) {
            if(is_array($valor)) {
                foreach ($valor as $cla => $val) {
                    $arrData[] = $val;
                }
            } else {
              $arrData[] = $valor;
            }
        }
        return $arrData;
    }

    function traerCampanaDet($nomCamp) {
        $campana = $this->Campana_Model->getCampaign($nomCamp);
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

    function traerInfoReporteRealTimeCamp($NomCamp) {
        $llamadasDiscadas = $this->Campana_Model->getDialedCalls($NomCamp);
        $llamadasConectadas = $this->Campana_Model->getConnectedCalls($NomCamp);
        $llamadasProcesadas = $this->Campana_Model->getProcessedCalls($NomCamp);
        $llamadasPerdidas = $this->Campana_Model->getLostCalls($NomCamp);
        $llamadasOcupadas = $this->Campana_Model->getBusyCalls($NomCamp);
        $cdadVentas = $this->Campana_Model->getSells($NomCamp);
        $arrInfo = array();

        foreach ($llamadasDiscadas as $key => $value) {
            foreach($value as $k => $v) {
                $arrInfo['dialed'] = $v;
            }
        }
        foreach ($llamadasConectadas as $key => $value) {
            foreach($value as $k => $v) {
                $arrInfo['connected'] = $v;
            }
        }
        foreach ($llamadasProcesadas as $key => $value) {
            foreach($value as $k => $v) {
                $arrInfo['processed'] = $v;
            }
        }
        foreach ($llamadasPerdidas as $key => $value) {
            foreach($value as $k => $v) {
                $arrInfo['abandoned'] = $v;
            }
        }
        foreach ($llamadasOcupadas as $key => $value) {
            foreach($value as $k => $v) {
                $arrInfo['busy'] = $v;
            }
        }
        return $arrInfo;
    }

    function traerCalificaciones($NomCamp) {
        $cdadCalificaciones = $this->Campana_Model->getScoreCuantity($NomCamp);
        $arrCuant = $arrScore = $arrData = array();
        foreach ($cdadCalificaciones as $key => $value) {
          foreach ($value as $ky => $va) {
            if($ky == "count") {
              $arrCuant[] = $va;
            } else {
              $arrScore[] = $va;
            }
          }
        }
        $arrData = array_combine($arrScore, $arrCuant);
        return $arrData;
    }

    function traerLlamadasEnCola($NomCamp) {
        $result = $this->Campana_Model->getQueuedCalls($NomCamp);
        $rawArrayData = array();
      	$result = explode(PHP_EOL, $result);
      	foreach($result as $clave => $valor) {
            $valor = explode(": ", $valor);
            $rawArrayData[] = $valor[1];
        }
        $rawArrayData = array_filter($rawArrayData, "strlen");
        return $rawArrayData;
    }

    function traerEstadoDeCanales($NomCamp) {
        $result = $this->Campana_Model->getChannelsStatus($NomCamp);
        $datosUtiles = array();
        foreach($result as $clave => $valor) {
            if($clave == "result") {
                foreach($valor as $key => $value) {
                    if($key == "hopperState") {
                        foreach($value as $clav => $valo) {
                            if(in_array($NomCamp, $valo)) {
                                $this->NumState = new NumState();
                                $this->NumState->setNumber($valo['number']);
                                $this->NumState->setState($valo['state']);
                                $datosUtiles[] = $this->NumState;
                                $this->NumState = NULL;
                            }
                        }
                    }
                }
            }
        }
        return $datosUtiles;
    }

}
