{include file="header.tpl"}
<form action="{$actionform}" method="POST">
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

{if isset($facturaspendientes)}

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
            {foreach from=$facturaspendientes key=$key item=$i}
                <tr>
                    <td><a href="/compta/facture/card.php?facid={$i['idfactura']}">{$i['ref']}</a></td>
                    <td>{$i['fechaEmision']}</td>
                    <td>{$i['cliente']}</td>
                    <td>{$i['rfc']}</td>
                    <td>$ {$i['totalFactura']}</td>
                    <td>{$i['moneda']}</td>
                    {if $i['tipoPago'] eq 0}
                        <td>Efectivo</td>
                    {/if}

                    <td><input type="checkbox" idfactura="{$i['idfactura']}" id="idfactura" name="idfactura[]"></td>
                </tr>
            {/foreach}
            <tr>
                <td style="border-top:1px solid black; text-align:center" colspan="8">
                    <button class="butAction" id="pagar">Marcar Facturas Pagadas</button>
                </td>
            </tr>

        </tbody>
    </table>

{else}
    <span>Seleccione la consulta</span>

{/if}

{include file="footer.tpl"}
<script>
    {literal}
        $(document).ready(function() {
            $("#tabla").DataTable();

            $("#pagar").click(function(e) {
                 e.preventDefault();
                console.log('enviando');
            });
        });


    {/literal}
</script>