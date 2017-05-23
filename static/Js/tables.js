var tabagt;
$(function () {
  /*  tabagt = $('#tableAgt').DataTable({
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
  });*/
  var url = window.location.href;
  if(url.indexOf('Detalle_Campana') !== -1) {
    //setInterval("actualiza_contenido_agt()", 1000);
    //setInterval("actualiza_contenido_camp()", 1000);
    setInterval("actualiza_contenido_colas()", 1000);
    //setInterval("actualiza_contenido_wombat()", 1000);
    //actualiza_contenido();
  }
});

function actualiza_contenido_agt() {
  var nomcamp = $("#nombreCamp").html();
  $.ajax({
    url: 'Controller/Detalle_Campana_Contenido.php',
    type: 'GET',
    dataType: 'html',
    data: 'nomcamp='+nomcamp+'&op=agstatus',
    success: function (msg) {
      if(msg!=="]") {
        var mje = JSON.parse(msg);
        tabagt.rows().remove().draw();
        tabagt.rows.add(mje).draw();
      } else {
        tabagt.rows().remove().draw();
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log("Error al ejecutar => " + textStatus + " - " + errorThrown);
    }
  });
}

function actualiza_contenido_camp() {
  var nomcamp = $("#nombreCamp").html();
  $.ajax({
    url: 'Controller/Detalle_Campana_Contenido.php',
    type: 'GET',
    dataType: 'html',
    data: 'nomcamp='+nomcamp+'&op=campstatus',
    success: function (msg) {
      if(msg!=="]") {
        var mje = JSON.parse(msg);
        $("#dialed").html(mje[0].discadas);
        $("#connected").html(mje[0].conectadas);
        $("#processed").html(mje[0].procesadas);
        $("#lost").html(mje[0].abandonadas);
        $("#busy").html(mje[0].ocupadas);
        var tabla = document.getElementById('bodyTableCampSummary');
        if($("#bodyTableCampSummary").children().length > 0) {
          while(tabla.firstChild) {
            tabla.removeChild(tabla.firstChild);
          }
        }
        for (var i = 0; i < mje[0].calificaciones.length; i++){
          var tdScoreContainer = document.createElement('td');
          var tdScoreLabel = document.createElement('td');
          var rowScore = document.createElement('tr');

          var textScoreContainer = document.createTextNode(mje[0].calificaciones[i].cantidad);
          var textScoreLabel = document.createTextNode(mje[0].calificaciones[i].calificacion);

          tdScoreLabel.id = mje[0].calificaciones[i].calificacion.tagId;
          tdScoreContainer.appendChild(textScoreContainer);
          tdScoreLabel.appendChild(textScoreLabel);
          rowScore.appendChild(tdScoreLabel);
          rowScore.appendChild(tdScoreContainer);
          tabla.appendChild(rowScore);
        }
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log("Error al ejecutar => " + textStatus + " - " + errorThrown);
    }
  });
}

function actualiza_contenido_colas() {
  var nomcamp = $("#nombreCamp").html();
  $.ajax({
    url: 'Controller/Detalle_Campana_Contenido.php',
    type: 'GET',
    dataType: 'html',
    data: 'nomcamp='+nomcamp+'&op=queuedcalls',
    success: function (msg) {
      if(msg!=="]") {
        var tabla = document.getElementById('tableQueuedCalls');
        if($("#tableQueuedCalls").children().length > 0) {
          while(tabla.firstChild) {
            tabla.removeChild(tabla.firstChild);
          }
        }
        for (var i = 0; i < msg.length; i++) {
          var tdTimeContainer = document.createElement('td');
          var tdTimeLabel = document.createElement('td');
          var rowTime = document.createElement('tr');

          var textTimeContainer = document.createTextNode(mje.nroLlam);
          var textTimeLabel = document.createTextNode(mje.tiempo);

          tdTimeContainer.appendChild(textTimeContainer);
          tdTimeLabel.appendChild(textTimeLabel);
          rowTime.appendChild(tdTimeLabel);
          rowTime.appendChild(tdTimeContainer);
          tabla.appendChild(rowTime);
        }
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log("Error al ejecutar => " + textStatus + " - " + errorThrown);
    }
  });
}

function actualiza_contenido_wombat() {
  var nomcamp = $("#nombreCamp").html();
  $.ajax({
    url: 'Controller/Detalle_Campana_Contenido.php',
    type: 'GET',
    dataType: 'html',
    data: 'nomcamp='+nomcamp+'&op=wdstatus',
    success: function (msg) {
      if(msg!=="]") {
        var mje = JSON.parse(msg);
        tabwd.rows().remove().draw();
        tabwd.rows.add(mje).draw();
      } else {
        tabagt.rows().remove().draw();
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log("Error al ejecutar => " + textStatus + " - " + errorThrown);
    }
  });
}
