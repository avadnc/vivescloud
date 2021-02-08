{include file="header.tpl"}
<div>
    <table class="border centpercent">
        <tr>
            <td>
                <label for="fechainicio">Fecha de Inicio</label><input type="date" id="fechainicio" name="fechainicio">
            </td>
            <td>
                <label for="fechafin">Fecha de Fin</label><input type="date" id="fechafin" name="fechafin">
            </td>
            <td>
                <label for="tipopago">Tipo de Pago</label>
                <select id="tipopago">
                    <option value="LIQ">Efectivo</option>
                    <option value="28">Tarjeta de Débito</option>
                    <option value="CB">Tarjeta de Crédito</option>
                </select>
            </td>
            <td>
                <label for="sucursal">Sucursal</label>
                <select id="sucursal">
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

<table id="tabla">
    <thead>
        <th>Factura</th>
        <th>Fecha Emisión</th>
        <th>Cliente</th>
        <th>Monto Total</th>
        <th>Moneda</th>
        <th>Tipo de Pago</th>
        <th>Acciones</th>
    </thead>

    <tbody>

    </tbody>

</table>
{include file="footer.tpl"}
<script>
    {literal}


        $(document).ready(function() {

            var table = $("#tabla").DataTable({

            });
            $("#enviar").click(function(e) {



                fechainicio = $("#fechainicio").val();
                fechafin = $("#fechafin").val();
                tipopago = $("#tipopago").val();
                sucursal = $("#sucursal").val();

                
          table.destroy();

                datos = new FormData();
                datos.append('action', 'obtenerFacturas');
                datos.append('fechainicio', fechainicio);
                datos.append('fechafin', fechafin);
                datos.append('tipopago', tipopago);
                datos.append('sucursal', sucursal);

                //console.log(datos);

                $.ajax({
                    url: "/custom/vivescloud/ajax/pagosMasivos.php",
                    method: 'POST',
                    data: datos,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                       
                        table = $("#tabla").DataTable({});
                        $.each(data, function(key,value){
                           
                           table.row.add([
                                value.ref,
                                value.fechaEmision,
                                value.cliente,
                                value.totalFactura,
                                tipopago,
                                'Pendiente de Pago',
                                '<input type="checkbox">',
                           ]).draw(false);
                        });
                        
                    }
                });
                /*table = $("#tabla").DataTable({

                ajax:{
                   url: "/custom/vivescloud/ajax/pagosMasivos.php",
                   method: 'GET',
                   data: datos,
                   cache: false,
                   contentType: false,
                   processData: false,
                   dataType: 'json',
                    success: function(data) {
                        console.log(data);
                    },


                }
            });*/
            })


            /*data = new FormData();
        data.append();*/
            /* $.ajax({
             url: window.location
             type: "POST",
             dataType: "JSON",

         });*/

        });
    {/literal}
</script>