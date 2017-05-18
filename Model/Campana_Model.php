<?php
//ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);
include $_SERVER['DOCUMENT_ROOT'] . '/Omnisup/config.php';
include_once entities . '/Phpagi_asmanager.php';

class Campana_Model {

    private $command;
    private $agi;
    private $argPdo;

    function __construct() {
        $this->command = "queue show";
        $this->agi = new Phpagi_asmanager();
        $this->argPdo = 'pgsql:host=172.16.20.90;dbname=kamailio;port=5432;user='.PG_USER.';password='.PG_PASSWORD;
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
        $cmd = "asterisk  -rx 'queue show " . $CampName . "' |grep from |awk '{print $1}' FS='has taken'|awk '{print $1, $2}' FS='\(ringinuse disabled\)' |awk '{print $1, $2}' FS='\(dynamic\)'";
        $data = shell_exec($cmd);
        return $data;
    }

    function getDialedCalls($CampName) {
      $sql = "select count(*) from cdr where EXTRACT(DAY FROM calldate) = :dia and " .
      "EXTRACT(MONTH FROM calldate) = :mes and EXTRACT(YEAR FROM calldate) = :ano and clid like :clid";
      $day = date("d");
      $month = date("m");
      $year = date("Y");
      try {
        $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
        $query = $cnn->prepare($sql);
        $query->bindParam(':dia', $day);
        $query->bindParam(':mes', $month);
        $query->bindParam(':ano', $year);
        $query->bindParam(':clid', $CampName . "%");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $cnn = NULL;
      } catch (PDOException $e) {
          $result= "Database Error: " . $e;
      }
      return $result;
    }

    function getConnectedCalls($CampName) {
      $sql = "select count(*) from ominicontacto_app_queuelog where EXTRACT(DAY FROM time) = 17 and " .
      "EXTRACT(MONTH FROM time) = 05 and EXTRACT(YEAR FROM time) = 2017 and event like 'ENTERQUEUE' and queuename like 'multinum-3'";
      $day = date("d");
      $month = date("m");
      $year = date("Y");
      try {
        $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
        $query = $cnn->prepare($sql);
        $query->bindParam(':dia', $day);
        $query->bindParam(':mes', $month);
        $query->bindParam(':ano', $year);
        $query->bindParam(':campname', $CampName);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $cnn = NULL;
      } catch (PDOException $e) {
          $result= "Database Error: " . $e;
      }
      return $result;
    }

    function getProcessedCalls($CampName) {
      $sql = "select count(*) from ominicontacto_app_queuelog where EXTRACT(DAY FROM time) = :dia and " .
      "EXTRACT(MONTH FROM time) = :mes and EXTRACT(YEAR FROM time) = :ano and event like 'CONNECT' and queuename like :campname";
      $day = date("d");
      $month = date("m");
      $year = date("Y");
      try {
        $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
        $query = $cnn->prepare($sql);
        $query->bindParam(':dia', $day);
        $query->bindParam(':mes', $month);
        $query->bindParam(':ano', $year);
        $query->bindParam(':campname', $CampName);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $cnn = NULL;
      } catch (PDOException $e) {
          $result= "Database Error: " . $e;
      }
      return $result;
    }

    function getLostCalls($CampName) {
      $sql = "select count(*) from ominicontacto_app_queuelog where EXTRACT(DAY FROM time) = :dia " .
      "and EXTRACT(MONTH FROM time) = :mes and EXTRACT(YEAR FROM time) = :ano and event like 'ABANDON' and " .
      "event like 'EXITWITHTIMEOUT' and queuename like :campname";
      $day = date("d");
      $month = date("m");
      $year = date("Y");
      try {
        $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
        $query = $cnn->prepare($sql);
        $query->bindParam(':dia', $day);
        $query->bindParam(':mes', $month);
        $query->bindParam(':ano', $year);
        $query->bindParam(':campname', $CampName);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $cnn = NULL;
      } catch (PDOException $e) {
          $result= "Database Error: " . $e;
      }
      return $result;
    }

    function getBusyCalls($CampName) {
      $sql = "select count(*) from cdr where EXTRACT(DAY FROM calldate) = :dia and EXTRACT(MONTH FROM calldate) = :mes and " .
      "EXTRACT(YEAR FROM calldate) = :ano and disposition like 'NO ANSWER' and disposition like 'BUSY' and clid like :campname";
      $day = date("d");
      $month = date("m");
      $year = date("Y");
      try {
        $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
        $query = $cnn->prepare($sql);
        $query->bindParam(':dia', $day);
        $query->bindParam(':mes', $month);
        $query->bindParam(':ano', $year);
        $query->bindParam(':campname', $CampName. "%");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $cnn = NULL;
      } catch (PDOException $e) {
          $result= "Database Error: " . $e;
      }
      return $result;
    }

    function getScoreCuantity($CampName) {
      $sql = "select count(*),c.nombre FROM ominicontacto_app_campanadialer cd JOIN ominicontacto_app_calificacioncliente cc " .
      "ON cd.id = cc.campana_id JOIN ominicontacto_app_calificacion c ON cc.calificacion_id = c.id AND EXTRACT(DAY from fecha) = :day " .
      "AND EXTRACT(MONTH from fecha) = :month AND EXTRACT(YEAR from fecha) = :year AND cd.nombre = :nombre GROUP BY c.nombre";
      $day = date("d");
      $month = date("m");
      $year = date("Y");
      try {
        $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
        $query = $cnn->prepare($sql);
        $query->bindParam(':dia', $day);
        $query->bindParam(':mes', $month);
        $query->bindParam(':ano', $year);
        $query->bindParam(':nombre', $CampName);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $cnn = NULL;
      } catch (PDOException $e) {
          $result= "Database Error: " . $e;
      }
      return $result;
    }

    function getCampSummary($CampName) {
      $sql = "";
        try {

        } catch (PDOException $e) {
            $result= "Database Error: " . $e;
        }
        return $result;
    }
}
