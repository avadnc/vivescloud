{include file="header.tpl"}
<pre>
{$datosFactura['fechainicio']}
</pre>
<div>
    <table class="border centpercent">
        <tr>
            <td>
                <input type="hidden" id="idcliente" name="idcliente" value="{$datosFactura['thirdparty'][0]->id}"><label
                    for="Cliente">Cliente: </label><input type="text" id="cliente" name="cliente"
                    value="{$datosFactura['thirdparty'][0]->name}" disabled>
            </td>
            <td>
                <label for="fechainicio">Fecha de Inicio</label><input type="date" id="fechainicio"
                    value="{$datosFactura['fechainicio']}" name="fechainicio" disabled>
            </td>
            <td>
                <label for="fechafin">Fecha de Fin</label><input type="date" id="fechafin"
                    value="{$datosFactura['fechafin']}" name="fechafin" disabled>
            </td>
            <td>
                {* <label for="tipopago">Tipo de Pago</label>
                <select id="tipopago">
                    <option>Efectivo</option>
                    <option>Tarjeta de Débito</option>
                    <option>Tarjeta de Crédito</option>
                </select> *}
            </td>
        </tr>
        <tr>
            <td>
                <input type="submit" id="enviar" class="button" value="Crear Factura">
            </td>

        </tr>
        <table>
</div>

{if $tabla}
    {* {$tabla|@var_dump} *}
    <div class="div-table-responsive">
        <table id="tabla" class="tagtable liste listwithfilterbefore">
            <thead>
                <tr>
                    <th>Serie</th>
                    <th>Fecha</th>
                    <th>Tipo de Pago</th>
                    <th>Monto Sin Iva</th>
                    <th>Total</th>
                    <th>Seleccionar</th>
                </tr>
            </thead>
            <tbody>
                {foreach $tabla key=k item=i}
                    <tr>
                        <td><a href="/compta/facture/card.php?facid={$i->rowid}">{$i->ref}</a></td>
                        <td>{$i->date_valid}</td>
                        <td>
                            {if $i->fk_mode_reglement eq '4'}
                                Efectivo
                            {/if}
                            {if $i->fk_mode_reglement eq '6'}
                                Tarjeta de Crédito
                            {/if}
                            {if $i->fk_mode_reglement eq '64'}
                                Tarjeta de Débito
                            {/if}

                        </td>
                        <td>{$i->total|string_format:"%.2f"}</td>
                        <td>{$i->total_ttc|string_format:"%.2f"}</td>
                        <td><input type="checkbox" id="rowidFactura[]" value="{$i->rowid}"></td>
                    </tr>
                {/foreach}

            </tbody>
        </table>
    </div>
{/if}

{include file="footer.tpl"}

<script>
    {literal}
        $(document).ready(function() {
            $("#tabla").DataTable();

            $("#enviar").click(function() {
                socid = $("#idcliente").val();
                var arr = $.map($('input:checkbox:checked'), function(e, i) {
                    return +e.value;
                });

                $.ajax({
                    url: '/custom/vivescloud/facturaglobal.php',
                    data: {
                        socid: socid,
                        rowidFactura: arr,
                        action: 'validate'
                    },
                    type: 'POST',
                    success: function(data) {
                        console.log(data);
                        $(location).attr('href', '/compta/facture/card.php?facid=' + data);
                    }
                });
            });

        });
    {/literal}
</script>