<?php
/* Smarty version 3.1.34-dev-7, created on 2021-01-17 00:25:35
  from 'D:\laragon\www\davila\custom\vivescloud\templates\facturaglobal.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_600383ffb40414_43036900',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4d804266233998a0bbbe0d3be990b4c402716405' => 
    array (
      0 => 'D:\\laragon\\www\\davila\\custom\\vivescloud\\templates\\facturaglobal.tpl',
      1 => 1610842611,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
),false)) {
function content_600383ffb40414_43036900 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<pre>
<?php echo $_smarty_tpl->tpl_vars['datosFactura']->value['fechainicio'];?>

</pre>
<div>
    <table class="border centpercent">
        <tr>
            <td>
                <input type="hidden" id="idcliente" name="idcliente" value="<?php echo $_smarty_tpl->tpl_vars['datosFactura']->value['thirdparty'][0]->id;?>
"><label
                    for="Cliente">Cliente: </label><input type="text" id="cliente" name="cliente"
                    value="<?php echo $_smarty_tpl->tpl_vars['datosFactura']->value['thirdparty'][0]->name;?>
" disabled>
            </td>
            <td>
                <label for="fechainicio">Fecha de Inicio</label><input type="date" id="fechainicio"
                    value="<?php echo $_smarty_tpl->tpl_vars['datosFactura']->value['fechainicio'];?>
" name="fechainicio" disabled>
            </td>
            <td>
                <label for="fechafin">Fecha de Fin</label><input type="date" id="fechafin"
                    value="<?php echo $_smarty_tpl->tpl_vars['datosFactura']->value['fechafin'];?>
" name="fechafin" disabled>
            </td>
            <td>
                            </td>
        </tr>
        <tr>
            <td>
                <input type="submit" id="enviar" class="button" value="Crear Factura">
            </td>

        </tr>
        <table>
</div>

<?php if ($_smarty_tpl->tpl_vars['tabla']->value) {?>
        <div class="div-table-responsive">
        <table id="tabla" class="tagtable liste listwithfilterbefore">
            <thead>
                <tr>
                    <th>Serie</th>
                    <th>Fecha</th>
                    <th>Tipo de Pago</th>
                    <th>Monto Sin Iva</th>
                    <th>Total</th>
                    <th>Seleccionar</th>
                </tr>
            </thead>
            <tbody>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tabla']->value, 'i', false, 'k');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
                    <tr>
                        <td><a href="/compta/facture/card.php?facid=<?php echo $_smarty_tpl->tpl_vars['i']->value->rowid;?>
"><?php echo $_smarty_tpl->tpl_vars['i']->value->ref;?>
</a></td>
                        <td><?php echo $_smarty_tpl->tpl_vars['i']->value->date_valid;?>
</td>
                        <td>
                            <?php if ($_smarty_tpl->tpl_vars['i']->value->fk_mode_reglement == '4') {?>
                                Efectivo
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['i']->value->fk_mode_reglement == '6') {?>
                                Tarjeta de Crédito
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['i']->value->fk_mode_reglement == '64') {?>
                                Tarjeta de Débito
                            <?php }?>

                        </td>
                        <td><?php echo sprintf("%.2f",$_smarty_tpl->tpl_vars['i']->value->total);?>
</td>
                        <td><?php echo sprintf("%.2f",$_smarty_tpl->tpl_vars['i']->value->total_ttc);?>
</td>
                        <td><input type="checkbox" id="rowidFactura[]" value="<?php echo $_smarty_tpl->tpl_vars['i']->value->rowid;?>
"></td>
                    </tr>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

            </tbody>
        </table>
    </div>
<?php }?>

<?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php echo '<script'; ?>
>
    
        $(document).ready(function() {
            $("#tabla").DataTable();

            $("#enviar").click(function() {
                socid = $("#idcliente").val();
                var arr = $.map($('input:checkbox:checked'), function(e, i) {
                    return +e.value;
                });

                $.ajax({
                    url: '/custom/vivescloud/facturaglobal.php',
                    data: {
                        socid: socid,
                        rowidFactura: arr,
                        action: 'validate'
                    },
                    type: 'POST',
                    success: function(data) {
                        console.log(data);
                        $(location).attr('href', '/compta/facture/card.php?facid=' + data);
                    }
                });
            });

        });
    
<?php echo '</script'; ?>
><?php }
}
