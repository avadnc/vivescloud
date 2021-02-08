{include file="header.tpl"}
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
    {include file="footer.tpl"}
    <script src="js/listaprecios.js" defer></script>
    <script src="js/printThis.js" defer></script>