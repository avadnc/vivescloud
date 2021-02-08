<?php
/* Smarty version 3.1.34-dev-7, created on 2021-01-03 19:20:44
  from 'D:\laragon\www\davila\custom\vivescloud\templates\listaprecios.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_5ff26d6c22f7a4_47095930',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1b1ac337958e2c3e908d10c3d82d0a368af2dfc3' => 
    array (
      0 => 'D:\\laragon\\www\\davila\\custom\\vivescloud\\templates\\listaprecios.tpl',
      1 => 1609723217,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
),false)) {
function content_5ff26d6c22f7a4_47095930 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<h2>Lista Dinámica de Precios</h2>
<h2>Tipo de Cambio</h2>
<div style="display:inline-block;">
    <div style="float:left">
        <label id="ultimocambiodolar"></label><img id="loaderdolar" src="img/load.gif" width="20px"><br>
        <input type="hidden" id="preciodolar">

    </div>
</div><br>
<div style="display:inline-block;">
    <div style="float:left">
        <label id="ultimocambioeuro"></label><img id="loadereuro" src="img/load.gif" width="20px"><br>
        <input type="hidden" id="precioeuro">

    </div>
</div><br>
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
</div><br>

<div class="div-table-responsive">
    <div style="display:inline-block;" class="botones">
        <h3>Mostrar Precio</h3>
        Mostrador<input id="4" type="checkbox" name="mostrador" checked>Minorista<input id="5" type="checkbox"
            name="mostrador" checked>Mayorista<input id="6" type="checkbox" name="mostrador" checked>
        <button id="cesta" class="button">Consultar Nota<img width="12px" src="img/add-to-cart.png"></button>
    </div>
    <table id="tabla" class="tagtable liste listwithfilterbefore">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Stock</th>
                <th>Moneda</th>
                <th>Mostrador</th>
                <th>Minorista</th>
                <th>Mayorista</th>
                <th>Marcar</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>


    <div id="items">


    </div>


    <div id="cotizacion" title="Hola mundo desde modal">
    <button id="btnPrint">Imprimir</button>
        
    </div>
    <?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
    <?php echo '<script'; ?>
 src="js/listaprecios.js" defer><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="js/printThis.js" defer><?php echo '</script'; ?>
><?php }
}
