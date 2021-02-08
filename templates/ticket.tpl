<html>

<head>
    <title>
        Imprimir Tikcket
    </title>
    <link rel="stylesheet" type="text/css" href="/custom/vivescloud/css/ticket.css">
</head>

<body>
    <pre>
{* {$datosTicket['cliente']|@var_dump} *}
</pre>
    <div class="entete">
        <div class="logo">
            <img src="{$datosTicket['logo']}">
        </div>
        <div class="infos">
            <p class="address">
                {$datosTicket['empresa']}<br>
                RFC: {$datosTicket['rfc']}<br>
                Dirección: {$datosTicket['direccion']}
            </p>
            <p class="address">
                Cliente: {$datosTicket['cliente']->name}<br>
                RFC: {$datosTicket['cliente']->idprof1}
            </p>

            <p style="align:right;" class="date_heure">
                Fecha: {$datosTicket['fecha']}<br>
                Comprobante: {$datosTicket['ticket']}

            </p>

        </div>
    </div>

    <br>

    <table class="liste_articles">
        <thead>
            <tr class="titres">
                <th>
                    Código
                </th>
                <th>
                    Descripción
                </th>
                <th>
                    Cantidad
                </th>

                <th>
                    Total
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$datosTicket['lineas'] key=$k item=$i}
                <tr>
                    <td>
                        {$i->product_ref}
                    </td>
                    <td>
                        {$i->product_label}
                    </td>
                    <td>
                        {$i->qty}
                    </td>
                    <td class="total">
                        $ {$i->total_ht|string_format:"%.2f"}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>

    <table class="totaux">
        <tr>
            <th class="nowrap">
                SubTotal
            </th>
            <td class="nowrap">
                $ {$datosTicket['subtotal']|string_format:"%.2f"}
                {* <?php echo price(price2num($obj_facturation->prixTotalHt(), 'MT'), '', $langs, 0, -1, -1, $conf->currency)."\n"; ?> *}
            </td>
        </tr>
        <tr>
            <th class="nowrap">
                IVA</th>
            <td class="nowrap">
                $ {$datosTicket['iva']|string_format:"%.2f"}
                {* '.price(price2num($obj_facturation->montantTva(), 'MT'), '', $langs, 0, -1, -1, $conf->currency)."\n"; ?> *}
            </td>
        </tr>
        <tr>
            <th class="nowrap">
                Total
            </th>
            <td class="nowrap">
                $
                {$datosTicket['total']|string_format:"%.2f"}{* '.price(price2num($obj_facturation->prixTotalTtc(), 'MT'), '', $langs, 0, -1, -1, $conf->currency)."\n"; ?> *}
            </td>
        </tr>
    </table>

    {* {if isset($datosXml['comprobante'])}
        <table class="datos_fiscales">
            <tr>
                <td colspan="2">
                    <h2>Datos CFDI</h2>
                </td>
            </tr>
            <tr>
                <td>
                    <h3>Régimen Fiscal:</h3>
                </td>

                <td>{$datosXml['regimenFiscal']}</td>
            </tr>
            <tr>
                <td>
                    <h3>Tipo de Pago:</h3>
                </td>

                <td>{$datosXml['tipoPago']}</td>
            </tr>
            <tr>
                <td>
                    <h3>Forma de Pago:</h3>
                </td>

                <td>{$datosXml['formaPago']}</td>
            </tr>
            <tr>
                <td>
                    <h3>Uso CFDI:</h3>
                </td>

                <td>{$datosXml['usoCfdi']}</td>
            </tr>
             <tr>
                 <td>
                     <h3>No Certificado Emisor:</h3>
                 </td>

                 <td>{$datosXml['comprobante']['NoCertificado']}</td>
             </tr>
             <tr>
                 <td>
                     <h3>No Certificado SAT:</h3>
                 </td>

                 <td>{$datosXml['tfd']['NoCertificadoSAT']}</td>
             </tr>
             <tr>
                 <td>
                     <h3>Fecha Emision:</h3>
                 </td>

                 <td>{$datosXml['comprobante']['Fecha']}</td>
             </tr>
             <tr>
                 <td>
                     <h3>Fecha Certificación:</h3>
                 </td>

                 <td>{$datosXml['tfd']['FechaTimbrado']}</td>
             </tr>
              <tr>
                  <td>
                      <h3>Fecha Certificación:</h3>
                  </td>

                  <td>{$datosXml['tfd']['FechaTimbrado']}</td>
              </tr>
        </table>
    {/if} *}
        <div>
            <p>Gracias Por Su Compra</p>
        </div>
        {* 
    <script type="text/javascript">
        window.print();
    </script> *}
    </body>

    </html>