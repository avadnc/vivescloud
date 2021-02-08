$(document).ready(function () {
  $(".tabsAction").hide();
  $(".selector").select2();
  $("#cantidad").bind("keyup paste", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
  });
  $("#correccion_stock").dialog({
    autoOpen: false,
    resizable: false,
    height: "auto",
    width: 400,
    modal: true,
    buttons: {
      Aceptar: function () {
        var action = "corregir";
        var almacen = $("#almacen").val();
        var accion = $("#accion").val();
        var cantidad = $("#cantidad").val();
        var idproducto = $("#idProducto").val();
        var cantidad = $("#cantidad").val();
        var lote = $("#pedimento").val();

        data = new FormData();
        data.append("action", action);
        data.append("almacen", almacen);
        data.append("movimiento", accion);
        data.append("cantidad", cantidad);
        data.append("producto", idproducto);
        data.append("lote", lote);

        $.ajax({
          url: "/custom/vivescloud/ajax/actualizarstock.php",
          method: "POST",
          data: data,
          contentType: false,
          processData: false,
          success: function (data) {
            if (data != "null") {
              data = JSON.parse(data);
              if (data["code"] == "Ok") {
                location.reload();
              }
              if (data["code"] == "error") {
                alert(data["msg"]);
              }
            } else {
              //añadir swal
              alert("no se pudo actualizar el stock");
            }
          },
        });
      },
      Cancelar: function () {
        $(this).dialog("close");
      },
    },
  });
  $("#corregir").click(function () {
    $("#correccion_stock").dialog("open");
  });
  $("#transferir_stock").dialog({
    autoOpen: false,
    resizable: false,
    height: "auto",
    width: 400,
    modal: true,
    buttons: {
      Aceptar: function () {
        var action = "transferir";
        var almacenorigen = $("#almacenorigen").val();
        var almacendestino = $("#almacendestino").val();
        var cantidadtransferencia = $("#cantidadtransferencia").val();
        var idproducto = $("#idProducto").val();
     
        var pedimentotransferencia = $("#pedimentotransferencia").val();

        data = new FormData();
        data.append("action", action);
        data.append("almacenorigen", almacenorigen);
        data.append("almacendestino", almacendestino);
        data.append("cantidad", cantidadtransferencia);
        data.append("producto", idproducto);
        data.append("lote", pedimentotransferencia);

        $.ajax({
          url: "/custom/vivescloud/ajax/actualizarstock.php",
          method: "POST",
          data: data,
          contentType: false,
          processData: false,
          success: function (data) {

            if (data != "null") {
              data = JSON.parse(data);
              if (data["code"] == "Ok") {
                location.reload();
              }
              if (data["code"] == "error") {
                alert(data["msg"]);
              }
            } else {
              //añadir swal
              alert("no se pudo actualizar el stock");
            }
          },
        });
      },
      Cancelar: function () {
        $(this).dialog("close");
      },
    },
  });
  $("#transferir").click(function () {
    $("#transferir_stock").dialog("open");
  });
});
