$(document).ready(function() {
    // Date Range Picker
    $("#fdesde").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        dateFormat:"yy-mm-dd"
        
    });
    $("#fhasta").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        dateFormat:"yy-mm-dd"
    });
});

function reporteSancion(){
    var fdesde=$('#fdesde').val();
    var fhasta=$('#fhasta').val();
    $.ajax({
            url: './includes/reportes/sanciones/Rsanciones_model.php?opcion=reporteSancion',
            datetype: "json",
            type: 'POST',
            data: {fdesde:fdesde,fhasta:fhasta},
            success: function(res) {
                    $('#muestraReporteSancion').html(res);
            }
        });
}