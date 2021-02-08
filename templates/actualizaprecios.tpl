{include file="header.tpl"}

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

{include file="footer.tpl"}
<script src="js/actualizar.js" defer></script>