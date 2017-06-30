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
      $sql = "select nombre from ominicontacto_app_campana ac join ominicontacto_app_supervisorprofile sp on
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
      $sql = "select nombre from ominicontacto_app_campana ac join ominicontacto_app_supervisorprofile sp on ac.reported_by_id = sp.user_id
              where estado = 2 and sp.id = :id
              union
              select nombre from ominicontacto_app_campana ac join ominicontacto_app_campana_supervisors cs on ac.id = cs.campana_id
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
      $sql = "select count(*) from cdr where EXTRACT(DAY FROM calldate) = :dia and EXTRACT(MONTH FROM calldate) = :mes and " .
      "EXTRACT(YEAR FROM calldate) = :ano and clid like :clid";
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
      $sql = "select count(*) from ominicontacto_app_queuelog where EXTRACT(DAY FROM time) = :dia and EXTRACT(MONTH FROM time) = :mes " .
      "and EXTRACT(YEAR FROM time) = :ano and (event='ABANDON') or (event='EXITWITHTIMEOUT') and queuename like :campname";
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
      "EXTRACT(YEAR FROM calldate) = :ano and disposition in ('NO ANSWER','BUSY') and clid like :campname";
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
        $query->bindParam(':campname', $CampName);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $cnn = NULL;
      } catch (PDOException $e) {
          $result= "Database Error: " . $e;
      }
      return $result;
    }

    function getScoreCuantity($CampName) {
      $sql = "select count(*),c.nombre as califica FROM ominicontacto_app_campana cd JOIN ominicontacto_app_calificacioncliente cc
      ON cd.id = cc.campana_id JOIN ominicontacto_app_calificacion c ON cc.calificacion_id = c.id AND EXTRACT(DAY from fecha) = :dia
      AND EXTRACT(MONTH from fecha) = :mes AND EXTRACT(YEAR from fecha) = :ano AND cd.nombre = :nombre GROUP BY c.nombre
      UNION select count(*),cd.gestion as califica FROM ominicontacto_app_campana cd JOIN ominicontacto_app_calificacioncliente cc
      ON cd.id = cc.campana_id AND EXTRACT(DAY from cc.fecha) = :dia AND EXTRACT(MONTH from cc.fecha) = :mes
      AND EXTRACT(YEAR from cc.fecha) = :ano AND cd.nombre = :nombre AND cc.es_venta = 't' GROUP BY cd.gestion";
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

    function getSells($CampName) {
      $sql = "select count(*) FROM ominicontacto_app_campana cd JOIN ominicontacto_app_calificacioncliente cc
      ON cd.id = cc.campana_id JOIN ominicontacto_app_calificacion c ON cc.calificacion_id = c.id AND EXTRACT(DAY from fecha) = :dia
      AND EXTRACT(MONTH from fecha) = :mes AND EXTRACT(YEAR from fecha) = :ano AND cd.nombre = :nombre AND es_venta = 't'";
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

    function getQueuedCalls($CampName) {
      $cmd = "asterisk  -rx 'queue show " . $CampName . "' |grep wait |awk '{print $2}' FS='\(' |awk '{print $1}' FS=','";
      $data = shell_exec($cmd);
      return $data;
    }

    function getChannelsStatus($CampName) {
      $process = curl_init("http://" . PG_HOST . ":8080/wombat/api/live/calls/");
      curl_setopt($process, CURLOPT_HEADER, 0);
      curl_setopt($process, CURLOPT_USERPWD, WD_API_USER . ":" . WD_API_PASS);
      curl_setopt($process, CURLOPT_POST, 1);
      curl_setopt($process, CURLOPT_POSTFIELDS, $CampName);
      curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
      $res = curl_exec($process);
      curl_close($process);
      $res = json_decode($res, true);
      return $res;
    }

    function getSIPcredentialsByUserId($userId) {
      $sql = "select sip_extension, sip_password FROM ominicontacto_app_supervisorprofile where id = :id";
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
}
