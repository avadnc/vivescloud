<?php
/* Smarty version 3.1.34-dev-7, created on 2021-02-09 13:19:52
  from 'D:\laragon\www\davila\custom\vivescloud\templates\pagos.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_6022e05809a659_07345316',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cfe265da79bf0f13d07e125b0afbe6363cd7f519' => 
    array (
      0 => 'D:\\laragon\\www\\davila\\custom\\vivescloud\\templates\\pagos.tpl',
      1 => 1612898384,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
),false)) {
function content_6022e05809a659_07345316 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
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
            </tr>
            <tr>
                <td>
                    <input type="submit" id="enviar" class="button" value="Consultar">
                </td>
            </tr>
            <table>
    </div>
</form>

<?php if ((isset($_smarty_tpl->tpl_vars['facturaspendientes']->value))) {?>

    <table id="tabla">
        <thead>
            <th>Factura</th>
            <th>Fecha Emisión</th>
            <th>Cliente</th>
            <th>RFC</th>
            <th>Monto Total</th>
            <th>Moneda</th>
            <th>Tipo de Pago</th>
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
                    <?php if ($_smarty_tpl->tpl_vars['i']->value['tipoPago'] == 0) {?>
                        <td>Efectivo</td>
                    <?php }?>

                    <td><input type="checkbox" idfactura="<?php echo $_smarty_tpl->tpl_vars['i']->value['idfactura'];?>
" id="idfactura" name="idfactura[]"></td>
                </tr>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            <tr>
                <td style="border-top:1px solid black; text-align:center" colspan="8">
                    <button class="butAction" id="pagar">Marcar Facturas Pagadas</button>
                </td>
            </tr>

        </tbody>
    </table>

<?php } else { ?>
    <span>Seleccione la consulta</span>

<?php }?>

<?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
    
        $(document).ready(function() {
            $("#tabla").DataTable();

            $("#pagar").click(function(e) {
                e.preventDefault();
                console.log('enviando');
            });
        });


    
<?php echo '</script'; ?>
><?php }
}
