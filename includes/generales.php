<?php

session_start();
include_once("conexiones/db_local.inc.php");
$dbmysql = new database();
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class general {

    function inicializaMenu() {
        $activarMenu1 = '';
        $activarMenu2 = '';
        $activarMenu3 = '';
        $activarMenu31 = '';
        $activarMenu32 = '';
        $activarMenu33 = '';
        $activarMenu34 = '';
        $activarMenu35 = '';
        $activarMenu4 = '';
        $activarMenu41 = '';
        $activarMenu42 = '';
        $activarMenu43 = '';
        $activarMenu44 = '';
    }

    function registrar_acceso() {
        global $dbmysql;
        $usuario = $_SESSION["user_id"];
        $centro = $_SESSION["usu_centro_cod"];
        $fechaHora = date('Y-m-d H:i:s');
        $ip = general::obtenerIp();
        $nombreEquipo = general::obtenerNombreEquipo();
        $sql = "INSERT INTO `sys_accesos` (`USU_COD`, `CEN_COD`, `ACC_FECHA`, `ACC_IP`, `ACC_EQUIPO`)
            VALUES ('$usuario', '$centro', '$fechaHora', '$ip', '$nombreEquipo');";
        $dbmysql->query($sql);
    }

    function obtenerIp() {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"] ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];
        return $_SERVER['REMOTE_ADDR'];
    }

    function obtenerNombreEquipo() {
        return gethostname();
    }

    function obtenerPabellonPPL($codigoPpl) {
        global $dbmysql;
        $sql = "SELECT pp.*,c.*,p.*
          FROM `sys_ppl` pp, `sys_celdas` c, `sys_pabellones` p 
          WHERE pp.`CEL_COD`=c.`CEL_COD` 
          AND p.PAB_COD=c.PAB_COD 
          AND pp.`PPL_COD`=$codigoPpl";
        $val = $dbmysql->query($sql);
        if ($val->num_rows > 0) {
            $row = $val->fetch_object();
            $pabellon = $row->PAB_COD;
        }
        return $pabellon;
    }

    function obtenerHorariosPabellon($codigoPabellon) {
        global $dbmysql;
        $sql = "SELECT * FROM `sys_horarios` WHERE PAB_COD=$codigoPabellon";
        $val = $dbmysql->query($sql);
        return $val;
    }

}
