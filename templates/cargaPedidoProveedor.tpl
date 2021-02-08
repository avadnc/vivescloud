{include file="header.tpl"}

{* <h2>Tipo de Cambio</h2>
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
<h2>Insertar Lineas</h2>
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
*}
<h2>Referencia Pedido Proveedor {$datosPedido->ref}</h2>

<h3>Insertar Lineas</h3>
<div style="display:inline-block;">
    <div style="float:left">
        <form id="insertar" action="" method="POST">
            <table class="tagtable liste listwithfilterbefore">
            <input type="hidden" id="numPedido" value="{$datosPedido->id}">
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
{* <pre>
{$datosPedido|@var_dump}
</pre> *}

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
<div><a class="butAction" href="/fourn/commande/card.php?id={$datosPedido->id}">Volver</a></div>
{include file="footer.tpl"}
<script src="js/cargarpedidoprov.js" defer></script>