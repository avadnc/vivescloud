{include file="header.tpl"}
{if !isset($facturaspendientes)}
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
                     <td>
                         <label for="moneda">Moneda</label>
                         <select id="moneda" name="moneda">
                             <option value="MXN">Pesos</option>
                             <option value="USD">Dólares USD</option>
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
    {include file="footer.tpl"}
{/if}

{if isset($facturaspendientes)}
    <div>
        <h2>Reporte de pagos: </h2>
    </div>
    <form action="{$actionform}" method="POST" id="pagarfacturas">
        <div>
            <label for="bank">Seleccione el banco de destino: </label>
            <select name="bank">
                {foreach from=$bancos item=$i key=$key}
                    <option value="{$i->rowid}">{$i->label}</option>
                {/foreach}
            </select>
        </div>

        <table id="tabla">
            <thead>
                <th>Factura</th>
                <th>Fecha Emisión</th>
                <th>Cliente</th>
                <th>RFC</th>
                <th>Monto Total</th>
                <th>Moneda</th>
                <th>Tipo de Pago</th>
                <th>Sucursal</th>
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
                        {if $i['tipoPago'] eq 'LIQ'}
                            <td>Efectivo</td>
                        {/if}
                        {if $i['tipoPago'] eq 'CB'}
                            <td>Tarjeta de Crédito </td>
                        {/if}
                        {if $i['tipoPago'] eq '28'}
                            <td>Tarjeta de Débito </td>
                        {/if}
                        {if $i['sucursal'] eq 1}
                            <td>Guadalajara </td>
                        {/if}
                        {if $i['sucursal'] eq 2}
                            <td>Vallarta <input type="hidden" name="sucursal[]" value="{$i['sucursal']}"></td>
                        {/if}
                        <td><input type="checkbox" value="{$i['idfactura']}-{$i['tipoPago']}-{$i['sucursal']}" name="idfactura[]"></td>
                    </tr>
                {/foreach}
                <tr>
                    <td style="border-top:1px solid black; text-align:center" colspan="9">

                        <input type="hidden" name="action" value="pagar">
                        <button class="butAction" id="pagar">Marcar Facturas Pagadas</button>

                    </td>
                </tr>

            </tbody>
        </table>
    </form>
    {include file="footer.tpl"}
    <script>
        {literal}
            $(document).ready(function() {
                $("#tabla").DataTable({});


                /* var arr = $.map($('input:checkbox:checked'), function(e, i) {
             return +e.value;
         });
         idfactura = $(this).attr('idfactura');
         console.log(idfactura);*/

            });



        {/literal} 
    </script>
{else}
    <div>
        <span>Consulta de Reportes</span>
        <table class="noborder centpercent">
            <tr class="liste_titre">
                <td>No. Reporte</td>
                <td>Fecha</td>
                <td>Descarga</td>
            </tr>
            <tr>
                <td>REP-0001</td>
                <td>11-02-2021</td>
                <td><a href="#">Descargar PDF</a></td>
            </tr>
        </table>
    </div>
{include file="footer.tpl"}
{/if}


