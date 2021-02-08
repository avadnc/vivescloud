<?php
/* Smarty version 3.1.34-dev-7, created on 2020-12-07 12:38:19
  from 'D:\laragon\www\davila\custom\vivescloud\templates\actualizaprecios.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_5fce769bcab965_85548045',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '64ff7c75efde79826fb952d60365239e4881f337' => 
    array (
      0 => 'D:\\laragon\\www\\davila\\custom\\vivescloud\\templates\\actualizaprecios.tpl',
      1 => 1607362446,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
),false)) {
function content_5fce769bcab965_85548045 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<h2>Tipo de Cambio</h2>
<div style="display:inline-block;">
    <div style="float:left">
        <label id="ultimocambiodolar"></label><img id="loaderdolar" src="img/load.gif" width="20px"><br>
        <form id="cambiodolar" action="" method="POST">

            <label for="dolar" style="margin-right: 3px;">Introducir Tipo de Cambio</label>
            <input type="text" id="dolar" name="dolar" placeholder="Tipo Cambio Dolar">

            <input type="submit" class="button" value="Actualizar">
        </form>
    </div>
</div><br>
<div style="display:inline-block;">
    <div style="float:left">
        <label id="ultimocambioeuro"></label><img id="loadereuro" src="img/load.gif" width="20px"><br>
        <form id="cambioeuro" action="" method="POST">
            <label for="euro" style="margin-right: 3px;">Introducir Tipo de Cambio</label>
            <input type="text" id="euro" name="euro" placeholder="Tipo Cambio Euro">
            <input type="submit" class="button" value="Actualizar">
        </form>
    </div>
</div>

<h2>Buscar Productos</h2>
<div style="display:inline-block;">
    <div style="float:left">
        <form id="buscar" action="" method="POST">
            <label for="codigo" style="margin-right: 3px;">Buscar Producto Por Codigo/Descripción</label>
            <input type="text" id="codigo" name="codigo" placeholder="Buscar Producto">

            <label for="marca">Buscar Por Marca</label>
            <select style="width: 200px;" name="marca" id="marca"></select>

            <input type="submit" class="button" value="Buscar">
        </form>
    </div>

</div>

<div class="div-table-responsive">
    <table id="tabla" class="tagtable liste listwithfilterbefore">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Moneda</th>
                <th>Precio en Moneda</th>
                <th>Precio Compra</th>
                <th>Margen 1</th>
                <th>Precio 1</th>
                <th>Margen 2</th>
                <th>Precio 2</th>
                <th>Margen 3</th>
                <th>Precio 3</th>

            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
 src="js/actualizar.js" defer><?php echo '</script'; ?>
><?php }
}
