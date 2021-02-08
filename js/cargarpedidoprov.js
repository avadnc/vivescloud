$(document).ready(function () {
  var pedido = $("#numPedido").val();
  //   console.log(pedido);
  var tabla = $("#tabla").DataTable({
    ajax: {
      url: "ajax/pedidoProveedor.php?pedido=" + pedido + "&action=leer",
      type: "GET",
      dataType: "json",
      dataSrc: null,
      success: function (data) {
          $.each(data,function(llave,valor){
               tabla.row.add([valor.ref, valor.desc,valor.qty,valor.price]).draw(false);
          })
      },
    },
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

  $("#codigo").focusout(function () {
    codigo = $(this).val();
    // console.log(codigo);
    $.ajax({
      url: "ajax/pedidoProveedor.php",
      type: "get",
      dataType: "json",
      data: { referencia: codigo },
      success: function (data) {
        if (data.length === 0) {
          $("#descripcion").text("SIN DATOS");
        }
        if (data.length > 1) {
          $("#descripcion").text("ESTE CODIGO TIENE MUCHOS PRODUCTOS");
        }
        if (data.length == 1) {
          $("#precio").val(data[0].cost_price);
          $("#descripcion").text(data[0].label);
          $("#idProd").val(data[0].id);
          $("#refProd").val(data[0].ref);
        }
        // console.log(data);
      },
    });
  });

  //insertar formulario
  $("#insertar").submit(function (e) {
    e.preventDefault();
    // console.log("hola");

    //validamos si estan vacias
    if (
      $("#codigo").val().length === 0 ||
      $("#cantidad").val().length === 0 ||
      $("#precio").val().length === 0
    ) {
      alert("no se puede");
      return;
    }

    var pedido = $("#numPedido").val();
    var idProd = $("#idProd").val();
    var cantidad = $("#cantidad").val();
    var precio = $("#precio").val();
    var descripion = $("#descripcion").text();
    var ref = $("#refProd").val();

    var datos = new FormData();
    datos.append("action", "insertar");
    datos.append("pedido", pedido);
    datos.append("idProd", idProd);
    datos.append("cantidad", cantidad);
    datos.append("precio", precio);
    $.ajax({
      url: "ajax/pedidoProveedor.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (data) {
        tabla.row
          .add([
            ref,
            descripion,
            cantidad,
            precio,
            "<button idProducto='" + data + "'>borrar</button>",
          ])
          .draw(false);
      },
    });

    //vaciamos datos
    $("#codigo").val("");
    $("#codigo").focus();
    $("#descripcion").text("");
    $("#cantidad").val("");
    $("#precio").val("");
  });
});
