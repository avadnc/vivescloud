<?php
/* Smarty version 3.1.34-dev-7, created on 2021-02-05 11:13:30
  from 'D:\laragon\www\davila\custom\vivescloud\templates\pagos.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_601d7cba677579_92326263',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cfe265da79bf0f13d07e125b0afbe6363cd7f519' => 
    array (
      0 => 'D:\\laragon\\www\\davila\\custom\\vivescloud\\templates\\pagos.tpl',
      1 => 1612545207,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
),false)) {
function content_601d7cba677579_92326263 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<div>
    <table class="border centpercent">
        <tr>
            <td>
                <label for="fechainicio">Fecha de Inicio</label><input type="date" id="fechainicio" name="fechainicio">
            </td>
            <td>
                <label for="fechafin">Fecha de Fin</label><input type="date" id="fechafin" name="fechafin">
            </td>
            <td>
                <label for="tipopago">Tipo de Pago</label>
                <select id="tipopago">
                    <option value="LIQ">Efectivo</option>
                    <option value="28">Tarjeta de Débito</option>
                    <option value="CB">Tarjeta de Crédito</option>
                </select>
            </td>
            <td>
                <label for="sucursal">Sucursal</label>
                <select id="sucursal">
                    <option value="1">Guadalajara</option>
                    <option value="2">Vallarta</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <input type="submit" id="enviar" class="button" value="Consultar">
            </td>

        </tr>
        <table>
</div>

<table id="tabla">
    <thead>
        <th>Factura</th>
        <th>Fecha Emisión</th>
        <th>Cliente</th>
        <th>Monto Total</th>
        <th>Moneda</th>
        <th>Tipo de Pago</th>
        <th>Acciones</th>
    </thead>

    <tbody>

    </tbody>

</table>
<?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
    


        $(document).ready(function() {

            var table = $("#tabla").DataTable({

            });
            $("#enviar").click(function(e) {



                fechainicio = $("#fechainicio").val();
                fechafin = $("#fechafin").val();
                tipopago = $("#tipopago").val();
                sucursal = $("#sucursal").val();

                
          table.destroy();

                datos = new FormData();
                datos.append('action', 'obtenerFacturas');
                datos.append('fechainicio', fechainicio);
                datos.append('fechafin', fechafin);
                datos.append('tipopago', tipopago);
                datos.append('sucursal', sucursal);

                //console.log(datos);

                $.ajax({
                    url: "/custom/vivescloud/ajax/pagosMasivos.php",
                    method: 'POST',
                    data: datos,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                       
                        table = $("#tabla").DataTable({});
                        $.each(data, function(key,value){
                           
                           table.row.add([
                                value.ref,
                                value.fechaEmision,
                                value.cliente,
                                value.totalFactura,
                                tipopago,
                                'Pendiente de Pago',
                                '<input type="checkbox">',
                           ]).draw(false);
                        });
                        
                    }
                });
                /*table = $("#tabla").DataTable({

                ajax:{
                   url: "/custom/vivescloud/ajax/pagosMasivos.php",
                   method: 'GET',
                   data: datos,
                   cache: false,
                   contentType: false,
                   processData: false,
                   dataType: 'json',
                    success: function(data) {
                        console.log(data);
                    },


                }
            });*/
            })


            /*data = new FormData();
        data.append();*/
            /* $.ajax({
             url: window.location
             type: "POST",
             dataType: "JSON",

         });*/

        });
    
<?php echo '</script'; ?>
><?php }
}
