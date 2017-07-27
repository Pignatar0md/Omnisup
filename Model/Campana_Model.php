<?php
//ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);
include $_SERVER['DOCUMENT_ROOT'] . '/Omnisup/config.php';

class Campana_Model {

    private $argPdo;

    function __construct() {
        $this->argPdo = 'pgsql:host=' . PG_HOST . ';dbname=kamailio;port=5432';
    }

    function getCampaignsForAdm() {
      $sql = "select nombre, ac.id from ominicontacto_app_campana ac join ominicontacto_app_supervisorprofile sp on
      ac.reported_by_id = sp.user_id where estado = 2";
      try {
        $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
        $query = $cnn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $cnn = NULL;
      } catch (PDOException $e) {
          $result= "Database Error: " . $e;
      }
      return $result;
    }

    function getCampaigns($userId) {
      $sql = "select nombre, ac.id from ominicontacto_app_campana ac join ominicontacto_app_supervisorprofile sp on ac.reported_by_id = sp.user_id
              where estado = 2 and sp.id = :id
              union
              select nombre, ac.id from ominicontacto_app_campana ac join ominicontacto_app_campana_supervisors cs on ac.id = cs.campana_id
              join  ominicontacto_app_supervisorprofile sp on sp.user_id = cs.user_id
              where ac.estado = 2 and sp.id = :id";
      try {
        $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
        $query = $cnn->prepare($sql);
        $query->bindParam(':id', $userId);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $cnn = NULL;
      } catch (PDOException $e) {
          $result= "Database Error: " . $e;
      }
      return $result;
    }

    function getCampaign($CampName) {
        $cmd = "asterisk  -rx 'queue show " . $CampName . "' |grep 'from ' |awk '{print $1}' FS='has taken'|awk '{print $1, $2}' FS='\(ringinuse disabled\)' |awk '{print $1, $2}' FS='\(dynamic\)'";
        $data = shell_exec($cmd);
        return $data;
    }

    function getDialedCalls($CampName) {
      $sql = "select count(*) from cdr where EXTRACT(DAY FROM calldate) = :dia and EXTRACT(MONTH FROM calldate) = :mes and
      EXTRACT(YEAR FROM calldate) = :ano and clid like :clid";
      $day = date("d");
      $month = date("m");
      $year = date("Y");
      $CampName = "%".$CampName."%";
      try {
        $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
        $query = $cnn->prepare($sql);
        $query->bindParam(':dia', $day);
        $query->bindParam(':mes', $month);
        $query->bindParam(':ano', $year);
        $query->bindParam(':clid', $CampName);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $cnn = NULL;
      } catch (PDOException $e) {
          $result= "Database Error: " . $e;
      }
      return $result;
    }

    function getConnectedCalls($CampName) {
      $sql = "select count(*) from ominicontacto_app_queuelog where EXTRACT(DAY FROM time) = :dia and " .
      "EXTRACT(MONTH FROM time) = :mes and EXTRACT(YEAR FROM time) = :ano and event like 'ENTERQUEUE' and queuename like :campname";
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
    