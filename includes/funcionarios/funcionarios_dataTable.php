<?php
session_start();
	/*
	 * Script:    DataTables server-side script for PHP and MySQL
	 * Copyright: 2014 - Willian Espinosa - Edicion Especial
	 */	

include_once '../../conexiones/db_local.inc.php';
    $dbmysql = new database();
    date_default_timezone_set('America/Bogota');
include_once( '../../conexiones/config_local.ini.php' );

global $dbmysql;
        $aColumns = array('VIF_COD','VIF_NOMBRE','VIF_APELLIDO','VIF_DIRECCION','VIF_TELEFONO','VIF_CORREO');
	/* Campo de Index */
	$sIndexColumn = "VIF_COD";
	/* Tabla a Usar */
	$sTable =  "sys_visitante_funcionario";
        /* Conexion a la Base */
	$gaSql['link'] =  mysql_pconnect( HOST_NAME, USER_NAME, USER_PASSWD  ) or
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
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
                
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
                                
			}
		}
//		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY USU_COD" )
		{
			$sOrder = "ORDER BY USU_COD DESC";
		}
	}
        
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
	/* Individual column filtering */
//	for ( $i=0 ; $i<count($aColumns) ; $i++ )
//	{
//		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
//		{
//			if ( $sWhere == "" )
//			{
//				$sWhere = "WHERE ";
//			}
//			else
//			{
//				$sWhere .= " AND ";
//			}
//			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
//		}
//	}
	/*
	 * SQL queries
	 * Get data to display
	 */
        
    $sWhere =($sWhere=='')?" WHERE CEN_COD = ".$_SESSION['usu_centro_cod']." ":" AND CEN_COD = ".$_SESSION['usu_centro_cod'];
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
                        FROM   $sTable";
            $rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
            $aResultTotal = mysql_fetch_array($rResultTotal);
            $iTotal = $aResultTotal[0];
            $output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array());
	/*
	 * Output
	 */
        $i=0;
        
	while ( $aRow = mysql_fetch_array( $rResult ) ){
                /* General output */
                    $nombre_visitante=utf8_encode($aRow[ 'VIS_NOMBRE' ].' '.$aRow[ 'VIS_APELLIDO' ]);
                    $nombre_ppl =$aRow[ 'PPL_NOMBRE' ].' '.$aRow[ 'PPL_APELLIDO' ];
                    $boton='<a class="btn btn-danger" title="Actualizar" href="javascript:negarAcceso3('.$aRow[ 'VIP_COD' ].','.$aRow[ 'VIS_COD' ].')"><i class="fa fa-ban"></i> Negar Acceso</a>
                            <a class="btn btn-success" title="Permitir acceso" href="javascript:permitirAcceso3(\''.$nombre_visitante.'\','.$aRow[ 'VIP_COD' ].','.$aRow[ 'PPL_COD' ].','.$aRow[ 'CON_COD' ].','.$aRow[ 'HOR_COD' ].')"><i class="fa fa-check"></i> Permitir Acceso</a>';
                    $output['aaData'][] =array( '<input type="hidden" class="codVisitante" id="cod_'.$aRow[ 'VIP_COD' ].'" name="codVisitante" value="'.$aRow[ 'VIP_COD' ].'">'.utf8_encode($aRow[ 'CON_COD' ]).'',
               	                		''.utf8_encode($nombre_visitante).'',
                                                ''.utf8_encode($aRow[ 'VIS_CEDULA' ]).'',
                                                ''.utf8_encode($nombre_ppl).'',
                                                ''.$boton.'',
                                                );
        }
//        print_r($output);
	echo json_encode( $output );
?>