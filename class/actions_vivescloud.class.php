<?php
/* Copyright (C) 2021 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    vivescloud/class/actions_vivescloud.class.php
 * \ingroup vivescloud
 * \brief   Example hook overload.
 *
 * Put detailed description here.
 */

/**
 * Class ActionsVivescloud
 */
class ActionsVivescloud
{
    /**
     * @var DoliDB Database handler.
     */
    public $db;

    /**
     * @var string Error code (or message)
     */
    public $error = '';

    /**
     * @var array Errors
     */
    public $errors = array();

    /**
     * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
     */
    public $results = array();

    /**
     * @var string String displayed by executeHook() immediately after return
     */
    public $resprints;

    /**
     * Constructor
     *
     *  @param        DoliDB        $db      Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Execute action
     *
     * @param    array            $parameters        Array of parameters
     * @param    CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
     * @param    string            $action          'add', 'update', 'view'
     * @return    int                             <0 if KO,
     *                                           =0 if OK but we want to process standard actions too,
     *                                            >0 if OK and we want to replace standard actions.
     */
    public function getNomUrl($parameters, &$object, &$action)
    {
        global $db, $langs, $conf, $user;
        $this->resprints = '';
        return 0;
    }

    /**
     * Overloading the doActions function : replacing the parent's function with the one below
     *
     * @param   array           $parameters     Hook metadatas (context, etc...)
     * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
     * @param   string          $action         Current action (if set). Generally create or edit or null
     * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
     * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
     */
    public function doActions($parameters, &$object, &$action, $hookmanager)
    {
        global $conf, $user, $langs;

        $error = 0; // Error counter

        // var_dump($parameters['currentcontext']);
        // exit;
        /* print_r($parameters); print_r($object); echo "action: " . $action; */
        if (in_array($parameters['currentcontext'], array('invoicecard'))) // do something only for the context 'somecontext1' or 'somecontext2'
        {

            // Do what you want here...
            // You can for example call global vars like $fieldstosearchall to overwrite them, or update database depending on $action and $_POST values.
        }

        if (!$error) {
            $this->results = array('myreturn' => 999);
            $this->resprints = 'A text to show';
            return 0; // or return 1 to replace standard code
        } else {
            $this->errors[] = 'Error message';
            return -1;
        }
    }

    /**
     * Overloading the doMassActions function : replacing the parent's function with the one below
     *
     * @param   array           $parameters     Hook metadatas (context, etc...)
     * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
     * @param   string          $action         Current action (if set). Generally create or edit or null
     * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
     * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
     */
    public function doMassActions($parameters, &$object, &$action, $hookmanager)
    {
        global $conf, $user, $langs;

        $error = 0; // Error counter

        /* print_r($parameters); print_r($object); echo "action: " . $action; */
        if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) // do something only for the context 'somecontext1' or 'somecontext2'
        {
            foreach ($parameters['toselect'] as $objectid) {
                // Do action on each object id
            }
        }

        if (!$error) {
            $this->results = array('myreturn' => 999);
            $this->resprints = 'A text to show';
            return 0; // or return 1 to replace standard code
        } else {
            $this->errors[] = 'Error message';
            return -1;
        }
    }

    /**
     * Overloading the addMoreMassActions function : replacing the parent's function with the one below
     *
     * @param   array           $parameters     Hook metadatas (context, etc...)
     * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
     * @param   string          $action         Current action (if set). Generally create or edit or null
     * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
     * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
     */
    public function addMoreMassActions($parameters, &$object, &$action, $hookmanager)
    {
        global $conf, $user, $langs;

        $error = 0; // Error counter

        //  print_r($parameters); print_r($object); echo "action: " . $action;
        // if (in_array($parameters['currentcontext'], array('invoicecard', 'somecontext2'))) // do something only for the context 'somecontext1' or 'somecontext2'
        // {
        //     $this->resprints = '<option value="0"' . ($disabled ? ' disabled="disabled"' : '') . '>' . $langs->trans("VivescloudMassAction") . '</option>';
        // }

        // if (!$error) {
        //     return 0; // or return 1 to replace standard code
        // } else {
        //     $this->errors[] = 'Error message';
        //     return -1;
        // }
    }

    /**
     * Overloading the formObjectOptions function : replacing the parent's function with the one below
     *
     * @param   array()         $parameters     Hook metadatas (context, etc...)
     * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
     * @param   string          &$action        Current action (if set). Generally create or edit or null
     * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
     * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
     */

    public function formObjectOptions($parameters, &$object, &$action, $hookmanager)
    {
        global $conf, $user, $langs, $db;

        $error = 0; // Error counter
        // echo '<pre>';
        // var_dump($parameters);
        // exit;

        if (in_array($parameters['currentcontext'], array('thirdpartycomm'))) {

            if (!$user->rights->vivescloud->permitirfactura->write) {
                echo '<script>$(document).ready(function(){ $("[href*=\'multiprix\']").hide(); });  </script>';

            }
        }
        if (in_array($parameters['currentcontext'], array('invoicesuppliercard'))) {
            // echo '<pre>';
            // var_dump($object);
            // echo '</pre>';
            if ($object->multicurrency_code == "USD") {
                $tipoCambio = $object->multicurrency_tx;
                $resultado = $object->total_ttc * $tipoCambio;

                echo '<script>
                $(document).ready(function(){
                  $(".amountremaintopay").html("$ ' . round(price2num($resultado), 2) . ' USD");
                   });</script>';

            }
            if ($object->multicurrency_code == "EUR") {
                $tipoCambio = $object->multicurrency_tx;
                $resultado = $object->total_ttc * $tipoCambio;

                echo '<script>
                $(document).ready(function(){
                  $(".amountremaintopay").html("€ ' . round(price2num($resultado), 2) . ' EUR");
                   });</script>';

            }

        }

        if (in_array($parameters['currentcontext'], array('stockproductcard'))) {
            dol_include_once('/vivescloud/class/ConsultasStockProductos.class.php');
            dol_include_once('/vivescloud/vendor/autoload.php');

            // echo '<pre>';
            // var_dump($object->stock_warehouse);
            // echo '</pre>';
            // exit;

            if ($object->array_options["options_batch"] == 1) {

                $campos = ['pb.rowid', 'pb.batch', 'pb.qty', 'ent.ref'];
                $select = new PO\QueryBuilder\Statements\Select();
                $select->select($campos);
                $select->from(MAIN_DB_PREFIX . 'product_batch AS pb');
                $select->innerJoin(MAIN_DB_PREFIX . 'product_stock', 'pb.fk_product_stock  = ' . MAIN_DB_PREFIX . 'product_stock.rowid');
                $select->innerJoin(MAIN_DB_PREFIX . 'entrepot AS ent', MAIN_DB_PREFIX . 'product_stock.fk_entrepot = ent.rowid');
                $select->where(MAIN_DB_PREFIX . 'product_stock.fk_product', $object->id, '=');
                $select->where('pb.qty', 0, '>');
                $select->orderBy('ent.ref');

                $resql = $db->query($select->toSql());
                $num = $db->num_rows($resql);
                $pedimentos = [];

                $totalStock = 0;
                $i = 0;
                if ($num) {
                    while ($i < $num) {

                        $obj = $db->fetch_object($resql);
                        array_push($pedimentos, $obj);
                        $i++;
                    }
                }

                $lineas = count($pedimentos);
                echo '<tr><td><strong>Pedimento</strong></td>';
                echo '<td><table class="noborder centpercent">';
                echo '<thead><tr class="liste_titre"><th>Almacén</th><th align="center">Pedimento</th><th align="center">Stock</th></tr></thead><tbody>';

                for ($i = 0; $i < $lineas; $i++) {

                    echo '<tr>';
                    echo '<td align="left"><span class="fas fa-box-open paddingright classfortooltip" style=" color: #a69944;"></span>' . $pedimentos[$i]->ref . '</td>';
                    echo '<td align="center">' . $pedimentos[$i]->batch . '</td>';
                    echo '<td align="center">' . $pedimentos[$i]->qty . '</td>';
                    echo '</tr>';

                }

                $field = ['rowid', 'ref'];
                $consulta = new PO\QueryBuilder\Statements\Select();
                $consulta->select($field);
                $consulta->from(MAIN_DB_PREFIX . 'entrepot');

                $resultado = $db->query($consulta->toSql());
                $num = $db->num_rows($resultado);
                $almacen = [];
                $i = 0;
                if ($num) {
                    while ($i < $num) {

                        $obj = $db->fetch_object($resultado);
                        array_push($almacen, $obj);
                        $i++;
                    }
                }

                echo '<tr><td></td><td><button id="corregir" class="butAction">Corregir Stock</button></td><td><button id="transferir" class="butAction">Trasnferir Stock</button></td></tr>';
                echo '</tbody></table></td></tr>';
                //Corrección stock
                echo '<div id="correccion_stock" title="Corrección de Stock">';
                echo '<p><label for="almacen">Seleccionar Almacén:</label><select class="selector" id="almacen">';

                $lineas = count($almacen);
                for ($i = 0; $i < $lineas; $i++) {
                    echo '<option value="' . $almacen[$i]->rowid . '">' . $almacen[$i]->ref . '</option>';
                }
                echo '</select></p>';
                echo '<p><label for="accion"> Acción: </label><select id="accion"><option value="sumar">Añadir</option><option value="restar">Quitar</option></select></p>';
                echo '<p><label for="cantidad">Cantidad:</label><input type="text" id="cantidad"></p>';
                echo '<p><label for="pedimento">Pedimento: </label><input type="text" id="pedimento" name="pedimento"></p>';
                // echo '<p><label for="pedimento">Pedimento:</label><select class="selector" id="pedimento">';

                // $lineas = count($pedimentos);

                // for ($i = 0; $i < $lineas; $i++) {
                //     echo '<option value="' . $pedimentos[$i]->batch . '">' . $pedimentos[$i]->ref . '-' . $pedimentos[$i]->batch . '</option>';
                // }

                // echo '</select></p>';
                echo '</div>';

                //Transferir Stock

                echo '<div id="transferir_stock" title="Transferir Stock">';
                echo '<p><label for="almacenorigen">Seleccionar Almacén Origen:</label><select class="selector" id="almacenorigen">';

                $lineas = count($almacen);
                for ($i = 0; $i < $lineas; $i++) {
                    echo '<option value="' . $almacen[$i]->rowid . '">' . $almacen[$i]->ref . '</option>';
                }
                echo '</select></p>';
                echo '<p><label for="almacendestino">Seleccionar Almacén Destino:</label><select class="selector" id="almacendestino">';

                $lineas = count($almacen);
                for ($i = 0; $i < $lineas; $i++) {
                    echo '<option value="' . $almacen[$i]->rowid . '">' . $almacen[$i]->ref . '</option>';
                }
                echo '</select></p>';

                echo '<p><label for="cantidadtransferencia">Cantidad:</label><input type="text" id="cantidadtransferencia"></p>';
                echo '<p><label for="pedimentotransferencia">Pedimento:</label><select class="selector" id="pedimentotransferencia">';

                $lineas = count($pedimentos);

                for ($i = 0; $i < $lineas; $i++) {
                    echo '<option value="' . $pedimentos[$i]->batch . '">' . $pedimentos[$i]->ref . '-' . $pedimentos[$i]->batch . '</option>';
                }

                echo '</select></p>';
                echo '</div>';

                echo '<input type="hidden" id="idProducto" value="' . $object->id . '">';
                echo '<script src="/custom/vivescloud/js/stocks.js"></script>';

            }

        }
        /* print_r($parameters); print_r($object); echo "action: " . $action; */
        if (in_array($parameters['currentcontext'], array('invoicecard'))) // do something only for the context 'somecontext1' or 'somecontext2'
        {
            if ($object->multicurrency_code == "USD") {
                //    echo '<pre>';var_dump($object);echo '</pre>';
                $pago = ($object->totalpaye) ? $object->totalpaye * $object->multicurrency_tx : 0;
                $restoPago = $object->multicurrency_total_ttc - $pago;

                if (round($restoPago, 2) <= 0) {
                    echo "el pago está completado";
                }

                echo '<script>
                $(document).ready(function(){
                    var multicurrency = ' . $object->multicurrency_tx . ';
                  $(".amountremaintopay").html("$ ' . round($restoPago, 2) . ' USD");
                  $(".amountremaintopay").parent().prev("tr").find("td:eq(1)").html("' . round($object->multicurrency_total_ttc, 2) . ' USD");
                  $(".amountremaintopay").parent().prev("tr").prev("tr").find("td:eq(1)").html("' . round($pago, 2) . ' USD");


                   $("[href*=\'paiement_id\']").each(function(){
                     valor = $(this).closest("td").prev("td").html();
                     tipoCambio = valor * multicurrency;
                     $(this).closest("td").prev("td").html(tipoCambio.toFixed(2)+" USD");

                  });


                   });</script>';

            }

            $factura = $object->ref;
            $factura = explode("-", $factura);

            // $serie = strpos($factura[0], "C");
            if ($factura[0] == "C" || $factura[0] == "VAL") {
                echo '<script>$(document).ready(function(){ $("[href*=\'cfdimx\']").hide(); });  </script>';
            }
            if ($factura[0] == "M" || $factura[0] == "V") {
                if (!$user->rights->vivescloud->modificarfactura->write) {
                    echo '<script>$(document).ready(function(){ $("[href*=\'reopen\']").hide(); });  </script>';
                    echo '<script>$(document).ready(function(){ $("[href*=\'canceled\']").hide(); });  </script>';
                    echo '<script>$(document).ready(function(){ $("[href*=\'formmailbeforetitle\']").hide(); });  </script>';

                }

            }

            if ($object->array_options["options_formpagcfdi"] == "PUE") {
                echo '<script>$(document).ready(function(){ $("[href*=\'pagos.php\']").hide(); });  </script>';

            }

            //validar si es fiscal o no
            $sql = "SELECT ref from " . MAIN_DB_PREFIX . "numberseries where rowid =" . $object->array_options["options_serie"];
            $result = $db->query($sql);
            $num = $db->num_rows($result);
            if ($num > 0) {
                $obj = $db->fetch_object($result);
                $tipo = explode(" ", $obj->ref);
                if ($tipo[0] == "Factura") {
                    echo '<script>$(document).ready(function(){ $("#tva_tx").hide(); });  </script>';
                }
            }

        }

    }
    /**
     * Execute action
     *
     * @param    array    $parameters     Array of parameters
     * @param   Object    $object               Object output on PDF
     * @param   string    $action         'add', 'update', 'view'
     * @return  int                     <0 if KO,
     *                                  =0 if OK but we want to process standard actions too,
     *                                  >0 if OK and we want to replace standard actions.
     */
    public function beforePDFCreation($parameters, &$object, &$action)
    {
        global $conf, $user, $langs;
        global $hookmanager;

        $outputlangs = $langs;

        $ret = 0;
        $deltemp = array();
        dol_syslog(get_class($this) . '::executeHooks action=' . $action);

        /* print_r($parameters); print_r($object); echo "action: " . $action; */
        if (in_array($parameters['currentcontext'], array('pdf_writelinedesc'))) // do something only for the context 'somecontext1' or 'somecontext2'
        {
            var_dump($object);
        }

        return $ret;
    }

    /**
     * Execute action
     *
     * @param    array    $parameters     Array of parameters
     * @param   Object    $pdfhandler     PDF builder handler
     * @param   string    $action         'add', 'update', 'view'
     * @return  int                     <0 if KO,
     *                                  =0 if OK but we want to process standard actions too,
     *                                  >0 if OK and we want to replace standard actions.
     */
    // public function afterPDFCreation($parameters, &$pdfhandler, &$action)
    // {
    //     global $conf, $user, $langs;
    //     global $hookmanager;

    //     $outputlangs = $langs;

    //     $ret = 0;
    //     $deltemp = array();
    //     dol_syslog(get_class($this) . '::executeHooks action=' . $action);

    //     /* print_r($parameters); print_r($object); echo "action: " . $action; */
    //     if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) // do something only for the context 'somecontext1' or 'somecontext2'
    //     {
    //     }

    //     return $ret;
    // }

    /**
     * Overloading the loadDataForCustomReports function : returns data to complete the customreport tool
     *
     * @param   array           $parameters     Hook metadatas (context, etc...)
     * @param   string          $action         Current action (if set). Generally create or edit or null
     * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
     * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
     */
    public function loadDataForCustomReports($parameters, &$action, $hookmanager)
    {
        global $conf, $user, $langs;

        $langs->load("vivescloud@vivescloud");

        $this->results = array();

        $head = array();
        $h = 0;

        if ($parameters['tabfamily'] == 'vivescloud') {
            $head[$h][0] = dol_buildpath('/module/index.php', 1);
            $head[$h][1] = $langs->trans("Home");
            $head[$h][2] = 'home';
            $h++;

            $this->results['title'] = $langs->trans("Vivescloud");
            $this->results['picto'] = 'vivescloud@vivescloud';
        }

        $head[$h][0] = 'customreports.php?objecttype=' . $parameters['objecttype'] . (empty($parameters['tabfamily']) ? '' : '&tabfamily=' . $parameters['tabfamily']);
        $head[$h][1] = $langs->trans("CustomReports");
        $head[$h][2] = 'customreports';

        $this->results['head'] = $head;

        return 1;
    }

    /**
     * Overloading the restrictedArea function : check permission on an object
     *
     * @param   array           $parameters     Hook metadatas (context, etc...)
     * @param   string          $action         Current action (if set). Generally create or edit or null
     * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
     * @return  int                                 <0 if KO,
     *                                          =0 if OK but we want to process standard actions too,
     *                                          >0 if OK and we want to replace standard actions.
     */
    public function restrictedArea($parameters, &$action, $hookmanager)
    {
        global $user;

        if ($parameters['features'] == 'myobject') {
            if ($user->rights->vivescloud->myobject->read) {
                $this->results['result'] = 1;
                return 1;
            } else {
                $this->results['result'] = 0;
                return 1;
            }
        }

        return 0;
    }

    public function pdf_writelinedesc($parameters, &$object, &$action)
    {

        global $db, $conf, $langs;

        $object->total_ht = round($object->total_ht, 2);
        $object->total_tva = round($object->total_tva, 2);
        $object->total_ttc = round($object->total_ttc, 2);
        $object->multicurrency_total_ht = round($object->multicurrency_total_ht, 2);
        $object->multicurrency_total_tva = round($object->multicurrency_total_tva, 2);
        $object->multicurrency_total_ttc = round($object->multicurrency_total_ttc, 2);

        $lineas = count($object->lines);
        for ($i = 0; $i < $lineas; $i++) {
            $object->lines[$i]->pu_ht = round($object->lines[$i]->pu_ht, 2);

            $object->lines[$i]->subprice = round($object->lines[$i]->subprice, 2);
            $object->lines[$i]->total_ht = round($object->lines[$i]->total_ht, 2);
            $object->lines[$i]->total_tva = round($object->lines[$i]->total_tva, 2);
            $object->lines[$i]->total_ttc = round($object->lines[$i]->total_ttc, 2);

            $object->lines[$i]->multicurrency_subprice = round($object->lines[$i]->multicurrency_subprice, 2);
            $object->lines[$i]->multicurrency_total_ht = round($object->lines[$i]->multicurrency_total_ht, 2);
            $object->lines[$i]->multicurrency_total_tva = round($object->lines[$i]->multicurrency_total_tva, 2);
            $object->lines[$i]->multicurrency_total_ttc = round($object->lines[$i]->multicurrency_total_ttc, 2);

        }

    }

    public function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
    {

        global $db;

        if (in_array('invoicecard', explode(':', $parameters['context']))) {

            $factura = $object->ref;
            $factura = explode("-", $factura);

            if ($factura[0] == "C" || $factura[0] == "VAL") {
                if ($object->statut != 0) {
                    echo '<button class="butAction" style="color:white;background-color:grey;" id="imprimir">Imprimir Ticket</button>';

                    echo '<script>
            $(document).ready(function(){
let params = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,
width=600,height=800,left=-1000,top=-1000`;

                $("#imprimir").click(function(){
                    abrirWeb("/custom/vivescloud/ticket.php",{id:' . $object->id . '});
                });
                function abrirWeb(url,data){
                        $.post(url,data, function (data) {
                        var w = window.open("about:blank","ticket",params);
                        w.document.write(data);
                        w.document.close();
                    });
                 }
            });
            </script>';

                }
            }

        }
    }

    /* Add here any other hooked methods... */
}
