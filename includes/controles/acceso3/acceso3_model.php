<?php

session_start();
include_once '../../generales.php';
$clGeneral = new general();
include_once '../../conexiones/db_local.inc.php';
$dbmysql = new database();
date_default_timezone_set('America/Bogota');
$funcion = isset($_GET['opcion']) ? $_GET['opcion'] : 'ninguno';
switch ($funcion) {
    case 'permitirAcceso3':
        permitirAcceso3();
        break;

    case 'bloquearAceeso3':
        bloquearAceeso3();
        break;
}

function permitirAcceso3() {
    global $dbmysql;
    $fecha = date('Y-m-d');
    $codVisita = $_POST['codigo'];
    $sql2 = "INSERT INTO `sys_control` (`GAR_COD` ,`VIP_COD` ,`CON_FECHA` ,`CON_ESTADO`)VALUES ('3', '$codVisita','$fecha','A');";
//    $val2 = $dbmysql->query($sql2);
    $poscedula=obtenerPosicioncedula();
    
//    if ($val2) {echo 1;} else {echo 0;}
}

function obtenerPosicioncedula(){
    global $dbmysql,$clGeneral;
    $fecha = date('Y-m-d');
    $sql2 = "SELECT MAX(`VISG_POSCHAR`) AS MAXLITERAL,MAX(`VISG_POSNUM`) AS MAXSECUENCIAL FROM `sys_visitas` WHERE `VISG_FECHA` ='$fecha' AND `VISG_ESTADO` ='A';";
    $val2 = $dbmysql->query($sql2);
    $row = $val2->fetch_object();
    $maxLiteral=$row->MAXLITERAL;
    $maxSecuencial=$row->MAXSECUENCIAL;
    echo $valParametro=$clGeneral->obtenerValorParametro(3);
    if($maxSecuencial==$valParametro){
        $maxSecuencial=1;
        $maxLiteral=($maxLiteral=='Z')?$maxLiteral='A':++$maxLiteral;
    }else{
        $maxSecuencial=$maxSecuencial+1;
        ++$maxLiteral;
    }
    exit;
    return $maxLiteral.$maxSecuencial;
}

function bloquearAceeso3() {
    global $dbmysql;
    $fecha = date('Y-m-d');
    $codVisita = $_POST['codigo'];
    $codVisitante = $_POST['visitante'];
    $descripcionBloqueo = $_POST['razon'];
    $usuario=$_SESSION["user_id"];
    $sql1="INSERT INTO `sys_visitante_sancion` (
            `VIS_COD` ,
            `SAN_COD` ,
            `USU_COD` ,
            `VSA_NOTA`,
            `VSA_FECHA_INI`
            )VALUES ('$codVisitante', 1,'$usuario', '$descripcionBloqueo','$fecha');";
    $val1 = $dbmysql->query($sql1);
    
    $sql2 = "INSERT INTO `sys_control` (`GAR_COD` ,`VIP_COD` ,`CON_FECHA` ,`CON_ESTADO`)VALUES ('3', '$codVisita','$fecha','S');";
    $val2 = $dbmysql->query($sql2);

    $sql3 = "UPDATE `sys_visitante` SET `VIS_ESTADO` ='S' WHERE VIS_COD=$codVisitante;";
    $val3 = $dbmysql->query($sql3);
    if ($val2 and $val2 and $val3) {echo 1;} else {echo 0;}
}
