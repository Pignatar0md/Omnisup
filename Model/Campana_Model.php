<?php
// ini_set('display_errors', 'On');
// error_reporting(E_ALL | E_STRICT);
include $_SERVER['DOCUMENT_ROOT'] . '/Omnisup/config.php';

class Campana_Model {

    private $argPdo;

    function __construct() {
        $this->argPdo = 'pgsql:host=' . PG_HOST . ';dbname=kamailio;port=5432';
    }

    function getCampaignsForAdm() {
      $sql = "select distinct nombre, ac.id from ominicontacto_app_campana ac
      join ominicontacto_app_campana_supervisors acs
      on ac.id = acs.campana_id join ominicontacto_app_supervisorprofile sp on
      acs.user_id = sp.user_id where estado = 2";
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
              join ominicontacto_app_supervisorprofile sp on sp.user_id = cs.user_id
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

    function getReceivedCalls($IdCamp) {
      $sql = "SELECT ominicontacto_app_queuelog.id, ominicontacto_app_queuelog.time,
                     ominicontacto_app_queuelog.callid, ominicontacto_app_queuelog.queuename,
                     ominicontacto_app_queuelog.campana_id, ominicontacto_app_queuelog.agent,
                     ominicontacto_app_queuelog.agent_id, ominicontacto_app_queuelog.event,
                     ominicontacto_app_queuelog.data1, ominicontacto_app_queuelog.data2,
                     ominicontacto_app_queuelog.data3, ominicontacto_app_queuelog.data4,
                     ominicontacto_app_queuelog.data5
              FROM ominicontacto_app_queuelog
              WHERE (ominicontacto_app_queuelog.event IN (ENTERQUEUE)
              AND ominicontacto_app_queuelog.campana_id = :campid
              AND EXTRACT(DAY from ominicontacto_app_queuelog.time) = :dia
              AND EXTRACT(MONTH from ominicontacto_app_queuelog.time) = :mes
              AND EXTRACT(YEAR from ominicontacto_app_queuelog.time) = :ano)
              ORDER BY ominicontacto_app_queuelog.time DESC";
      $day = date("d");
      $month = date("m");
      $year = date("Y");
      try {
        $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
        $query = $cnn->prepare($sql);
        $query->bindParam(':dia', $day);
        $query->bindParam(':mes', $month);
        $query->bindParam(':ano', $year);
        $query->bindParam(':campid', $IdCamp);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $cnn = NULL;
      } catch (PDOException $e) {
          $result= "Database Error: " . $e;
      }
      return $result;
    }

    function getAttendedCalls($IdCamp) {
      $sql = "SELECT ominicontacto_app_queuelog.id, ominicontacto_app_queuelog.time,
                     ominicontacto_app_queuelog.callid, ominicontacto_app_queuelog.queuename,
                     ominicontacto_app_queuelog.campana_id, ominicontacto_app_queuelog.agent,
                     ominicontacto_app_queuelog.agent_id, ominicontacto_app_queuelog.event,
                     ominicontacto_app_queuelog.data1, ominicontacto_app_queuelog.data2,
                     ominicontacto_app_queuelog.data3, ominicontacto_app_queuelog.data4,
                     ominicontacto_app_queuelog.data5
              FROM ominicontacto_app_queuelog
              WHERE (ominicontacto_app_queuelog.event IN (CONNECT)
              AND ominicontacto_app_queuelog.campana_id = :campid
              AND EXTRACT(DAY from ominicontacto_app_queuelog.time) = :dia
              AND EXTRACT(MONTH from ominicontacto_app_queuelog.time) = :mes
              AND EXTRACT(YEAR from ominicontacto_app_queuelog.time) = :ano)
              ORDER BY ominicontacto_app_queuelog.time DESC";
      $day = date("d");
      $month = date("m");
      $year = date("Y");
      try {
        $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
        $query = $cnn->prepare($sql);
        $query->bindParam(':dia', $day);
        $query->bindParam(':mes', $month);
        $query->bindParam(':ano', $year);
        $query->bindParam(':campid', $IdCamp);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $cnn = NULL;
      } catch (PDOException $e) {
          $result= "Database Error: " . $e;
      }
      return $result;
    }

    function getAbandonedCalls($IdCamp) {
      $sql = "SELECT ominicontacto_app_queuelog.id, ominicontacto_app_queuelog.time,
                     ominicontacto_app_queuelog.callid, ominicontacto_app_queuelog.queuename,
                     ominicontacto_app_queuelog.campana_id, ominicontacto_app_queuelog.agent,
                     ominicontacto_app_queuelog.agent_id, ominicontacto_app_queuelog.event,
                     ominicontacto_app_queuelog.data1, ominicontacto_app_queuelog.data2,
                     ominicontacto_app_queuelog.data3, ominicontacto_app_queuelog.data4,
                     ominicontacto_app_queuelog.data5
              FROM ominicontacto_app_queuelog
              WHERE (ominicontacto_app_queuelog.event IN (ABANDON)
              AND ominicontacto_app_queuelog.campana_id = :campid
              AND EXTRACT(DAY from ominicontacto_app_queuelog.time) = :dia
              AND EXTRACT(MONTH from ominicontacto_app_queuelog.time) = :mes
              AND EXTRACT(YEAR from ominicontacto_app_queuelog.time) = :ano)
              ORDER BY ominicontacto_app_queuelog.time DESC";
      $day = date("d");
      $month = date("m");
      $year = date("Y");
      try {
        $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
        $query = $cnn->prepare($sql);
        $query->bindParam(':dia', $day);
        $query->bindParam(':mes', $month);
        $query->bindParam(':ano', $year);
        $query->bindParam(':campid', $IdCamp);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $cnn = NULL;
      } catch (PDOException $e) {
          $result= "Database Error: " . $e;
      }
      return $result;
    }

    function getExpiredCalls($IdCamp) {
      $sql = "SELECT ominicontacto_app_queuelog.id, ominicontacto_app_queuelog.time,
                     ominicontacto_app_queuelog.callid, ominicontacto_app_queuelog.queuename,
                     ominicontacto_app_queuelog.campana_id, ominicontacto_app_queuelog.agent,
                     ominicontacto_app_queuelog.agent_id, ominicontacto_app_queuelog.event,
                     ominicontacto_app_queuelog.data1, ominicontacto_app_queuelog.data2,
                     ominicontacto_app_queuelog.data3, ominicontacto_app_queuelog.data4,
                     ominicontacto_app_queuelog.data5
              FROM ominicontacto_app_queuelog
              WHERE (ominicontacto_app_queuelog.event IN (EXITWITHTIMEOUT)
              AND ominicontacto_app_queuelog.campana_id = :campid
              AND EXTRACT(DAY from ominicontacto_app_queuelog.time) = :dia
              AND EXTRACT(MONTH from ominicontacto_app_queuelog.time) = :mes
              AND EXTRACT(YEAR from ominicontacto_app_queuelog.time) = :ano)
              ORDER BY ominicontacto_app_queuelog.time DESC";
      $day = date("d");
      $month = date("m");
      $year = date("Y");
      try {
        $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
        $query = $cnn->prepare($sql);
        $query->bindParam(':dia', $day);
        $query->bindParam(':mes', $month);
        $query->bindParam(':ano', $year);
        $query->bindParam(':campid', $IdCamp);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $cnn = NULL;
      } catch (PDOException $e) {
          $result= "Database Error: " . $e;
      }
      return $result;
    }

    function getManualCalls($IdCamp) {
      $sql = "SELECT ominicontacto_app_queuelog.id, ominicontacto_app_queuelog.time,
                     ominicontacto_app_queuelog.callid, ominicontacto_app_queuelog.queuename,
                     ominicontacto_app_queuelog.campana_id, ominicontacto_app_queuelog.agent,
                     ominicontacto_app_queuelog.agent_id, ominicontacto_app_queuelog.event,
                     ominicontacto_app_queuelog.data1, ominicontacto_app_queuelog.data2,
                     ominicontacto_app_queuelog.data3, ominicontacto_app_queuelog.data4,
                     ominicontacto_app_queuelog.data5
              FROM ominicontacto_app_queuelog
              WHERE (ominicontacto_app_queuelog.event IN (ENTERQUEUE)
              AND ominicontacto_app_queuelog.campana_id = :campid
              AND EXTRACT(DAY from ominicontacto_app_queuelog.time) = :dia
              AND EXTRACT(MONTH from ominicontacto_app_queuelog.time) = :mes
              AND EXTRACT(YEAR from ominicontacto_app_queuelog.time) = :ano
               AND ominicontacto_app_queuelog.data4 = 'saliente')
              ORDER BY ominicontacto_app_queuelog.time DESC";
      $day = date("d");
      $month = date("m");
      $year = date("Y");
      try {
          $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
          $query = $cnn->prepare($sql);
          $query->bindParam(':dia', $day);
          $query->bindParam(':mes', $month);
          $query->bindParam(':ano', $year);
          $query->bindParam(':campid', $IdCamp);
          $query->execute();
          $result = $query->fetchAll(PDO::FETCH_ASSOC);
          $cnn = NULL;
      } catch (PDOException $e) {
          $result= "Database Error: " . $e;
      }
      return $result;
    }

    function getAttendedManualCalls($IdCamp) {
        $sql = "SELECT ominicontacto_app_queuelog.id, ominicontacto_app_queuelog.time,
                       ominicontacto_app_queuelog.callid, ominicontacto_app_queuelog.queuename,
                       ominicontacto_app_queuelog.campana_id, ominicontacto_app_queuelog.agent,
                       ominicontacto_app_queuelog.agent_id, ominicontacto_app_queuelog.event,
                       ominicontacto_app_queuelog.data1, ominicontacto_app_queuelog.data2,
                       ominicontacto_app_queuelog.data3, ominicontacto_app_queuelog.data4,
                       ominicontacto_app_queuelog.data5
                FROM ominicontacto_app_queuelog
                WHERE (ominicontacto_app_queuelog.event IN (CONNECT)
                AND ominicontacto_app_queuelog.campana_id = :campid
                AND EXTRACT(DAY from ominicontacto_app_queuelog.time) = :dia
                AND EXTRACT(MONTH from ominicontacto_app_queuelog.time) = :mes
                AND EXTRACT(YEAR from ominicontacto_app_queuelog.time) = :ano
                AND ominicontacto_app_queuelog.data4 = 'saliente')
                ORDER BY ominicontacto_app_queuelog.time DESC";
        $day = date("d");
        $month = date("m");
        $year = date("Y");
        try {
            $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
            $query = $cnn->prepare($sql);
            $query->bindParam(':dia', $day);
            $query->bindParam(':mes', $month);
            $query->bindParam(':ano', $year);
            $query->bindParam(':campid', $IdCamp);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $cnn = NULL;
        } catch (PDOException $e) {
            $result= "Database Error: " . $e;
        }
        return $result;
    }

    function getNotAttendedManualCalls($IdCamp) {
         $sql = "SELECT ominicontacto_app_queuelog.id, ominicontacto_app_queuelog.time,
                       ominicontacto_app_queuelog.callid, ominicontacto_app_queuelog.queuename,
                       ominicontacto_app_queuelog.campana_id, ominicontacto_app_queuelog.agent,
                       ominicontacto_app_queuelog.agent_id, ominicontacto_app_queuelog.event,
                       ominicontacto_app_queuelog.data1, ominicontacto_app_queuelog.data2,
                       ominicontacto_app_queuelog.data3, ominicontacto_app_queuelog.data4,
                       ominicontacto_app_queuelog.data5
                       FROM ominicontacto_app_queuelog
                       WHERE (ominicontacto_app_queuelog.event IN (ABANDON)
                       AND ominicontacto_app_queuelog.campana_id = :campid
                       AND EXTRACT(DAY from ominicontacto_app_queuelog.time) = :dia
                       AND EXTRACT(MONTH from ominicontacto_app_queuelog.time) = :mes
                       AND EXTRACT(YEAR from ominicontacto_app_queuelog.time) = :ano
                       AND ominicontacto_app_queuelog.data4 = 'saliente')
                       ORDER BY ominicontacto_app_queuelog.time DESC";
         $day = date("d");
         $month = date("m");
         $year = date("Y");
         try {
             $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
             $query = $cnn->prepare($sql);
             $query->bindParam(':dia', $day);
             $query->bindParam(':mes', $month);
             $query->bindParam(':ano', $year);
             $query->bindParam(':campid', $IdCamp);
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

    function getAnswererDetected($IdCamp) {
        $sql = 'select COUNT(ominicontacto_app_wombatlog.estado) AS "answererdetected" FROM ominicontacto_app_wombatlog
                WHERE (ominicontacto_app_wombatlog.campana_id = :campid
                AND ominicontacto_app_wombatlog.calificacion LIKE \'CONTESTADOR\')
                AND EXTRACT(DAY from ominicontacto_app_wombatlog.fecha_hora) = :dia
                AND EXTRACT(MONTH from ominicontacto_app_wombatlog.fecha_hora) = :mes
                AND EXTRACT(YEAR from ominicontacto_app_wombatlog.fecha_hora) = :ano';
        $day = date("d");
        $month = date("m");
        $year = date("Y");
        try {
            $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
            $query = $cnn->prepare($sql);
            $query->bindParam(':dia', $day);
            $query->bindParam(':mes', $month);
            $query->bindParam(':ano', $year);
            $query->bindParam(':campid', $IdCamp);
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
        $sql = "SELECT * FROM wombat.hop_calls, wombat.call_records B, wombat.call_lists C
                WHERE callId = cr AND B.listId = C.listId AND name = :nomcamp ";
        try {
            $cnn = new PDO($this->argPdo, PG_USER, PG_PASSWORD);
            $query = $cnn->prepare($sql);
            $query->bindParam(':nomcamp', $CampName);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $cnn = NULL;
        } catch (PDOException $e) {
            $result= "Database Error: " . $e;
        }
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
