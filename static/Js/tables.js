var tabagt;
$(function () {
  $('#tableCamp').DataTable({
    searching: false,
    bLengthChange: false,
    "language": {
      "info": "pagina _PAGE_ de _PAGES_",
      "paginate": {
        "first":      "Primero",
        "last":       "Ultimo",
        "next":       "Siguiente",
        "previous":   "Anterior"
      }
    }
  });

  tabagt = $('#tableAgt').DataTable({
    columns: [
        {data: 'agente'},
        {data: 'estado'},
        {data: 'tiempo'},
        {data: 'acciones'},
    ],
    ordering: false,
    searching: false,
    bLengthChange: false,
    language: {
      "info": "pagina _PAGE_ de _PAGES_",
      "paginate": {
        "first":      "Primero",
        "last":       "Ultimo",
        "next":       "Siguiente",
        "previous":   "Anterior"
      }
    }
  });
  if(window.href='index.php?page=Detalle_Campana') {
    setInterval("actualiza_contenido()", 1000);
  }
});
function actualiza_contenido() {
  var nomcamp = $("#nombreCamp").html();
  $.ajax({
    url: 'Controller/Detalle_Campana_Contenido.php',
    type: 'GET',
    dataType: 'html',
    data: 'nomcamp='+nomcamp,
    success: function (msg) {
      var mje = JSON.parse(msg);
      tabagt.rows().remove().draw();
      tabagt.rows.add(mje).draw();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log("Error al ejecutar => " + textStatus + " - " + errorThrown);
    }
  });
}
