<?php
session_start();
	/*
	 * Script:    DataTables server-side script for PHP and MySQL
	 * Copyright: 2014 - Willian Espinosa - Edicion Especial
	 */	

include_once '../conexiones/db_local.inc.php';
    $dbmysql = new database();
    date_default_timezone_set('America/Bogota');
include_once( '../conexiones/config_local.ini.php' );
global $dbmysql;
        $aColumns = array('PPL_COD','PAB_COD','CEL_COD','PPL_NOMBRE','PPL_APELLIDO','PPL_CEDULA','PPL_IMG','PPL_ESTADO');
	/* Campo de Index */
	$sIndexColumn = "PPL_COD";
	/* Tabla a Usar */
	$sTable =  "sys_ppl";
        /* Conexion a la Base */
	$gaSql['link'] =  mysql_pconnect( HOST_NAME, USER_NAME, USER_PASSWD) or
		die( 'Could not open connection to server' );
            mysql_select_db( DB_NAME, $gaSql['link'] ) or 
		die( 'Could not select database'. DB_NAME );
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	/*
	 * Ordenamiento
	 */
//	if ( isset( $_GET['iSortCol_0'] ) )
//	{
//		$sOrder = "ORDER BY  ";
                $sOrder = "ORDER BY PPL_COD DESC";
                
//		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
//		{
//			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
//			{
//				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
//				 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
//                                
//			}
//		}
////		
//		$sOrder = substr_replace( $sOrder, "", -2 );
//		if ( $sOrder == "ORDER BY PPL_COD asc" )
//		{
//			$sOrder = "ORDER BY PPL_COD DESC";
//		}
//	}
//        
	/* 
	 * Filtering
	 */
	$sWhere = "";
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= '  )';
	}
	
	/*
	 * SQL queries
	 * Get data to display
	 */ 
		$condicion =
        $sWhere=($sWhere=='')?" WHERE PPL_ESTADO='A' ":$sWhere." AND PPL_ESTADO='A' ";
        
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , "," ", implode(",", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit";
	$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
//	echo $sQuery;
	/* Data set length after filtering */
	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
        /***********************************/
        
            $sQuery = "SELECT COUNT(".$sIndexColumn.")
                        FROM   $sTable 
                        where PAB_COD in (SELECT PAB_COD FROM `sys_pabellones` WHERE CEN_COD ={$_SESSION['usu_centro_cod']});";
            $rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
            $aResultTotal = mysql_fetch_array($rResultTotal);
            $iTotal = $aResultTotal[0];
            $output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iTotal,
		"aaData" => array());
	/*
	 * Output
	 */
        $i=0;
        
	while ( $aRow = mysql_fetch_array( $rResult ) ){
//            Consulta descripcion de Pabellon
            $sql1 = "SELECT PAB_DESCRIPCION, CEN_COD FROM `sys_pabellones` WHERE PAB_COD = {$aRow[ 'PAB_COD' ]};";
            $val1 = $dbmysql->query($sql1);
            $row = $val1->fetch_object();

            if($row->CEN_COD == $_SESSION['usu_centro_cod'])
            {	
            //Consulta Pabellon Anterior
            $sql2 = "SELECT h.*,p.PAB_DESCRIPCION FROM sys_historia_ppl h,`sys_pabellones` p WHERE h.PAB_COD=p.PAB_COD AND h.PPL_COD = {$aRow[ 'PPL_COD' ]} ORDER BY h.`HIS_COD` DESC LIMIT 0,1";
            $val2 = $dbmysql->query($sql2);
            $row2 = $val2->fetch_object();
                /* General output */
                    $img=($aRow[ 'PPL_IMG' ]!='')?''.SISTEM_NAME.PATH_PPL.$aRow[ 'PPL_IMG' ]:SISTEM_NAME.'/img/avatars/male.png';
                    $nombre=$aRow[ 'PPL_NOMBRE' ].' '.$aRow[ 'PPL_APELLIDO' ];
                    $cadenaParametros=utf8_encode($aRow[ 'PPL_COD' ].','."'$nombre'");
                    $output['aaData'][] =array( ''.utf8_encode($aRow[ 'PPL_COD' ]).'',
                                                ''.utf8_encode($nombre).'',
                                                '<img src="/'.$img.'" class="img-thumbnail" style="width: 60px">',
                                                '<div style="text-align:center;"><span class="badge bg-color-blue">'.$row->PAB_DESCRIPCION.'</span></div>',
                                                '<a class="btn btn-success btn-xs" title="Visitantes Habilitados" href="javascript:revisarVisitantesDisponibles('.$aRow[ 'PPL_COD' ].')">
                                                    <i class="fa fa-group"></i>
                                                </a>
                                                <a class="btn btn-success btn-xs" title="Editar Usuario" href="javascript:editarPpl('.$aRow[ 'PPL_COD' ].')">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a class="btn btn-danger btn-xs '.$aRow[ 'PPL_COD' ].' eliminaParticipante" title="Anular PPL" href="javascript:eliminarPpl('.$cadenaParametros.')">
                                                    <i class="fa fa-trash-o"></i>
                                                </a>
                                                <a class="btn btn-warning btn-xs '.$aRow[ 'PPL_COD' ].'" title="Traspaso de PPL" href="javascript:aplicarTraspaso('.$aRow[ 'PAB_COD'].','.$aRow[ 'PPL_COD' ].')">
                                                    <i class="fa fa fa-exchange"></i>  Traspaso
                                                </a>');
			}

        }
//        print_r($output);
	echo json_encode( $output );
?>