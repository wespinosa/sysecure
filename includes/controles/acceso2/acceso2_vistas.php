<?php

session_start();

date_default_timezone_set('America/Bogota');

//include_once PATH_PROD.SISTEM_NAME.'/includes/conexiones/db_local.inc.php';
//
//$dbmysql = new database();



function frm_revisarAcceso2() {
    $retval = '';
    $retval = '<article class="col-xs-12 col-sm-12 col-md-12 col-lg-10">
                <div class="botonesSuperiores">
                </div>
                <div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false">
                    <header>
                            <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                            <h2>Listado de PPL </h2>

                    </header>
                    <div>
                        <div class="widget-body no-padding">
                            <table id="listaAcceso2" class="table table-striped table-bordered table-hover" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><i class="fa fa-fw fa-male txt-color-blue hidden-md hidden-sm hidden-xs"></i> Nombre visitante</th>
                                        <th><i class="fa fa-fw fa-user txt-color-blue hidden-md hidden-sm hidden-xs"></i> Cédula visitante</th>
                                        <th><i class="fa fa-fw fa-lock txt-color-blue hidden-md hidden-sm hidden-xs"></i> Nombre del PPL</th>
                                        <th><i class="fa fa-fw fa-lock txt-color-blue hidden-md hidden-sm hidden-xs"></i> Estado</th>
                                       <th><i class="fa fa-fw fa-map-marker txt-color-blue hidden-md hidden-sm hidden-xs"></i>Accion</th>


                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
            </div>
        </article>';
   $retval .=frmAccesoVisitante();
//    $retval .=frmCentros();
    return $retval;
}


function frmAccesoVisitante() {

    $retval = '';
    $retval = '<div class="modal fade" id="frmVisitanteModal" tabindex="-1" role="dialog" aria-labelledby="PagoModalLabel" aria-hidden="true">
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
                                                        <form id="form-visitante" class="smart-form" action="javascript:guardarVisitante(2)">
                                                            <header>
                                                                    Formulario de Registro
                                                            </header>
                                                            <fieldset>
                                                                    <input type="hidden" id="IDvisitante" name="IDvisitante">
                                                                    <input type="hidden" id="IDvisPpl" name="IDvisPpl">
                                                            </fieldset>
                                                            <fieldset>
                                                                <div class="row">
                                                                    <section class="col-md-2" style="width:50%;text-align:center;" >
                                                                        <label class="input">Foto:</label>
                                                                        <div id="my_camera" ></div>
                                                                        <a class="btn btn-danger btn-xs 4 eliminaParticipante" href="javascript:tomarFoto()"><i class="fa fa fa-camera"></i> Tomar Foto</a>
                                                                        <script type="text/javascript" src="js/webcam.js"></script>
                                                                        <input type="hidden" id="codeImage" name="codeImage">
                                                                    </section>
                                                                    <section class="col-md-2" style="width:50%;text-align:center;">
                                                                        <label class="input">Foto Actual:</label>
                                                                        <div id="my_result"></div> 
                                                                    </section>
                                                                </div>    
                                                                <div class="row">
                                                                    <section class="col col-6">
                                                                            <label>Número de Cédula:</label>
                                                                            <label class="input">
                                                                                    <input type="telf" id="cedula" name="cedula" placeholder="Número de Cédula">
                                                                            </label>
                                                                    </section>
                                                                    <section class="col col-6">
                                                                            <label>Parentesco:</label>
                                                                            <label class="select">
                                                                                    <select id="parentesco" name="parentesco" class="form-control">
                                                                                        <option value="0"> -- Seleccione --</option>
                                                                                        '.parentesco().'
                                                                                    </select>
                                                                            </label>
                                                                    </section>
                                                                </div>
                                                                    <section id="encuentraCedula">
                                                                        <div class="alert alert-success fade in">
                                                                            <button class="close" data-dismiss="alert"> × </button>
                                                                            <i class="fa-fw fa fa-check"></i>
                                                                            <strong>Satisfactorio</strong>
                                                                            El número de Cédula si existe con los siguientes datos:
                                                                        </div>
                                                                    </section>
                                                                <div class="row">
                                                                    <section class="col col-6">
                                                                            <label>Nombre:</label>
                                                                            <label class="input">
                                                                                    <input type="text" id="nombre" name="nombre" placeholder="Nombre">
                                                                            </label>
                                                                    </section>
                                                                    <section class="col col-6">
                                                                            <label>Apellido:</label>
                                                                            <label class="input">
                                                                                    <input type="text" id="apellido" name="apellido" placeholder="Apellido">
                                                                            </label>
                                                                    </section>
                                                                </div>

                                                                <div class="row">
                                                                    
                                                                    <section class="col col-6">
                                                                        <label>Teléfono:</label>
                                                                        <label class="input">
                                                                            <input type="telf" id="telefono" name="telefono" placeholder="Teléfono Celular">
                                                                        </label>
                                                                    </section>
                                                                    <section class="col col-6">
                                                                            <label class="input">
                                                                                    <label>Correo Electrónico:</label>
                                                                                    <input type="telf" id="correo" name="correo" placeholder="Correo Electrónico" style="text-transform:lowercase;">
                                                                            </label>
                                                                    </section>
                                                                </div>     
                                                                <div class="row">
                                                                    <section class="col col-6">
                                                                            <label>Dirección:</label>
                                                                            <label class="input">
                                                                                    <input type="telf" id="direccion" name="direccion" placeholder="Direccion" style=" width: 527px; ">
                                                                            </label>
                                                                    </section>
                                                                </div>    
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

function parentesco(){
    global $dbmysql;
    $retval = '';
    $sql = "SELECT * FROM `sys_parentesco`;";
    $val = $dbmysql->query($sql);
    if ($val->num_rows > 0) {
        while ($row = $val->fetch_object()) {
            $retval.='<option value="' . $row->PAR_COD . '">' . $row->PAR_DESCRIPCION . '</option>';
        }
    }
    return $retval;
}
