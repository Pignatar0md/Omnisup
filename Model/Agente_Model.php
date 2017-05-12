<?php

include_once entities . '/Phpagi_asmanager.php';

class Agente_Model {

    private $command;
    private $agi;

    function __construct() {
        $this->command = "sip show peers";
        $this->agi = new Phpagi_asmanager();
    }

    function getAgents() {
        try {

        } catch (Exception $ex) {
            return "problemas de Conexion : " . $ex;
        }

        return $data;
    }

    function getPauseAgents($agt) {
        try {
            $this->agi->connect(AMI_HOST, AMI_USERNAME, AMI_PASWORD);
        } catch (Exception $ex) {
            return "problemas de Conexion AMI: " . $ex;
        }
        $this->agi->Events('off');
        $this->command = "database show PAUSECUSTOM/AGENT/" . $agt;
        $data = $this->agi->Command($this->command);
        $this->agi->disconnect();
        return $data;
    }

    function ChanSpy($agt, $exten) {
        try {
            $this->agi->connect(AMI_HOST, AMI_USERNAME, AMI_PASWORD);
        } catch (Exception $ex) {
            return "problemas de Conexion AMI: " . $ex;
        }
        $this->agi->Originate("SIP/$exten", "001$agt", 'fts-sup', 1, NULL, NULL, '25000', NULL, NULL, NULL, NULL, false, NULL);
        $this->agi->disconnect();
    }

    function ChanSpyWhisper($agt, $exten) {
        try {
            $this->agi->connect(AMI_HOST, AMI_USERNAME, AMI_PASWORD);
        } catch (Exception $ex) {
            return "problemas de Conexion AMI: " . $ex;
        }
        $this->agi->Originate('SIP/' . $exten, "002$agt", 'fts-sup', 1, NULL, NULL, '25000', NULL, NULL, NULL, NULL, false, NULL);
        $this->agi->disconnect();
    }

    function Conference($agt, $exten) {
        try {
            $this->agi->connect(AMI_HOST, AMI_USERNAME, AMI_PASWORD);
        } catch (Exception $ex) {
            return "problemas de Conexion AMI: " . $ex;
        }
        $this->agi->Originate('SIP/' . $exten, "006$agt", 'fts-sup', 1, NULL, NULL, '25000', NULL, NULL, NULL, NULL, false, NULL);
        $this->agi->disconnect();
    }

    function AgentLogoff($agt, $queueName) {
        try {
            $this->agi->connect(AMI_HOST, AMI_USERNAME, AMI_PASWORD);
        } catch (Exception $ex) {
            return "problemas de Conexion AMI: " . $ex;
        }
        foreach ($queueName as $value) {
            $this->agi->Events('off');
            $this->command = "queue remove member sip/" . $agt . " from ". $value;
            $this->agi->Command($this->command);
        }
        $this->agi->disconnect();
    }
}
