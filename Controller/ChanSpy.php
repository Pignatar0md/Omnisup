<?php

include $_SERVER['DOCUMENT_ROOT'] . '/Omnisup/config.php';
include controllers . '/Agente.php';

session_start();
$_SESSION['exten'] = 1003;
if (isset($_GET['sip']) && isset($_SESSION['exten'])) {
    $Controller_Agente = new Agente();
    $res = $Controller_Agente->espiarAgente($_GET['sip'], $_SESSION['exten']);
    echo $res;
}
header('location: ../index.php?page=Lista_Agentes');
