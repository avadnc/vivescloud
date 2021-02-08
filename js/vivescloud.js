$(document).ready(function () {
  //borramos pedimentos
  let qty;
  let pedimento;
  $("#options_pedimento").hide();
  $("#options_numposicionzf").parent().parent().hide();

  if (window.location.href.indexOf("/compta/facture/card.php?facid=") > -1) {
    $('a[href*="ZF"]').each(function () {
      $("#options_numposicionzf").parent().parent().show();
    });
  }
  if (window.location.href.indexOf("/fourn/commande/dispatch.php?id=") > -1) {
    $("input[name='dispatch']").click(function (e) {
      
      if ($("#pedimento").val() == "") {
        Swal.fire({
          title: "Alerta",
          text: "Va a recibir Artíclos sin Pedimento",
          showCancelButton: true,
          showConfirmButton: true,
        }).then(function (result) {
          // console.log(result);
          if (result.isConfirmed == false) {
            e.preventDefault();
          }
        });
      }
    });

    if ($(".statusref span.badge").text() == "Todos los productos recibidos") {
      $(".div-table-responsive-no-min :input").prop("disabled", true);
      $("input[name='dispatch']").prop("disabled", true);
    }
  }

  var moneda = $.ajax({
    url: "/custom/vivescloud/ajax/actualizaMoneda.php?cargarhistorico=dolar",
    type: "GET",
    async: false,
  }).responseText;
  moneda = JSON.parse(moneda);
  //Lista de precio

  if ($("#options_listaprecio").length) {
    $("#options_listaprecio").change(function () {
      var optionSelected = $("#options_listaprecio")
        .children("option:selected")
        .val();
      if ($("#search_idprod").length) {
        var producto = $("#search_idprod").val();
        $.ajax({
          url: "/custom/vivescloud/ajax/stockProducts.php?producto=" + producto,
          type: "GET",
          dataType: "json",
          success: function (data) {
            console.log(optionSelected);

            switch (optionSelected) {
              case "1":
                $("#price_ht").val(data[0]["precio1"]);

                if ($("#multicurrency_subprice").length) {
                  var precioDivisa = data[0]["precio1"] / moneda["label"];
                  $("#multicurrency_subprice").val(precioDivisa.toFixed(4));
                }
                break;
              case "2":
                $("#price_ht").val(data[0]["precio2"]);

                if ($("#multicurrency_subprice").length) {
                  var precioDivisa = data[0]["precio2"] / moneda["label"];
                  $("#multicurrency_subprice").val(precioDivisa.toFixed(4));
                }

                break;

              case "3":
                $("#price_ht").val(data[0]["precio3"]);

                if ($("#multicurrency_subprice").length) {
                  var precioDivisa = data[0]["precio3"] / moneda["label"];
                  $("#multicurrency_subprice").val(precioDivisa.toFixed(4));
                }
                break;

              default:
                $("#price_ht").val(data[0]["precio1"]);
                if ($("#multicurrency_subprice").length) {
                  var precioDivisa = data[0]["precio1"] / moneda["label"];
                  $("#multicurrency_subprice").val(precioDivisa.toFixed(4));
                }
                break;
            }
          },
        });
      }
      if ($("#product_id").length) {
        var idProducto = $("#product_id").val();

        $.ajax({
          url:
            "/custom/vivescloud/ajax/stockProducts.php?idproducto=" +
            idProducto,
          type: "GET",
          dataType: "json",
          success: function (data) {
            switch (optionSelected) {
              case "1":
                $("#price_ht").val(data[0]["precio1"]);

                if ($("#multicurrency_subprice").length) {
                  var precioDivisa = data[0]["precio1"] / moneda["label"];
                  $("#multicurrency_subprice").val(precioDivisa.toFixed(4));
                }
                break;
              case "2":
                $("#price_ht").val(data[0]["precio2"]);

                if ($("#multicurrency_subprice").length) {
                  var precioDivisa = data[0]["precio2"] / moneda["label"];
                  $("#multicurrency_subprice").val(precioDivisa.toFixed(4));
                }

                break;

              case "3":
                $("#price_ht").val(data[0]["precio3"]);

                if ($("#multicurrency_subprice").length) {
                  var precioDivisa = data[0]["precio3"] / moneda["label"];
                  $("#multicurrency_subprice").val(precioDivisa.toFixed(4));
                }
                break;

              default:
                $("#price_ht").val(data[0]["precio1"]);
                if ($("#multicurrency_subprice").length) {
                  var precioDivisa = data[0]["precio1"] / moneda["label"];
                  $("#multicurrency_subprice").val(precioDivisa.toFixed(4));
                }
                break;
            }
          },
        });
      }
    });
  }
  // var producto = null;

  // if ($("#product_id").length) {
  //   $.ajax({
  //     url:
  //       "/custom/vivescloud/ajax/stockProducts.php?productoid=" +
  //       $("#product_id").val(),
  //     type: "GET",
  //     dataType: "json",
  //     success: function (data) {
  //       console.log(data);

  //       $.each(data, function (key, value) {
  //         console.log(value);

  //         $("#options_stocks").after(function () {
  //           return (
  //             "<div><span style='color:white;background-color:red;font-size:20px;font-weight: bold;padding:px;'>Almacén:" +
  //             value.warehouse +
  //             "</span>  <strong>Stock:</strong>" +
  //             value.stock +
  //             "<br></div>"
  //           );
  //         });
  //       });
  //     },
  //   });
  // }

  // Buscar Pedimento
  if ($("#search_idprod").length) {
    $("#search_idprod").focusout(function () {
      if ($("#search_idprod").val().length > 0) {
        producto = $("#idprod").val();
        $.ajax({
          url:
            "/custom/vivescloud/ajax/stockProducts.php?productoLote=" +
            producto,
          type: "GET",
          dataType: "json",
          async: false,
          success: function (data) {
            if (data == null) {
              return;
            } else {
              $("#options_pedimento").before(
                "<select id='pedimento'><option>Seleccione Pedimento</option></select>"
              );

              console.log(data.data);
              if (data.msg == "success") {
                $.each(data.data, function (key, value) {
                  $("#pedimento").append(
                    '<option stock="' +
                      value.qty +
                      '" value="' +
                      value.batch +
                      '">' +
                      value.batch +
                      " - <strong>Stock:</strong> " +
                      value.qty +
                      "</option>"
                  );
                });
              }
              var opt = {
                autoOpen: false,
                modal: true,
                width: 550,
                height: 200,
                title: "Seleccionar Pedimento",
                buttons: {
                  Confirm: function () {
                    qty = $("option:selected", this).attr("stock");
                    pedimento = $("#pedimento").val();
                    $("#options_pedimento").val(pedimento);
                    // console.log(pedimento);
                    // return;
                    parseInt(qty);

                    if (qty !== undefined) {
                      // $("#options_pedimento").prop("disabled", true);
                      $("#options_pedimento").before(
                        "<p>Pedimento: " + pedimento + " Stock: " + qty + "</p>"
                      );
                      theDialog.dialog("close");
                    }
                  },
                },
              };
              var theDialog = $("#pedimento").dialog(opt);
              theDialog.dialog("open");
            }
          },
        });
      } 
    });
  }

  $("#qty").focusout(function () {
    cantidad = $(this).val();
    // parseInt(cantidad);
    // console.log(cantidad);
    // console.log(qty);
    // console.log(cantidad > qty);
    resultado = compara(parseInt(cantidad), parseInt(qty));
    if (resultado == true) {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Stock Insuficiente para ese Pedimento",
      });
    }

    // if (cantidad > qty) {

    // }
  });

  $("#multicurrency_price_ht").focusout(function(){
    
    precio = $(this).val();
    precio_moneda = precio * moneda.label;
    // console.log(precio_moneda);
    $("#price_ht").val(precio_moneda);
  });

  function compara(a, b) {
    if (a > b) {
      return true;
    }
    if (a < b) {
      return false;
    }
    if (a == b) {
      return false;
    }
  }
  $(document).on("change", function () {
    if (window.location.href.indexOf("/compta/facture/card.php?facid=") > -1) {
      $("#pedimento").change(function () {
        parseInt(qty);
      });
    }
  });
});
