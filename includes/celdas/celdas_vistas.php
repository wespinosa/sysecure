<?php
session_start();
include_once PATH_PROD.SISTEM_NAME.'/includes/generales.php';
$clGeneral = new general();
include_once PATH_PROD.SISTEM_NAME.'/includes/conexiones/db_local.inc.php';
$dbmysql = new database();
date_default_timezone_set('America/Bogota');
$funcion = isset($_GET['funcion']) ? $_GET['funcion'] : 'ninguno';
switch ($funcion) {
    case 'consultadatosVisitante':
        consultadatosVisitante();
        break;
}

function reporte_celdas() {
    global $dbmysql,$clGeneral;
    $sql="SELECT p.*,a.*,pi.*,c.* FROM sys_celdas c,`sys_pisos` pi,sys_pabellones p, sys_alas a WHERE c.PIS_COD=pi.`PIS_COD` AND pi.PAB_COD=a.PAB_COD AND pi.`PAB_COD`=p.`PAB_COD` AND pi.CEN_COD={$_SESSION['usu_centro_cod']}";
     $val_s = $dbmysql->query($sql);
        $retval = '<article class="col-sm-12 col-md-12 col-lg-8">
                    <div class="botonesSuperiores">
                    <fieldset>
                            <button id="agregarPabellon" class="btn btn-labeled btn-primary btn-personal"  data-toggle="modal" onclick="javascript:nuevoCelda()">
                                <span class="btn-label"><i class="glyphicon glyphicon-plus"></i></span>
                                Agregar Celda
                            </button>
                        </fieldset>
                    </div>
                    <div class="jarviswidget jarviswidget-color-darken" id="wid-id-2" data-widget-editbutton="false">
                            <header>
                                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                                    <h2>Celdas del Centro '.$_SESSION["usu_centro_descrip"].' </h2>
                            </header>
                            <div>
                                    <div class="jarviswidget-editbox">
                                    </div>
                                    <div class="widget-body no-padding">
                                            <div class="table-responsive">
                                                    <table id="tablaEtapas" class="table table-bordered table-striped table-hover">
                                                            <thead>
                                                                    <tr>
                                                                            <th>#</th>
                                                                            <th>Pabellón</th>
                                                                            <th>Ala</th>
                                                                            <th>Piso</th>
                                                                            <th>Descripción</th>
                                                                            <th>Acción</th>
                                                                    </tr>
                                                            </thead>
                                                            <tbody>';
                                                    while($row = $val_s->fetch_object()){
                                                        $cadenaParametros=utf8_encode($row->CEL_COD.','."'$row->CEL_DESCRIPCION'");
                                                         $retval .= '<tr>
                                                                            <td>'.$row->CEL_COD.'</td>
                                                                            <td>'.$row->PAB_DESCRIPCION.'</td>
                                                                            <td>'.$row->ALA_DESCRIPCION.'</td>
                                                                            <td>'.$row->PIS_DESCRIPCION.'</td>
                                                                            <td>'.$row->CEL_DESCRIPCION.'</td>
                                                                            <td>
                                                                                <a class="btn btn-success btn-xs" title="Editar Celdas" href="javascript:editarCeldas('.$row->CEL_COD.')">
                                                                                    <i class="fa fa-pencil"></i>
                                                                                </a>
                                                                                <a class="btn btn-danger btn-xs '.$row->CEL_COD.'" title="Eliminar Celdas" href="javascript:eliminarCeldas('.$cadenaParametros.')">
                                                                                    <i class="fa fa-trash-o"></i>
                                                                                </a>
                                                                            </td>    
                                                                    </tr>';
                                                    }              
                                            $retval .= '</tbody>
                                                    </table>
                                            </div>
                                    </div>
                            </div>
                    </div>
            </article>';
      $retval .= frmCeldas();         
    return $retval;
}
function frmCeldas() {
    global $clGeneral;
    $retval = '';
    $retval = '<div class="modal fade" id="frmCeldasModal" tabindex="-1" role="dialog" aria-labelledby="PagoModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                        &times;
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="jarviswidget jarviswidget-sortable" id="wid-id-4" data-widget-editbutton="false" data-widget-custombutton="false">
                                                <header>
                                                        <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                                                        <h2>Formulario de Registro </h2>				
                                                </header>
                                                <div>
                                                    <div class="widget-body no-padding">
                                                        <form id="formCeldas" class="smart-form" action="javascript:guardarCeldas()">
                                                            <header>
                                                                    Formulario de Registro
                                                            </header>
                                                            <fieldset>
                                                                <input type="hidden" id="IDceldas" name="IDceldas">
                                                                <section class="col col-6">
                                                                    <label class="label">Pabellón</label>
                                                                    <label class="select">
                                                                            <select id="pabellon" name="pabellon" onchange="llenaAla();">
                                                                                    <option value="" selected="" disabled="">-- Pabellon --</option>
                                                                                    ' . $clGeneral->comboPabellon() . '
                                                                            </select> <i></i> 
                                                                    </label>
                                                                </section>
                                                                <section class="col col-6">
                                                                    <label class="label">Ala</label>
                                                                    <label class="select" id="comboAla">
                                                                        <select id="ala" name="ala">
                                                                            <option value="" selected="" disabled="">-- Ala --</option>
                                                                        </select>
                                                                    </label>
                                                                </section>
                                                                <section class="col col-6">
                                                                    <label class="label">Piso</label>
                                                                    <label class="select" id="comboPiso">
                                                                        <select id="piso" name="piso">
                                                                            <option value="" selected="" disabled="">-- Piso --</option>
                                                                        </select>
                                                                    </label>
                                                                </section>
                                                                <section class="col col-6">
                                                                        <label class="label">Descripción de la Celda</label>
                                                                        <label class="input"> <i class="icon-append fa fa-user"></i>
                                                                            <input type="text" id="descripcion" name="descripcion" placeholder="Descripción de la Celda">
                                                                            <b class="tooltip tooltip-bottom-right">Ingrese la descripción de la Celda</b> 
                                                                        </label>
                                                                </section>
                                                            </fieldset>
                                                            <footer>
                                                                    <button type="submit" class="btn btn-primary">
                                                                            Registrar
                                                                    </button>
                                                            </footer>
                                                        </form>						
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            
                        </div>
                    </div>
                </div>';
    return $retval;
}
