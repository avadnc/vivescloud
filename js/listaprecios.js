cargarMarcas();
cargarDolar();
cargarEuro();
$("#cotizacion").hide();
$("#items").hide();
$(".botones").hide();

var listaItems = [];
var precioDolar = [];
var precioEuro = [];

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

      $("#ultimocambiodolar").append(
        "<strong style='color:red'>DOLAR</strong>" +
          " - Última Actualización " +
          data.fecha_modificacion +
          " - Precio: <strong>$" +
          data.label +
          "</strong>"
      );
      precioDolar = data.label;
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
        "<strong style='color:blue'>EURO</strong>" +
          " - Última Actualización " +
          data.fecha_modificacion +
          " - Precio: <strong>€" +
          data.label +
          "</strong>"
      );
      precioEuro = data.label;
    },
    complete: function (data) {
      // Hide image container
      $("#loadereuro").hide();
    },
  });
}
var tabla = $("#tabla").DataTable({
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
      sSortAscending: ": Activar para odernar la columna de manera ascendente",
      sSortDescending: "Activar para ordenar la columna de manera descendente",
    },
  },
});

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
  $("#4").prop("checked", true);
  $("#5").prop("checked", true);
  $("#6").prop("checked", true);
  var codigo = $("#codigo").val();
  var marca = $("#marca option:selected").val();
  codigo = $.trim(codigo);

  if (codigo != "") {
    cargarTabla("ajax/stockProducts.php?producto=" + codigo);
  }

  if (marca != undefined) {
    cargarTabla("ajax/stockProducts.php?marca=" + marca);
  }
});

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
     dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
    ajax: {
      dataType: "json",
      url: url,
      dataSrc: "",
    },
    columnDefs: [{ targets: "_all", width: "auto" }],
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
      {
        data: function (data) {
          string = " <select class='stock'>";

          if (data["stock"].length > 0) {
            $.each(data["stock"], function (llave, valor) {
              string +=
                "<option>" +
                valor["warehouse"] +
                " - Stock:" +
                valor["stock"] +
                "</option>";
            });
            string += "</select>";
            return string;
          } else {
            return "<strong style='color:red;'>Sin STOCK</strong>";
          }
        },
      },
      {
        data: function (data) {
          string = " <select class='moneda'>";
          $.each(data["moneda"], function (llave, valor) {
            string += '<option value="' + valor + '">' + valor + "</option>";
          });
          string += "</select>";
          return string;
        },
      },
      {
        data: function (data) {
          if (data["precio1"] > 0) {
            var moneda = Object.keys(data.moneda)[0];
            if (moneda == "USD") {
              var precio1 = data["precio1"] / precioDolar;
              return (
                "<input type='hidden' name='divisa' tipodivisa='USD'>"+
                "<p>$" +
                numberWithCommas(precio1.toFixed(2)) +
                "</p>"
              );
            } else if (moneda == "EUR") {
              var precio1 = data["precio1"] / precioEuro;
              return (
                "<input type='hidden' name='divisa' tipodivisa='EUR'>" +
                "<p>€" +
                numberWithCommas(precio1.toFixed(2)) +
                "</p>"
              );
            } else {
              return (
                "<p>$" + numberWithCommas(data.precio1.toFixed(2)) + "</p>"
              );
            }
          } else {
            return "<strong style='color:red;'>N/D</strong>";
          }
        },
      },
      {
        data: function (data) {
          if (data["precio2"] > 0) {
            var moneda = Object.keys(data.moneda)[0];
            if (moneda == "USD") {
              var precio2 = data["precio2"] / precioDolar;
              return (
                "<p>$" +
                numberWithCommas(precio2.toFixed(2)) +
                "</p>"
              );
            } else if (moneda == "EUR") {
              var precio2 = data["precio2"] / precioEuro;
              return (
                "<p>€" +
                numberWithCommas(precio2.toFixed(2)) +
                "</p>"
              );
            } else {
              return (
                "<p>$" + numberWithCommas(data.precio2.toFixed(2)) + "</p>"
              );
            }
          } else {
            return "<strong style='color:red;'>N/D</strong>";
          }
        },
      },
      {
        data: function (data) {
          if (data["precio3"] > 0) {
            var moneda = Object.keys(data.moneda)[0];
            if (moneda == "USD") {
              var precio3 = data["precio3"] / precioDolar;
              return (
                "<p>$" +
                numberWithCommas(precio3.toFixed(2)) +
                "</p>"
              );
            } else if (moneda == "EUR") {
              var precio3 = data["precio3"] / precioEuro;
              return (
                "<p>€" +
                numberWithCommas(precio3.toFixed(2)) +
                "</p>"
              );
            } else {
              return (
                "<p>$" + numberWithCommas(data.precio3.toFixed(2)) + "</p>"
              );
            }
          } else {
            return "<strong style='color:red;'>N/D</strong>";
          }
        },
      },
      //introducido 08/12
      {
        data: function (data) {
          return (
            '<button name="agregar" class="agregar" descProducto="' +
            data["label"] +
            '" refProducto="' +
            data["ref"] +
            '" idProducto="' +
            data["id"] +
            '" precio ="Mostrador $' +
            data["precio1"] +
            " - Minorista $" +
            data["precio2"] +
            " - Mayorista $" +
            data["precio3"] +
            '">Agregar</button>'
          );
        },
      },
      //fin
    ],
    rowCallback: function (row, data) {
      var moneda = Object.keys(data.moneda)[0];
      if (moneda == "USD") {
        for (i = 4; i < 7; i++) {
          $(row)
            .find("td:eq(" + i + ")")
            .css("background-color", "red");
          $(row)
            .find("td:eq(" + i + ")")
            .css("color", "white");
          $(row)
            .find("td:eq(" + i + ")")
            .css("font-weight", "bold");
        }
      }
      if (moneda == "EUR") {
        for (i = 4; i < 7; i++) {
          $(row)
            .find("td:eq(" + i + ")")
            .css("background-color", "blue");
          $(row)
            .find("td:eq(" + i + ")")
            .css("color", "white");
          $(row)
            .find("td:eq(" + i + ")")
            .css("font-weight", "bold");
        }
      }
    },
    initComplete: function (settings) {
      var api = new $.fn.dataTable.Api(settings);

      // Initialize Select2 control
      $(".stock", api.rows().nodes()).select2({
        sortResults: (data) =>
          data.sort((a, b) => a.text.localeCompare(b.text)),
        dropdownAutoWidth: true,
        width: "auto",
      });
      $(".moneda", api.rows().nodes()).select2({
        dropdownAutoWidth: true,
        width: "auto",
      });
    },
    pageLength: 100,
    lengthMenu: [
      [10, 20, 25, 50, -1],
      [10, 20, 25, 50, "All"],
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
  $(".botones").show();
}
function numberWithCommas(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function dialogo() {
  var opt = {
    autoOpen: false,
    modal: true,
    width: 550,
    height: 650,
    title: "Details",
  };
  var theDialog = $("#cotizacion").dialog(opt);
  theDialog.dialog("open");
}

function items() {
  var opt = {
    autoOpen: false,
    modal: true,
    width: 550,
    height: 650,
    title: "Consulta de Items",
  };
  $("#items").html(
    '<button id="btnPrint">Imprimir</button><table id="idNota" class="tagtable liste listwithfilterbefore"><thead><tr><th>Referencia</th><th>Descripcion</th><th>Precio</th></tr></thead></table>'
  );
  if (listaItems.length > 0) {
    $.each(listaItems, function (llave, valor) {
      return $("#items>table tr:last").after(
        "<tr>" +
          '<td style="text-align:center">' +
          valor.referencia +
          "</td>" +
          '<td style="text-align:center">' +
          valor.descripcion +
          "</td>" +
          '<td style="text-align:center">' +
          valor.precio +
          "</td>" +
          "</tr>"
      );
    });
  }

  var theDialog = $("#items").dialog(opt);
  theDialog.dialog("open");
}

$("#items").on("dialogclose", function (event) {
  $("#items>table").empty();
});

$("#cotiza").click(function () {
  dialogo();
});

$(document).on("click", ".agregar", function () {
  idItem = $(this).attr("idProducto");
  refItem = $(this).attr("refProducto");
  descItem = $(this).attr("descProducto");
  precio = $(this).attr("precio");

  listaItems.push({
    producto: idItem,
    referencia: refItem,
    descripcion: descItem,
    precio: precio,
  });
  $("#" + idItem).val("");
});

$("#cesta").click(function () {
  if (listaItems.length > 0) {
    items();
  }
});

$(document).on("change", ".moneda", function () {
  let element = $(this)[0];
  moneda =  $("input[name*='divisa']").attr('tipodivisa');
  console.log(moneda);
  
  if (moneda == "USD") {
    $.ajax({
      url: "ajax/actualizaMoneda.php?cargarhistorico=dolar",
      // data: { cargarhistorico: "dolar" },
      type: "GET",
      success: function (data) {
        data = JSON.parse(data);
        var i;
        for (i = 4; i < 7; i++) {
          $(element)
            .parent()
            .parent()
            .find("td:eq(" + i + ")")
            .css("background-color", "red");
          $(element)
            .parent()
            .parent()
            .find("td:eq(" + i + ")")
            .css("color", "white");
          $(element)
            .parent()
            .parent()
            .find("td:eq(" + i + ")")
            .css("font-weight", "bold");
          precio = $(element)
            .parent()
            .parent()
            .find("td:eq(" + i + ")")
            .text();
          precio = precio.substr(1);
          precio = parseFloat(precio.replace(",", ""));
          resultado = precio * data.label;

          $(element)
            .parent()
            .parent()
            .find("td:eq(" + i + ")")
            .html("<p>$" + numberWithCommas(resultado.toFixed(2)) + "</p>");
        }
        $(element).prop('disabled',true);
      },
    });
  }
  if (moneda == "EUR") {
    $.ajax({
      url: "ajax/actualizaMoneda.php?cargarhistorico=euro",
      // data: { cargarhistorico: "dolar" },
      type: "GET",
      success: function (data) {
        data = JSON.parse(data);
        var i;
        for (i = 4; i < 7; i++) {
          $(element)
            .parent()
            .parent()
            .find("td:eq(" + i + ")")
            .css("background-color", "blue");
          $(element)
            .parent()
            .parent()
            .find("td:eq(" + i + ")")
            .css("color", "white");
          $(element)
            .parent()
            .parent()
            .find("td:eq(" + i + ")")
            .css("font-weight", "bold");
          precio = $(element)
            .parent()
            .parent()
            .find("td:eq(" + i + ")")
            .text();
          precio = precio.substr(1);
          console.log();
          precio = parseFloat(precio.replace(",", ""));
          resultado = precio * data.label;

          console.log(precio);
          $(element)
            .parent()
            .parent()
            .find("td:eq(" + i + ")")
            .html("<p>$" + numberWithCommas(resultado.toFixed(2)) + "</p>");
        }
         $(element).prop("disabled", true);
      },
    });
  }
});

$("input:checkbox").change(function () {
  console.log("here i am: " + this.id + " value = " + $(this).val());
  table = $("#tabla").DataTable();
  var column = table.column(this.id);
  // // Toggle the visibility
  column.visible(!column.visible());
});

$("#btnPrint").click(function () {
  console.log("si voy a imprimir");
  $("#cotizacion").printThis();
});
