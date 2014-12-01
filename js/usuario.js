$(document).ready(function() {
    recargarUsuario();
    validarContrasena();
    $.validator.addMethod("validacionClave", function(value, element) {
        return  /(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/.test(value);
    });
    var $registerForm = $("#smart-form-register").validate({
        rules: {
            usuario: {
                required: true,
                minlength: 6
            },
            email: {
                required: true,
                email: true
            },
            password: {
                required: true,
                minlength: 8,
                maxlength: 20,
                validacionClave:true
            },
            passwordConfirm: {
                required: true,
                minlength: 8,
                maxlength: 20,
                equalTo: '#password'
            },
            nombre: {required: true},
            apellido: {required: true},
            cedula: {required: true},
            tipoUsuario: {required: true},
            centro: {required: true}
        }, 
        messages: {
            password: {
                validacionClave:'La contraseñas debe contener al menos una letra mayúscula(A), al menos una letra minúscula(b), '
                                +'al menos un número o caracter especial(1*), longitud mínima de 8 caracteres, '
            }
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element.parent());
        }
    });
    $("#cambioClave").validate({
        rules: {
            password2: {
                required: true,
                minlength: 8,
                maxlength: 20,
                validacionClave:true
            },
            passwordConfirm2: {
                required: true,
                minlength: 8,
                maxlength: 20,
                equalTo: '#password2'
            }
        },
        messages: {
            password2: {
                validacionClave:'La contraseñas debe contener al menos una letra mayúscula(A), al menos una letra minúscula(b), '
                                +'al menos un número o caracter especial(1*), longitud mínima de 8 caracteres, '
            }
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element.parent());
        }
    });
    $("#cedula").validarCedulaEC({
        onValid: function () {
            $(this).parent('label').removeClass('state-error'); 
            $(this).parent('label').addClass('state-success'); 
            $(this).parent('label').parent('section').children("em").remove();
            $("button[type=submit]").removeAttr("disabled");
        },
        onInvalid: function () {
            $(this).parent('label').removeClass('state-success');
            $(this).parent('label').addClass('state-error');  
            $(this).parent('label').siblings("em").remove();
            $(this).parent('label').parent('section').append('<em class="invalid" for="cedula">Cédula o Ruc no Valido</em>');
            $("button[type=submit]").attr("disabled", "disabled");
        }
    });
});
function recargarUsuario() {
    var dtTable = $('#listaUsuarios').dataTable({
        "bDestroy": true,
        "bProcessing": true,
        "bRetrieve": true,
        "bStateSave": true,
        "bPaginate": true,
        "bServerSide": true,
        "sAjaxSource": "includes/usuario/Usuarios_dataTable.php",
        "oLanguage": {
            "sEmptyTable": "No hay datos disponibles en la tabla",
            "sInfo": "Existen _TOTAL_ registros en total, mostrando (_START_ a _END_)",
            "sInfoEmpty": "No hay entradas para mostrar",
            "sInfoFiltered": " - Filtrado de registros _MAX_",
            "sZeroRecords": "No hay registros que mostrar"
        }
    });
        dtTable.fnReloadAjax();
}
function validarContrasena(){
    $('input[type=password]').keyup(function() {
        var pswd = $(this).val();
        if ( pswd.length < 8 ) {
            $('#length').removeClass('valid').addClass('invalid');
        } else {
            $('#length').removeClass('invalid').addClass('valid');
        }
        //validate letter
        if ( pswd.match(/[A-z]/) ) {
            $('#letter').removeClass('invalid').addClass('valid');
        } else {
            $('#letter').removeClass('valid').addClass('invalid');
        }
        //validate capital letter
        if ( pswd.match(/[A-Z]/) ) {
            $('#capital').removeClass('invalid').addClass('valid');
        } else {
            $('#capital').removeClass('valid').addClass('invalid');
        }
        //validate number
        if ( pswd.match(/\d/) ) {
            $('#number').removeClass('invalid').addClass('valid');
        } else {
            $('#number').removeClass('valid').addClass('invalid');
        }
    }).focus(function() {
        $('#pswd_info').show();
    }).blur(function() {
        $('#pswd_info').hide();
    });
}
function cambiarClaveUsuario(usuario) {
    limpiarFormularioUsuario();
    $('#frmCambioClaveModal').modal('show');
    $('#IDuser').val(usuario);
}
function cambiarClave(usuario) {
    $('#frmClaveModal').modal('show');
    $('#IDuser').val(usuario);
}

function GuardarCambioClaveUsuario() {
    $.SmartMessageBox({
        title: "Confirmación!",
        content: "Esta seguro de cambiar la Contraseña del Usuario?",
        buttons: '[No][Si]'
    }, function(ButtonPressed) {
        if (ButtonPressed === "Si") {
            var clave = $('#password2').val();
            var codPar = $('#IDuser').val();
            $.ajax({
                url: "./includes/usuario/Usuarios_model.php?opcion=cambioClaveUsuario",
                type: 'post',
                data: {codigo: codPar, clave: clave},
                success: function(respuesta) {
                    if (respuesta === '1') {
                        $.smallBox({
                            title: 'Actualización',
                            content: "<i class='fa fa-clock-o'></i> <i>Usuario Actualizado su Clave...</i>",
                            color: "#659265",
                            iconSmall: "fa fa-check fa-2x fadeInRight animated",
                            timeout: 4000
                        });
                        limpiarFormularioUsuario();
                        $('#frmCambioClaveModal').modal('hide');
                        recargarUsuario();
                    }
                }
            });
        }
    });
}
function editarUsuario(usuario) {
    var url = './includes/usuario/Usuarios_model.php?opcion=enviarDatosUsuario';
    $.ajax({
        url: url,
        datetype: "json",
        type: 'POST',
        data: {codigoUsu: usuario},
        success: function(res) {
            var json_obj = $.parseJSON(res);
            limpiarFormularioUsuario();
            carga_DatosIncialesUsuarios(json_obj);
            $('#frmUsuarioModal').modal('show');
            $("#centro").hide();
            $("#password").hide();
            $("#passwordConfirm").hide();
            $('#smart-form-register >header').text('Actualización de Datos Usuario')
            $('#IDuser').val(usuario);
             
        }
    });

}
function guardarUsuario() {
    var usuario = $('#IDuser').val();
    if (usuario === '') {
        $.ajax({
            url: './includes/usuario/Usuarios_model.php?opcion=guardaDatosUsuario',
            datetype: "json",
            type: 'POST',
            data: $("#smart-form-register").serialize(),
            success: function(res) {
                if (res === '1') {
                    $.smallBox({
                        title: "Usuario Almacenado",
                        content: "<i class='fa fa-clock-o'></i> <i>Usuario Agregado correctamente...</i>",
                        color: "#659265",
                        iconSmall: "fa fa-check fa-2x fadeInRight animated",
                        timeout: 4000
                    });
                    limpiarFormularioUsuario();
                    $('#frmCambioClaveModal').modal('hide');
                    recargarUsuario();
                }
            }
        });
    } else {
        $.ajax({
            url: './includes/usuario/Usuarios_model.php?opcion=actualizarDatosUsuario',
            datetype: "json",
            type: 'POST',
            data: $("#smart-form-register").serialize(),
            success: function(res) {
                if (res === '1') {
                    $.smallBox({
                        title: "Actualización",
                        content: "<i class='fa fa-clock-o'></i> <i>Usuario Actualizado correctamente...</i>",
                        color: "#659265",
                        iconSmall: "fa fa-check fa-2x fadeInRight animated",
                        timeout: 4000
                    });
                    limpiarFormularioUsuario();
                    location.reload();
                }
            }
        });
    }
    $('#frmUsuarioModal').modal('hide');
}
function nuevoUsuario() {
    limpiarFormularioUsuario();
    $('#frmUsuarioModal').modal('show');
    $("#centro").show();
    $("#password").show();
    $("#passwordConfirm").show();
    $('#smart-form-register >header').text('Registro Nuevo Usuario')
    $('#IDuser').val('');
}
function eliminarUsuario(codPar, nomCod) {

    $.SmartMessageBox({
        title: "Confirmación!",
        content: "Esta seguro de eliminar al Usuario de <span class='txt-color-orangeDark'><strong>" + nomCod + " </strong></span>?",
        buttons: '[No][Si]'
    }, function(ButtonPressed) {
        if (ButtonPressed === "Si") {
            $.ajax({
                url: "./includes/usuario/Usuarios_model.php?opcion=eliminarUsuario",
                type: 'post',
                data: {codigo: codPar},
                success: function(respuesta) {
                    if (respuesta === '1') {
                        $('.' + codPar).parent('td').parent('tr').addClass('paraEliminarUsuario');
                        $('.paraEliminarUsuario').fadeOut('tr');
                        $.smallBox({
                            title: nomCod,
                            content: "<i class='fa fa-clock-o'></i> <i>Usuario Eliminado...</i>",
                            color: "#659265",
                            iconSmall: "fa fa-check fa-2x fadeInRight animated",
                            timeout: 4000
                        });
                    }
                }
            });

        }
        if (ButtonPressed === "No") {
        }
    });
}
function limpiarFormularioUsuario() {
    $("#nombre").val('');  /*Nombre*/
    $("#apellido").val('');  /*Apellido*/
    $("#usuario").val('');  /*Usuario*/
    $("#password").val('');  /*Usuario*/
    $("#passwordConfirm").val('');  /*Usuario*/
    $("#email").val('');  /*E-Mail*/
    $("#celular").val('');/*Celular*/
    $("#cedula").val('');/*Cedula*/
    $('#tipoUsuario').prop('selectedIndex', 0);/*Tipo de Usuario*/
    $('#centro').prop('selectedIndex', 0);/*Centro*/
}
function carga_DatosIncialesUsuarios(edt) {
    $("#nombre").val(edt.datosUsuario.USU_NOMBRE);  /*Nombre*/
    $("#apellido").val(edt.datosUsuario.USU_APELLIDO);  /*Apellido*/
    $("#usuario").val(edt.datosUsuario.USU_USUARIO);  /*Usuario*/
    $("#email").val(edt.datosUsuario.USU_EMAIL);  /*E-Mail*/
    $("#celular").val(edt.datosUsuario.USU_CELULAR);/*Celular*/
    $("#cedula").val(edt.datosUsuario.USU_CEDULA);/*Celular*/
//    $('#tipoUsuario option[value="' + edt.datosUsuario.ROL_COD + '"]').attr("selected", true);/*Tipo de Usuario*/
    $('#tipoUsuario').prop('selectedIndex', edt.datosUsuario.ROL_COD);
}
function revisarCentrosDisponibles(codUsuario){
    $.ajax({
            url: "./includes/usuario/Usuarios_model.php?opcion=obtenerCentrosListado",
            type: 'post',
            data: {codUsuario: codUsuario},
            success: function(respuesta) {
                $('#listaCentrosActivos >tbody').html(respuesta);
                $.ajax({
                    url: "./includes/usuario/Usuarios_model.php?opcion=obtenerCentrosMenu",
                    type: 'post',
                    data: {codUsuario: codUsuario},
                    success: function(res) {
                        $('#listaCentrosOpciones').html(res);
                        $('#frmCentrosDisponibles').modal('show');
                    }
                });
            }
        });
     
}
function agregaCentroTabla(codCentro,codUsuario){
     $.ajax({
            url: "./includes/usuario/Usuarios_model.php?opcion=agregaCentrosTabla",
            type: 'post',
            data: {codCentro: codCentro,codUsuario:codUsuario},
            success: function(respuesta) {
               $('#listaCentrosActivos >tbody').html(respuesta);
            }
        });
}