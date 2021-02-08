cargarMarcas();
cargarDolar();
cargarEuro();

var tabla = $("#tabla").DataTable();
$("#marca").select2();

$("#codigo").on("input", function () {
  if ($("#codigo").length && $("#codigo").val().length) {
    $("#marca").empty();
    $("#marca").prop("disabled", true);
    $("#marca").prop("selected", false);
  } else {
    $("#marca").prop("disabled", false);
    cargarMarcas();
  }
});

$("#buscar").submit(function (e) {
  e.preventDefault();
  var codigo = $("#codigo").val();
  var marca = $("#marca option:selected").val();
  codigo = $.trim(codigo);

  if (codigo != "") {
    cargarTabla("ajax/actualizaProduct.php?referencia=" + codigo);
  }

  if (marca != undefined) {
    cargarTabla("ajax/actualizaProduct.php?marca=" + marca);
  }
});

$("#cambiodolar").submit(function (e) {
  e.preventDefault();
  var valor_moneda = $("#dolar").val();
  if (valor_moneda == undefined || valor_moneda == "" || valor_moneda == null) {
    alert("No puede insertar valores vacios");
  } else {
    var data = { moneda: "dolar", valor_moneda: valor_moneda };
    $.ajax({
      url: "ajax/actualizaMoneda.php",
      type: "POST",
      data: data,
      beforeSend: function () {
        // Show image container
        $("#loaderdolar").show();
      },
      success: function (data) {
        $("#ultimocambiodolar").html("");
        cargarDolar();
      },
      complete: function (data) {
        // Hide image container
        $("#loaderdolar").hide();
      },
    });
  }
});

$("#cambioeuro").submit(function (e) {
  e.preventDefault();
  var valor_moneda = $("#euro").val();
  if (valor_moneda == undefined || valor_moneda == "" || valor_moneda == null) {
    alert("No puede insertar valores vacios");
  } else {
    var data = { moneda: "euro", valor_moneda: valor_moneda };
    $.ajax({
      url: "ajax/actualizaMoneda.php",
      type: "POST",
      data: data,
      beforeSend: function () {
        // Show image container
        $("#loadereuro").show();
      },
      success: function (data) {
        $("#ultimocambioeuro").html("");
        cargarEuro();
      },
      complete: function (data) {
        // Hide image container
        $("#loadereuro").hide();
      },
    });
  }
});

function cargarDolar() {
  $.ajax({
    url: "ajax/actualizaMoneda.php?cargarhistorico=dolar",
    type: "GET",
    beforeSend: function () {
      // Show image container
      $("#loaderdolar").show();
    },
    success: function (data) {
      data = JSON.parse(data);
      console.log(data);
      $("#ultimocambiodolar").append(
        "<strong style='color:red'>DOLAR</strong>" +
          " - Última Actualización " +
          data.fecha_modificacion +
          " - Precio: <strong>$" +
          data.label +
          "</strong>"
      );
    },
    complete: function (data) {
      // Hide image container
      $("#loaderdolar").hide();
    },
  });
}

function cargarEuro() {
  $.ajax({
    url: "ajax/actualizaMoneda.php?cargarhistorico=euro",
    type: "GET",
    beforeSend: function () {
      // Show image container
      $("#loadereuro").show();
    },
    success: function (data) {
      data = JSON.parse(data);
      console.log(data);
      $("#ultimocambioeuro").append(
        "<strong style='color:red'>EURO</strong>" +
          " - Última Actualización " +
          data.fecha_modificacion +
          " - Precio: <strong>$" +
          data.label +
          "</strong>"
      );
    },
    complete: function (data) {
      // Hide image container
      $("#loadereuro").hide();
    },
  });
}

function cargarMarcas() {
  $.ajax({
    url: "ajax/actualizaProduct.php?categoria=productos",
    type: "GET",
    success: function (data) {
      data = JSON.parse(data);

      $.each(data, function (llave, valor) {
        $("#marca").append(
          "<option value='" + valor.id + "'>" + valor.categoria + "</option>"
        );
      });
    },
  });
}
function cargarTabla(url) {
  tabla.destroy();
  tabla = $("#tabla").DataTable({
    keys: true,
    ajax: {
      url: url,
      dataSrc: "",
    },
    columns: [
      {
        data: function (data) {
          return (
            "<a href='/product/card.php?id=" +
            data["id"] +
            "'>" +
            data["ref"] +
            "</a>"
          );
        },
      },
      { data: "label" },
      { data: "moneda" },
      { data: "precio_moneda" },
      {
        data: function (data) {
          return (
            "<input type='text' name='cost_price' refe='" +
            data["ref"] +
            "' value='" +
            data["cost_price"] +
            "' class='editt'>"
          );
        },
      },
      {
        data: function (data) {
          if (data["margen1"]) {
            return data["margen1"] + "%";
          } else {
            return "<strong style='color:red'>Margen N/D</strong>";
          }
        },
      },
      {
        data: function (data) {
          return "$<span>" + data["precio1"] + "</span>";
        },
      },
      {
        data: function (data) {
          if (data["margen2"]) {
            return data["margen2"] + "%";
          } else {
            return "<strong style='color:red'>Margen N/D</strong>";
          }
        },
      },
      {
        data: function (data) {
          return "$<span>" + data["precio2"] + "</span>";
        },
      },
      {
        data: function (data) {
          if (data["margen3"]) {
            return data["margen3"] + "%";
          } else {
            return "<strong style='color:red'>Margen N/D</strong>";
          }
        },
      },
      {
        data: function (data) {
          return "$<span>" + data["precio3"] + "</span>";
        },
      },
    ],
    language: {
      sProcessing: "Procesando",
      sLengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron registros",
      sEmptyTable: "Ningún dato disponible en esta tabla",
      sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
      sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0",
      sInfoFiltered: "(filtrado un total de _MAX_ registros)",
      sInfoPostFix: "",
      sSearch: "Buscar:",
      sUrl: "",
      sInfoThousands: ",",
      sLoadingRecords: "Cargando...",
      oPaginate: {
        sFirst: "Primero",
        sLast: "Último",
        sNext: "Siguiente",
        sPrevious: "Anterior",
      },
      oAria: {
        sSortAscending:
          ": Activar para odernar la columna de manera ascendente",
        sSortDescending:
          "Activar para ordenar la columna de manera descendente",
      },
    },
  });

}

tabla.on("key", function (e, datatable, key, cell, originalEvent) {
  if (key == 13) {
    textoseparado = cell.data().split("'");
    $('input[refe="' + textoseparado[5] + '"]').focus();
    $('input[refe="' + textoseparado[5] + '"]').select();
  }
});

$(document).on("change", ".editt", function () {
  let element = $(this)[0];
  precioCompra = $(element).val();
  referencia = $(element).attr("refe");
  var data = { cost_price: precioCompra, refe: referencia };
  $.ajax({
    type: "POST",
    url: "ajax/actualizaProduct.php",
    data: data,
    success: function (data) {
      var imagen = $(element).parent().find("img");
      imagen.hide();
      if (data == "error") {
        $(element).parent().append('<img src="img/error.png">');
        return;
      } else {
        data = JSON.parse(data);
        $(element).parent().append('<img src="img/check.png">');
        $(element)
          .parent()
          .parent()
          .find("td:eq(6)")
          .css("background-color", "#008000")
          .html("$<span style='color:white'>" + data["precio1"] + "</span>");
        $(element)
          .parent()
          .parent()
          .find("td:eq(8)")
          .css("background-color", "#008000")
          .html("$<span style='color:white'>" + data["precio2"] + "</span>");
        $(element)
          .parent()
          .parent()
          .find("td:eq(10)")
          .css("background-color", "#008000")
          .html("$<span style='color:white'>" + data["precio3"] + "</span>");
      }

      // console.log(element);
    },
  });
});
