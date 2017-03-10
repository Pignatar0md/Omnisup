<?php
//ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);

include_once entities . '/Phpagi_asmanager.php';

class Campana_Model {

    private $command;
    private $agi;
    
    function __construct() {
        $this->command = "queue show";
        $this->agi = new Phpagi_asmanager();
    }
    
    function getCampaigns() {
        try {
            $this->agi->connect(AMI_HOST, AMI_USERNAME, AMI_PASWORD);
        } catch (Exception $ex) {
            return "problemas de Conexion AMI: ".$ex;
        }
        $data = $this->agi->Command($this->command);
        $this->agi->disconnect();
        return $data;
    }
    
    function getCampaign($CampName) {
        try {
            $this->agi->connect(AMI_HOST, AMI_USERNAME, AMI_PASWORD);
        } catch (Exception $ex) {
            return "problemas de Conexion AMI: ".$ex;
        }
        $data = $this->agi->Command($this->command." $CampName");
        $this->agi->disconnect();
        return $data;
    }
}
