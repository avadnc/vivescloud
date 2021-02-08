<?php
/* Copyright (C) 2021 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * Library javascript to enable Browser notifications
 */

if (!defined('NOREQUIREUSER')) {
    define('NOREQUIREUSER', '1');
}

if (!defined('NOREQUIREDB')) {
    define('NOREQUIREDB', '1');
}

if (!defined('NOREQUIRESOC')) {
    define('NOREQUIRESOC', '1');
}

if (!defined('NOREQUIRETRAN')) {
    define('NOREQUIRETRAN', '1');
}

if (!defined('NOCSRFCHECK')) {
    define('NOCSRFCHECK', 1);
}

if (!defined('NOTOKENRENEWAL')) {
    define('NOTOKENRENEWAL', 1);
}

if (!defined('NOLOGIN')) {
    define('NOLOGIN', 1);
}

if (!defined('NOREQUIREMENU')) {
    define('NOREQUIREMENU', 1);
}

if (!defined('NOREQUIREHTML')) {
    define('NOREQUIREHTML', 1);
}

if (!defined('NOREQUIREAJAX')) {
    define('NOREQUIREAJAX', '1');
}

/**
 * \file    vivescloud/js/vivescloud.js.php
 * \ingroup vivescloud
 * \brief   JavaScript file for module Vivescloud.
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
    $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"] . "/main.inc.php";
}

// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME'];
$tmp2 = realpath(__FILE__);
$i = strlen($tmp) - 1;
$j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {$i--;
    $j--;}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1)) . "/main.inc.php")) {
    $res = @include substr($tmp, 0, ($i + 1)) . "/main.inc.php";
}

if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1)) . "/../main.inc.php")) {
    $res = @include substr($tmp, 0, ($i + 1)) . "/../main.inc.php";
}

// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) {
    $res = @include "../../main.inc.php";
}

if (!$res && file_exists("../../../main.inc.php")) {
    $res = @include "../../../main.inc.php";
}

if (!$res) {
    die("Include of main fails");
}
dol_include_once('/vivescloud/vendor/autoload.php');
include '../class/ConsultasStockProductos.class.php';
// Define js type
header('Content-Type: application/javascript');
// Important: Following code is to cache this file to avoid page request by browser at each Dolibarr page access.
// You can use CTRL+F5 to refresh your browser cache.
if (empty($dolibarr_nocache)) {
    header('Cache-Control: max-age=3600, public, must-revalidate');
} else {
    header('Cache-Control: no-cache');
}
?>
$(document).ready(function () {
  var producto = null;

  if ($("#options_pedimento").length) {
    $("#options_pedimento").hide();
  }

  if ($("#product_id").length) {
    $.ajax({
      url:
        "/custom/vivescloud/ajax/stockProducts.php?productoid=" +
        $("#product_id").val(),
      type: "GET",
      dataType: "json",
      success: function (data) {
        console.log(data);

        $.each(data, function (key, value) {
          console.log(value);

          $("#options_stocks").after(function () {
            return (
              "<div><span style='color:white;background-color:red;font-size:20px;font-weight: bold;padding:px;'>Almacén:" +
              value.warehouse +
              "</span>  <strong>Stock:</strong>" +
              value.stock +
              "<br></div>"
            );
          });
        });
      },
    });
  }

  var moneda = $.ajax({
    url: "/custom/vivescloud/ajax/actualizaMoneda.php?cargarhistorico=dolar",
    type: "GET",
    async: false,
  }).responseText;

  moneda = JSON.parse(moneda);

  // Buscar Pedimento
  if ($("#search_idprod").length) {
    $("#search_idprod").focusout(function () {
      $("#options_stocks").nextAll().remove();
      $("#options_pedimento").hide();
      $("#options_pedimento").empty();
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
            console.log(data);
            if (data != null) {
              $("#options_pedimento").show();
              $.each(data, function (key, value) {
                $("#options_pedimento").append(
                  '<option stock="' +
                    value.qty +
                    '" value="' +
                    value.rowid +
                    "-" +
                    value.batch +
                    '">' +
                    value.batch +
                    " - <strong>Stock:</strong> " +
                    value.qty +
                    "</option>"
                );
              });
            } else {
              $.ajax({
                url:
                  "/custom/vivescloud/ajax/stockProducts.php?productoid=" +
                  producto,
                type: "GET",
                dataType: "json",
                success: function (data) {
                  console.log(data);

                  $.each(data, function (key, value) {
                    console.log(value);

                    $("#options_stocks").after(function () {
                      return (
                        "<div><span style='color:white;background-color:red;font-size:20px;font-weight: bold;padding:px;'>Almacén:" +
                        value.warehouse +
                        "</span>  <strong>Stock:</strong>" +
                        value.stock +
                        "<br></div>"
                      );
                    });
                  });
                },
              });
            }
          },
        });
      }
    });
  }

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

  //validar linea

  $("#addline").click(function (e) {
    e.preventDefault();

    if ($("#options_pedimento").has("option").length > 0) {
      var stock = $("select#options_pedimento option:selected").attr("stock");

      if (stock >= $("#qty").val()) {
        $("#addproduct").submit();
      } else {
        alert("Stock Insuficiente para ese Pedimento");
      }
    } else {
      $.ajax({
        url:
          "/custom/vivescloud/ajax/stockProducts.php?productostock=" + producto,
        type: "GET",
        dataType: "json",
        success: function (data) {
          if (data.code == "err") {
            alert("Stock No Disponible");
          }
          if (data.code == "success") {
            console.log(data);
            if (data.msg >= $("#qty").val()) {
              $("#addproduct").submit();
            } else {
              alert("Stock Disponible Para este Almacén: " + data.msg);
            }
          }
        },
      });
    }
  });
});
