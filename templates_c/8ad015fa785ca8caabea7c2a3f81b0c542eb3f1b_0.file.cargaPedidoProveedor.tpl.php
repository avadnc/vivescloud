<?php
/* Smarty version 3.1.34-dev-7, created on 2021-01-11 22:06:11
  from 'D:\laragon\www\davila\custom\vivescloud\templates\cargaPedidoProveedor.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_5ffd2033980387_40451181',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8ad015fa785ca8caabea7c2a3f81b0c542eb3f1b' => 
    array (
      0 => 'D:\\laragon\\www\\davila\\custom\\vivescloud\\templates\\cargaPedidoProveedor.tpl',
      1 => 1610424357,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
),false)) {
function content_5ffd2033980387_40451181 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<h2>Referencia Pedido Proveedor <?php echo $_smarty_tpl->tpl_vars['datosPedido']->value->ref;?>
</h2>

<h3>Insertar Lineas</h3>
<div style="display:inline-block;">
    <div style="float:left">
        <form id="insertar" action="" method="POST">
            <table class="tagtable liste listwithfilterbefore">
            <input type="hidden" id="numPedido" value="<?php echo $_smarty_tpl->tpl_vars['datosPedido']->value->id;?>
">
            <input type="hidden" id="idProd" >
            <input type="hidden" id="refProd">
                <tr>
                    <td>
                        <label for="codigo" style="margin-right: 3px;">Código</label>
                        <input type="text" id="codigo" name="codigo" placeholder="Código">
                    </td>
                    <td>
                        <label id="descripcion">Descripcion de Ejemplo</label>
                    </td>
                    <td>
                        <label for="cantidad" style="margin-right: 3px;">Cantidad</label>
                        <input type="text" id="cantidad" name="cantidad" placeholder="Cantidad">
                    </td>
                    <td>
                        <label for="precio" style="margin-right: 3px;">Precio Unitario</label>
                        <input type="text" id="precio" name="precio" placeholder="Precio">
                    </td>
                    <td>
                        <input type="submit" class="button" value="Insertar">
                    </td>
                </tr>
            </table>

        </form>
    </div>

</div>

<div class="div-table-responsive">
    <table id="tabla" class="tagtable liste listwithfilterbefore">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio de Compra</th>
               
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
<div><a class="butAction" href="/fourn/commande/card.php?id=<?php echo $_smarty_tpl->tpl_vars['datosPedido']->value->id;?>
">Volver</a></div>
<?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
 src="js/cargarpedidoprov.js" defer><?php echo '</script'; ?>
><?php }
}
