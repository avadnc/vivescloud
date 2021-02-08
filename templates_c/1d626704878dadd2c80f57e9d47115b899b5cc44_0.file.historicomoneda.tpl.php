<?php
/* Smarty version 3.1.34-dev-7, created on 2020-12-01 20:24:18
  from 'D:\laragon\www\davila\custom\vivescloud\templates\historicomoneda.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_5fc6fad20e7a08_13275216',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1d626704878dadd2c80f57e9d47115b899b5cc44' => 
    array (
      0 => 'D:\\laragon\\www\\davila\\custom\\vivescloud\\templates\\historicomoneda.tpl',
      1 => 1606875786,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
),false)) {
function content_5fc6fad20e7a08_13275216 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="div-table-responsive">
    <table id="tabla" class="tagtable liste listwithfilterbefore">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Moneda</th>
                <th>Tipo de Cambio</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
 src="js/monedas.js" defer><?php echo '</script'; ?>
><?php }
}
