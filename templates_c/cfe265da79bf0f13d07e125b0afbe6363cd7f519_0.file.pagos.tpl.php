<?php
/* Smarty version 3.1.34-dev-7, created on 2021-02-11 11:40:31
  from 'D:\laragon\www\davila\custom\vivescloud\templates\pagos.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_60256c0f2cd7d2_79168481',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cfe265da79bf0f13d07e125b0afbe6363cd7f519' => 
    array (
      0 => 'D:\\laragon\\www\\davila\\custom\\vivescloud\\templates\\pagos.tpl',
      1 => 1613065226,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 3,
  ),
),false)) {
function content_60256c0f2cd7d2_79168481 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
if (!(isset($_smarty_tpl->tpl_vars['facturaspendientes']->value))) {?>
    <form action="<?php echo $_smarty_tpl->tpl_vars['actionform']->value;?>
" method="POST">
        <div>
            <table class="border centpercent">
                <input type="hidden" name="action" value="consulta">
                <tr>
                    <td>
                        <label for="fechainicio">Fecha de Inicio</label><input type="date" id="fechainicio"
                            name="fechainicio">
                    </td>
                    <td>
                        <label for="fechafin">Fecha de Fin</label><input type="date" id="fechafin" name="fechafin">
                    </td>
                    <td>
                        <label for="tipopago">Tipo de Pago</label>
                        <select id="tipopago" name="tipopago">
                            <option value="LIQ">Efectivo</option>
                            <option value="28">Tarjeta de Débito</option>
                            <option value="CB">Tarjeta de Crédito</option>
                        </select>
                    </td>
                    <td>
                        <label for="sucursal">Sucursal</label>
                        <select id="sucursal" name="sucursal">
                            <option value="1">Guadalajara</option>
                            <option value="2">Vallarta</option>
                        </select>
                    </td>
                     <td>
                         <label for="moneda">Moneda</label>
                         <select id="moneda" name="moneda">
                             <option value="MXN">Pesos</option>
                             <option value="USD">Dólares USD</option>
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
    </form>
    <?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}?>

<?php if ((isset($_smarty_tpl->tpl_vars['facturaspendientes']->value))) {?>
    <div>
        <h2>Reporte de pagos: </h2>
    </div>
    <form action="<?php echo $_smarty_tpl->tpl_vars['actionform']->value;?>
" method="POST" id="pagarfacturas">
        <div>
            <label for="bank">Seleccione el banco de destino: </label>
            <select name="bank">
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['bancos']->value, 'i', false, 'key');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['i']->value->rowid;?>
"><?php echo $_smarty_tpl->tpl_vars['i']->value->label;?>
</option>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </select>
        </div>

        <table id="tabla">
            <thead>
                <th>Factura</th>
                <th>Fecha Emisión</th>
                <th>Cliente</th>
                <th>RFC</th>
                <th>Monto Total</th>
                <th>Moneda</th>
                <th>Tipo de Pago</th>
                <th>Sucursal</th>
                <th>Acciones</th>
            </thead>

            <tbody>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['facturaspendientes']->value, 'i', false, 'key');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>

                    <tr>
                        <td><a href="/compta/facture/card.php?facid=<?php echo $_smarty_tpl->tpl_vars['i']->value['idfactura'];?>
"><?php echo $_smarty_tpl->tpl_vars['i']->value['ref'];?>
</a></td>
                        <td><?php echo $_smarty_tpl->tpl_vars['i']->value['fechaEmision'];?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['i']->value['cliente'];?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['i']->value['rfc'];?>
</td>
                        <td>$ <?php echo $_smarty_tpl->tpl_vars['i']->value['totalFactura'];?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['i']->value['moneda'];?>
</td>
                        <?php if ($_smarty_tpl->tpl_vars['i']->value['tipoPago'] == 'LIQ') {?>
                            <td>Efectivo</td>
                        <?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['i']->value['tipoPago'] == 'CB') {?>
                            <td>Tarjeta de Crédito </td>
                        <?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['i']->value['tipoPago'] == '28') {?>
                            <td>Tarjeta de Débito </td>
                        <?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['i']->value['sucursal'] == 1) {?>
                            <td>Guadalajara </td>
                        <?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['i']->value['sucursal'] == 2) {?>
                            <td>Vallarta <input type="hidden" name="sucursal[]" value="<?php echo $_smarty_tpl->tpl_vars['i']->value['sucursal'];?>
"></td>
                        <?php }?>
                        <td><input type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['i']->value['idfactura'];?>
-<?php echo $_smarty_tpl->tpl_vars['i']->value['tipoPago'];?>
-<?php echo $_smarty_tpl->tpl_vars['i']->value['sucursal'];?>
" name="idfactura[]"></td>
                    </tr>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                <tr>
                    <td style="border-top:1px solid black; text-align:center" colspan="9">

                        <input type="hidden" name="action" value="pagar">
                        <button class="butAction" id="pagar">Marcar Facturas Pagadas</button>

                    </td>
                </tr>

            </tbody>
        </table>
    </form>
    <?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
    <?php echo '<script'; ?>
>
        
            $(document).ready(function() {
                $("#tabla").DataTable({});


                /* var arr = $.map($('input:checkbox:checked'), function(e, i) {
             return +e.value;
         });
         idfactura = $(this).attr('idfactura');
         console.log(idfactura);*/

            });



         
    <?php echo '</script'; ?>
>
<?php } else { ?>
    <div>
        <span>Consulta de Reportes</span>
        <table class="noborder centpercent">
            <tr class="liste_titre">
                <td>No. Reporte</td>
                <td>Fecha</td>
                <td>Descarga</td>
            </tr>
            <tr>
                <td>REP-0001</td>
                <td>11-02-2021</td>
                <td><a href="#">Descargar PDF</a></td>
            </tr>
        </table>
    </div>
<?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}?>


<?php }
}
