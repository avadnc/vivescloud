<?php
/* Copyright (C) 2020 SuperAdmin
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
 * \file    core/triggers/interface_99_modVivescloud_VivescloudTriggers.class.php
 * \ingroup vivescloud
 * \brief   Example trigger.
 *
 * Put detailed description here.
 *
 * \remarks You can create other triggers by copying this one.
 * - File name should be either:
 *      - interface_99_modVivescloud_MyTrigger.class.php
 *      - interface_99_all_MyTrigger.class.php
 * - The file must stay in core/triggers
 * - The class name must be InterfaceMytrigger
 * - The constructor method must be named InterfaceMytrigger
 * - The name property name must be MyTrigger
 */

require_once DOL_DOCUMENT_ROOT . '/core/triggers/dolibarrtriggers.class.php';
/**
 * req envio
 */
require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT . '/product/stock/class/productlot.class.php';
require_once DOL_DOCUMENT_ROOT . '/product/stock/class/entrepot.class.php';
require_once DOL_DOCUMENT_ROOT . '/product/stock/class/mouvementstock.class.php';
require_once DOL_DOCUMENT_ROOT . '/product/class/productbatch.class.php';

dol_include_once('/vivescloud/vendor/autoload.php');
dol_include_once('/vivescloud/class/MovimientoStocksProductos.class.php');

/**
 *  Class of triggers for Vivescloud module
 */
class InterfaceVivescloudTriggers extends DolibarrTriggers
{
    /**
     * @var DoliDB Database handler
     */
    protected $db;

    /**
     * Constructor
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;

        $this->name = preg_replace('/^Interface/i', '', get_class($this));
        $this->family = "demo";
        $this->description = "Vivescloud triggers.";
        // 'development', 'experimental', 'dolibarr' or version
        $this->version = 'development';
        $this->picto = 'vivescloud@vivescloud';
    }

    /**
     * Trigger name
     *
     * @return string Name of trigger file
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Trigger description
     *
     * @return string Description of trigger file
     */
    public function getDesc()
    {
        return $this->description;
    }

    /**
     * Function called when a Dolibarrr business event is done.
     * All functions "runTrigger" are triggered if file
     * is inside directory core/triggers
     *
     * @param string         $action     Event action code
     * @param CommonObject     $object     Object
     * @param User             $user         Object user
     * @param Translate     $langs         Object langs
     * @param Conf             $conf         Object conf
     * @return int                      <0 if KO, 0 if no triggered ran, >0 if OK
     */
    public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
    {
        echo '<pre>';
        var_dump($_POST);
        exit;
        if (empty($conf->vivescloud->enabled)) {
            return 0;
        }
        // If module is not enabled, we do nothing

        // Put here code you want to execute when a Dolibarr business events occurs.
        // Data and type of action are stored into $object and $action

        switch ($action) {
            // Users
            //case 'USER_CREATE':
            //case 'USER_MODIFY':
            //case 'USER_NEW_PASSWORD':
            //case 'USER_ENABLEDISABLE':
            //case 'USER_DELETE':
            //case 'USER_SETINGROUP':
            //case 'USER_REMOVEFROMGROUP':

            // Actions
            //case 'ACTION_MODIFY':
            //case 'ACTION_CREATE':
            //case 'ACTION_DELETE':

            // Groups
            //case 'USERGROUP_CREATE':
            //case 'USERGROUP_MODIFY':
            //case 'USERGROUP_DELETE':

            // Companies
            //case 'COMPANY_CREATE':

            // case 'COMPANY_MODIFY':

            //     //añadir comercial
            //     if ($object->commercial_id != null) {
            //         $tercero = new Societe($this->db);
            //         $tercero->fetch($object->id);
            //         $this->db->begin();
            //         $tercero->add_commercial($user, $object->commercial_id);
            //         $this->db->commit();
            //     }

            //     //borrar commercial
            //     if ($object->del_commercial != null) {
            //         $tercero = new Societe($this->db);
            //         $tercero->fetch($object->id);
            //         $this->db->begin();
            //         $tercero->del_commercial($user, $object->del_commercial);
            //         $this->db->commit();
            //     }
            // break;
            //case 'COMPANY_DELETE':

            // Contacts
            //case 'CONTACT_CREATE':
            //case 'CONTACT_MODIFY':
            //case 'CONTACT_DELETE':
            //case 'CONTACT_ENABLEDISABLE':

            // Products
            // case 'PRODUCT_CREATE':

            // case 'PRODUCT_MODIFY':
            //case 'PRODUCT_DELETE':
            // case 'PRODUCT_PRICE_MODIFY':
            //     var_dump($object);
            //     exit;
            //case 'PRODUCT_SET_MULTILANGS':
            //case 'PRODUCT_DEL_MULTILANGS':

            //Stock mouvement
            case 'STOCK_MOVEMENT':

                dol_include_once('/stocktransfers/lib/stoctransfers.lib');

                /**
                 *  @var $status    0 = draft, 1 = send, 2 = recibed
                 *                  1 - decrementa el stock de origen
                 *                  2 - incrementa en el stock de destino
                 */

                $transfer = new StockTransfer($this->db);

                $action = GETPOST('action');
                $status = GETPOST('status', 'int');
                $id = GETPOST('rowid', 'int');
                $fk_warehouse_origin = GETPOST('fk_depot1', 'int');
                $fk_warehouse_dest = GETPOST('fk_depot2', 'int');

                if ($action == 'save_card') {
                    if ($status == 1) {
                        $transfer->fetch($id);
                        $movimientos = unserialize($transfer->s_products);

                        foreach ($movimientos as $movimiento) {

                            if ($movimiento['b'] != null) {

                                $consulta = $this->consultarPedimento($movimiento['b'], $fk_warehouse_origin, $movimiento['id']);
                                $datosActualiza = [
                                    'qty' => $consulta->qty - $movimiento['n'],
                                ];

                                $actualizar = $this->actualizarTabla('product_batch', $consulta->idbatch, $datosActualiza);

                                if ($actualizar != true) {
                                    setEventMessage('No se pudo actualizar', 'error');
                                }

                            }
                        }
                    }
                    if ($status == 2) {
                        $transfer->fetch($id);
                        $movimientos = unserialize($transfer->s_products);

                        foreach ($movimientos as $movimiento) {

                            if ($movimiento['b'] != null) {

                                $consulta = $this->consultarPedimento($movimiento['b'], $fk_warehouse_dest, $movimiento['id']);
                                $datosActualiza = [
                                    'qty' => $consulta->qty + $movimiento['n'],
                                ];

                                $actualizar = $this->actualizarTabla('product_batch', $consulta->idbatch, $datosActualiza);

                                if ($actualizar != true) {
                                    setEventMessage('No se pudo actualizar', 'error');
                                }
                            }
                        }
                    }
                }

                $movimiento = GETPOST('movimiento');
                $lote = GETPOST('lote');
                $cantidad = GETPOST('cantidad');

                $consulta = $this->consultarPedimento($lote, $object->warehouse_id, $object->product_id);

                if ($movimiento == 'sumar') {
                    $datosActualiza = [
                        'qty' => $cantidad + $consulta->qty,
                    ];

                    $actualizar = $this->actualizarTabla('product_batch', $consulta->idbatch, $datosActualiza);

                    if ($actualizar != true) {
                        setEventMessage('No se pudo actualizar', 'error');
                    }

                }

                break;
            //MYECMDIR
            //case 'MYECMDIR_CREATE':
            //case 'MYECMDIR_MODIFY':
            //case 'MYECMDIR_DELETE':

            // Customer orders
            case 'ORDER_CREATE':
                $cliente = new Societe($this->db);
                $cliente->fetch($object->socid);

                $limite = $cliente->outstanding_limit;
                if ($limite == 0) {
                    return;
                }

                $debe = $cliente->getOutstandingBills();

                if ($debe["opened"] > $limite) {

                    if (!$user->rights->vivescloud->actualizaprecios->write && !$user->rights->vivescloud->actualizaprecios->read) {
                        echo '<script>window . location . replace("/comm/card.php?socid=' . $object->socid . '");</script>';
                        exit;

                    }

                }

                break;
            //case 'ORDER_MODIFY':
            //case 'ORDER_VALIDATE':
            //case 'ORDER_DELETE':
            //case 'ORDER_CANCEL':
            //case 'ORDER_SENTBYMAIL':
            //case 'ORDER_CLASSIFY_BILLED':
            //case 'ORDER_SETDRAFT':
            //case 'LINEORDER_INSERT':
            //case 'LINEORDER_UPDATE':
            //case 'LINEORDER_DELETE':

            // Supplier orders
            //case 'ORDER_SUPPLIER_CREATE':
            //case 'ORDER_SUPPLIER_MODIFY':
            //case 'ORDER_SUPPLIER_VALIDATE':
            //case 'ORDER_SUPPLIER_DELETE':
            //case 'ORDER_SUPPLIER_APPROVE':
            //case 'ORDER_SUPPLIER_REFUSE':
            //case 'ORDER_SUPPLIER_CANCEL':
            //case 'ORDER_SUPPLIER_SENTBYMAIL':
            // case 'ORDER_SUPPLIER_DISPATCH':
            //case 'LINEORDER_SUPPLIER_DISPATCH':
            case 'LINEORDER_SUPPLIER_CREATE':

                $producto = new Product($this->db);
                $producto->fetch($object->fk_product);
                $linea = new CommandeFournisseurLigne($this->db);
                $linea->fetch($object->id);
// echo '<pre>';
                // var_dump($_POST);
                // exit;

                if ($object->multicurrency_code == "USD") {

                    $linea->subprice = $_POST['price_ht'] ? $_POST['price_ht'] : $producto->cost_price;
                    $linea->multicurrency_subprice = round($_POST['multicurrency_price_ht'] ? $_POST['multicurrency_price_ht'] : $producto->array_options['options_precio_moneda'], 2);
                    $linea->multicurrency_total_ht = ($linea->multicurrency_subprice * $object->qty);
                    $linea->multicurrency_total_tva = (($linea->multicurrency_total_ht * 16) / 100);
                    $linea->multicurrency_total_ttc = (($linea->multicurrency_subprice * $object->qty) * 1.16);

                    $linea->total_ht = $_POST['price_ht'] ? $_POST['price_ht'] : $producto->cost_price * $object->qty;
                    $linea->total_tva = (($_POST['price_ht'] ? $_POST['price_ht'] : $producto->cost_price * $object->qty) * 16) / 100;
                    $linea->total_ttc = ($_POST['price_ht'] ? $_POST['price_ht'] : $producto->cost_price * $object->qty) * 1.16;

                } else {

                    $linea->qty = $object->qty;

                    $linea->subprice = $_POST['price_ht'] ? $_POST['price_ht'] : $producto->cost_price;
                    $linea->total_ht = $_POST['price_ht'] ? $_POST['price_ht'] : $producto->cost_price * $object->qty;
                    $linea->total_tva = (($_POST['price_ht'] ? $_POST['price_ht'] : $producto->cost_price * $object->qty) * 16) / 100;
                    $linea->total_ttc = ($_POST['price_ht'] ? $_POST['price_ht'] : $producto->cost_price * $object->qty) * 1.16;

                }

                $linea->ref_supplier = "";
                $this->db->begin();
                $linea->update($user);
                $this->db->commit();

                break;
            case 'LINEORDER_SUPPLIER_UPDATE':

                $producto = new Product($this->db);
                $producto->fetch($object->fk_product);
                $linea = new CommandeFournisseurLigne($this->db);
                $linea->fetch($object->id);
                $linea->qty = $_POST['qty'] ? $_POST['qty'] : $object->qty;

                // echo '<pre>';
                // var_dump($_POST);
                // exit;
                if ($object->multicurrency_code == "USD") {

                    $linea->subprice = $_POST['price_ht'] ? $_POST['price_ht'] : $producto->cost_price;
                    $linea->multicurrency_subprice = round($_POST['multicurrency_subprice'] ? $_POST['multicurrency_subprice'] : $producto->array_options['options_precio_moneda'], 2);
                    $linea->multicurrency_total_ht = ($linea->multicurrency_subprice * ($_POST['qty']?$_POST['qty']:$object->qty));
                    $linea->multicurrency_total_tva = (($linea->multicurrency_total_ht * 16) / 100);
                    $linea->multicurrency_total_ttc = (($linea->multicurrency_subprice * ($_POST['qty']?$_POST['qty']:$object->qty)) * 1.16);
                    $linea->total_ht = ($_POST['price_ht'] ? $_POST['price_ht'] : $producto->cost_price) * ($_POST['qty'] ? $_POST['qty'] : $object->qty);
                    $linea->total_tva = ((($_POST['price_ht'] ? $_POST['price_ht'] : $producto->cost_price) * ($_POST['qty'] ? $_POST['qty'] : $object->qty)) * 16) / 100;
                    $linea->total_ttc = (($_POST['price_ht'] ? $_POST['price_ht'] : $producto->cost_price) * ($_POST['qty'] ? $_POST['qty'] : $object->qty)) * 1.16;

                } else {

                    $linea->subprice = $_POST['price_ht'] ? $_POST['price_ht'] : $producto->cost_price;
                    $linea->total_ht = ($_POST['price_ht'] ? $_POST['price_ht'] : $producto->cost_price) * ($_POST['qty'] ? $_POST['qty'] : $object->qty);
                    $linea->total_tva = ((($_POST['price_ht'] ? $_POST['price_ht'] : $producto->cost_price) * ($_POST['qty'] ? $_POST['qty'] : $object->qty)) * 16) / 100;
                    $linea->total_ttc = (($_POST['price_ht'] ? $_POST['price_ht'] : $producto->cost_price) * ($_POST['qty'] ? $_POST['qty'] : $object->qty)) * 1.16;

                }

                $linea->ref_supplier = "";
                $this->db->begin();
                $linea->update($user);
                $this->db->commit();

                break;
            //case 'LINEORDER_SUPPLIER_DELETE':

            // Proposals
            //case 'PROPAL_CREATE':
            //case 'PROPAL_MODIFY':
            //case 'PROPAL_VALIDATE':
            //case 'PROPAL_SENTBYMAIL':
            //case 'PROPAL_CLOSE_SIGNED':
            //case 'PROPAL_CLOSE_REFUSED':
            //case 'PROPAL_DELETE':
            // case 'LINEPROPAL_INSERT':
            //     echo '<pre>';
            //     var_dump($object);
            //     echo '<pre>';
            //     exit;
            //     break;
            //case 'LINEPROPAL_UPDATE':
            //case 'LINEPROPAL_DELETE':

            // SupplierProposal
            //case 'SUPPLIER_PROPOSAL_CREATE':
            //case 'SUPPLIER_PROPOSAL_MODIFY':
            //case 'SUPPLIER_PROPOSAL_VALIDATE':
            //case 'SUPPLIER_PROPOSAL_SENTBYMAIL':
            //case 'SUPPLIER_PROPOSAL_CLOSE_SIGNED':
            //case 'SUPPLIER_PROPOSAL_CLOSE_REFUSED':
            //case 'SUPPLIER_PROPOSAL_DELETE':
            //case 'LINESUPPLIER_PROPOSAL_INSERT':
            //case 'LINESUPPLIER_PROPOSAL_UPDATE':
            //case 'LINESUPPLIER_PROPOSAL_DELETE':

            // Contracts
            //case 'CONTRACT_CREATE':
            //case 'CONTRACT_MODIFY':
            //case 'CONTRACT_ACTIVATE':
            //case 'CONTRACT_CANCEL':
            //case 'CONTRACT_CLOSE':
            //case 'CONTRACT_DELETE':
            //case 'LINECONTRACT_INSERT':
            //case 'LINECONTRACT_UPDATE':
            //case 'LINECONTRACT_DELETE':

            // Bills
            case 'BILL_CREATE':

                if ($object->type == 0) {
                    $cliente = new Societe($this->db);
                    $cliente->fetch($object->socid);
                    if ($cliente->nom == "CLIENTE MOSTRADOR") {
                        return;
                    }
                    $factura = new Facture($this->db);
                    $factura->fetch($object->id);
                    $sql = "SELECT rowid,nbjour from llx_c_payment_term where rowid = " . $cliente->array_options["options_credito"];
                    $result = $this->db->query($sql);
                    $num = $this->db->num_rows($result);

                    if ($num > 0) {
                        $obj = $this->db->fetch_object($result);
                        $factura->date_lim_reglement = $factura->calculate_date_lim_reglement($obj->rowid);

                    }
                    if ($object->mode_reglement_id == 53) {
                        $factura->array_options["options_formpagcfdi"] = "PPD";
                    } else {
                        $factura->array_options["options_formpagcfdi"] = "PUE";
                        return;
                    }

                    $factura->update($user);

                    $sql = "UPDATE " . MAIN_DB_PREFIX . "facture set fk_cond_reglement = " . $cliente->array_options["options_credito"] . " WHERE rowid = " . $object->id;
                    $result = $this->db->query($sql);

                    $fields = ['date_lim_reglement'];
                    $select = new PO\QueryBuilder\Statements\Select();
                    $select->select($fields);
                    $select->from(MAIN_DB_PREFIX . 'facture');
                    $select->where('fk_soc', $object->socid, '=');
                    $select->where('paye', 0, '=');
                    $select->where('type', 0, '=');
                    $select->where('fk_statut', 3, '<>');
                    $select->where('fk_statut', 0, '<>');
                    $select->orderBy('rowid ASC');

                    $result = $this->db->query($select->toSql());
                    $num = $this->db->num_rows($result);

                    if ($num > 0) {
                        $i = 0;
                        while ($i < $num) {

                            $obj = $this->db->fetch_object($result);

                            $fechaFactura = new DateTime($obj->date_lim_reglement);

                            $fechaHoy = new DateTime(date('Y-m-d', dol_now()));

                            if ($fechaHoy > $fechaFactura) {
                                setEventMessages($cliente->error, "Supera Número de Días de Crédito", "errors");
                            }

                            $i++;

                        }
                    }
                    $limite = $cliente->outstanding_limit;

                    if ($limite == 0) {
                        return;
                    }

                    if (!$object->linked_objects["commande"]) {

                        $debe = $cliente->getOutstandingBills();

                        if ($debe["opened"] > $limite) {
                            if (!$user->rights->vivescloud->permitirfactura->write && !$user->rights->vivescloud->permitirfactura->read) {
                                setEventMessages($cliente->error, "Supera Límite de Crédito", "errors");

                                echo '<script>window . location . replace("/comm/card.php?socid=' . $object->socid . '");</script>';
                                exit;

                            }

                        }

                    }
                }

                break;

            case 'BILL_MODIFY':
                if (!$user->rights->vivescloud->modificarfactura->write) {
                    echo '<script>window . location . replace("/compta/facture/card.php?facid=' . $object->id . '");</script>';
                    exit;
                }

                break;
            case 'BILL_VALIDATE':

                //sacar productos de product_batch
                $almacen = GETPOST('idwarehouse', 'int');
                $sql = "UPDATE llx_facture_extrafields  SET almacen = " . $almacen . " where fk_object = " . $object->id;
                $this->db->query($sql);

                if ($object->type == 0) {

                    $factura = new Facture($this->db);
                    $factura->fetch($object->id);
                    $cliente = new Societe($this->db);
                    $cliente->fetch($factura->socid);

                    $sql = "SELECT rowid,nbjour from llx_c_payment_term where rowid = " . $cliente->array_options["options_credito"];
                    $result = $this->db->query($sql);
                    $num = $this->db->num_rows($result);
                    $diasCredito = 0;
                    if ($num > 0) {
                        $obj = $this->db->fetch_object($result);
                        $diasCredito = $obj->nbjour;
                        $factura->date_lim_reglement = $factura->calculate_date_lim_reglement($obj->rowid);

                    }
                    if ($object->mode_reglement_id == 53) {
                        $factura->array_options["options_formpagcfdi"] = "PPD";

                        $fields = ['date_lim_reglement'];
                        $select = new PO\QueryBuilder\Statements\Select();
                        $select->select($fields);
                        $select->from(MAIN_DB_PREFIX . 'facture');
                        $select->where('fk_soc', $object->socid, '=');
                        $select->where('paye', 0, '=');
                        $select->where('type', 0, '=');
                        $select->where('fk_statut', 3, '<>');
                        $select->where('fk_statut', 0, '<>');
                        $select->orderBy('rowid ASC');

                        $result = $this->db->query($select->toSql());
                        $num = $this->db->num_rows($result);

                        if ($num > 0) {
                            $i = 0;
                            while ($i < $num) {

                                $obj = $this->db->fetch_object($result);

                                $fechaFactura = new DateTime($obj->date_lim_reglement);
                                //$NewDate= Date('$day', strtotime("+7 days"));

                                $fechaLimite = date('Y-m-d', strtotime($obj->date_lim_reglement . "+5 days"));
                                
                                $fechaLimite = new DateTime($fechaLimite);

                                $fechaHoy = new DateTime(date('Y-m-d', dol_now()));

                                if ($fechaHoy > $fechaLimite) {

                                    if (!$user->rights->vivescloud->permitirfactura->write && !$user->rights->vivescloud->permitirfactura->read) {

                                    setEventMessages($cliente->error, "Supera Número de Días de Crédito", "errors");
                                    echo '<script>window . location . replace("/compta/facture/card.php?facid=' . $object->id . '");</script>';
                                    exit;

                                    }
                                }

                                $i++;

                            }
                        }
                    } else {

                        $factura->array_options["options_formpagcfdi"] = "PUE";

                    }
                    $factura->update($user);

                    $sql = "UPDATE " . MAIN_DB_PREFIX . "facture set fk_cond_reglement = " . $cliente->array_options["options_credito"] . " WHERE rowid = " . $object->id;
                    $result = $this->db->query($sql);

                    $lineas = count($object->lines);
                    $producto = new Product($this->db);

                    for ($i = 0; $i < $lineas; $i++) {

                        if ($object->lines[$i]->array_options["options_pedimento"] != null && $object->lines[$i]->array_options["options_pedimento"] != 'Seleccione Pedimento') {

                            $producto->fetch($object->lines[$i]->fk_product);
                            if ($producto->array_options["options_batch"] == 1) {

                                $qty = $object->lines[$i]->qty;
                                $consulta = $this->consultarPedimento($object->lines[$i]->array_options['options_pedimento'], $almacen, $object->lines[$i]->fk_product);
                                $datosActualiza = [
                                    'qty' => $consulta->qty - $object->lines[$i]->qty,
                                ];
                                $result = $this->actualizarTabla('product_batch', $consulta->idbatch, $datosActualiza);

                            }

                        } else {

                            $producto->fetch($object->lines[$i]->fk_product);
                            if ($producto->array_options["options_batch"] == 1) {

                                setEventMessages($object->error, 'Error en el Pedimento ' . $object->lines[$i]->ref, 'errors');

                                echo '<script>window . location . replace("/compta/facture/card.php?facid=' . $object->id . '");</script>';
                                exit;

                            }

                        }

                    }

                }

                if ($object->type == 2) {
                    $lineas = count($object->lines);
                    for ($i = 0; $i < $lineas; $i++) {

                        if ($object->lines[$i]->array_options["options_pedimento"] != null) {
                            $producto = new Product($this->db);
                            $producto->fetch($object->lines[$i]->fk_product);
                            if ($producto->array_options["options_batch"] == 1) {

                                $qty = $object->lines[$i]->qty;

                                $fields = array('pb.rowid AS idbatch', 'pb.qty');
                                $select = new PO\QueryBuilder\Statements\Select();
                                $select->select($fields);
                                $select->from('llx_product AS p');
                                $select->innerJoin(MAIN_DB_PREFIX . 'product_stock AS ps', 'p.rowid = ps.fk_product');
                                $select->innerJoin(MAIN_DB_PREFIX . 'product_lot AS pl', 'p.rowid = pl.fk_product');
                                $select->innerJoin(MAIN_DB_PREFIX . 'product_batch AS pb', 'p.rowid = pl.fk_product');
                                $select->where('p.rowid', $object->lines[$i]->fk_product, '=');
                                $select->where('ps.fk_entrepot', $almacen, '=');
                                $select->where('pb.batch', $object->lines[$i]->array_options['options_pedimento'], '=');
                                $select->groupBy('idbatch');

                                // echo $select->toSql();
                                // exit;
                                $resql = $this->db->query($select->toSql());
                                $num = $this->db->num_rows($resql);
                                $j = 0;

                                while ($j < $num) {
                                    $obj = $this->db->fetch_object($resql);
                                    if ($obj) {
                                        if ($qty <= $obj->qty) {

                                            $suma = $obj->qty + $qty;
                                            $update = PO\QueryBuilder::update(MAIN_DB_PREFIX . 'product_batch');
                                            $update->set(['qty' => $suma])->where('rowid', ':idbatch');
                                            $resultado = $this->db->query($update->toSql(['idbatch' => $obj->idbatch]));

                                            // echo $resultado;
                                            // exit;

                                            //guardarlo en un registro
                                        }
                                    }
                                    $j++;

                                }
                            }

                        }

                    }

                }

                if ($object->type == 1) {

                    $sql = "SELECT uuid from " . MAIN_DB_PREFIX . "cfdimx where fk_facture = " . $object->fk_facture_source;
                    $resql = $this->db->query($sql);
                    $obj = $this->db->fetch_object($resql);

                    $factura = new Facture($this->db);
                    $factura->fetch($object->id);
                    $factura->array_options['options_cfdidocuuid'] = $obj->uuid;
                    $this->db->begin();
                    $factura->update($user);
                    $this->db->commit();

                }
                $tipocambio = [];
                $consulta = PO\QueryBuilder::factorySelect();
                $factura = new Facture($this->db);
                $factura->fetch($object->id);
                $soc = new Societe($this->db);
                $soc->fetch($factura->socid);

                // echo '<pre>';
                // var_dump($soc);
                // exit;

                if ($object->multicurrency_code == "USD") {
                    $campos = array('label');
                    $consulta = PO\QueryBuilder::factorySelect();
                    $consulta->from(MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi');
                    $consulta->where([
                        ['code', $object->multicurrency_code, '='],
                        ['active', 1, '='],
                    ]);

                    $resql = $this->db->query($consulta->toSql());
                    $tipocambio = $this->db->fetch_object($resql);
                    // echo $consulta->toSql();

                    if (!empty($tipocambio)) {
                        $factura->array_options["options_tipodecambiocfdi"] = $tipocambio->label;

                    }

                }

                $factura->update($user, 1);

                break;

            case 'BILL_UNVALIDATE':

                $almacen = GETPOST('idwarehouse', 'int');
                if ($object->type == 0) {
                    $lineas = count($object->lines);
                    for ($i = 0; $i < $lineas; $i++) {

                        if ($object->lines[$i]->array_options["options_pedimento"] != null) {
                            $producto = new Product($this->db);
                            $producto->fetch($object->lines[$i]->fk_product);
                            if ($producto->array_options["options_batch"] == 1) {

                                $qty = $object->lines[$i]->qty;

                                $fields = array('pb.rowid AS idbatch', 'pb.qty');
                                $select = new PO\QueryBuilder\Statements\Select();
                                $select->select($fields);
                                $select->from('llx_product AS p');
                                $select->innerJoin(MAIN_DB_PREFIX . 'product_stock AS ps', 'p.rowid = ps.fk_product');
                                $select->innerJoin(MAIN_DB_PREFIX . 'product_lot AS pl', 'p.rowid = pl.fk_product');
                                $select->innerJoin(MAIN_DB_PREFIX . 'product_batch AS pb', 'p.rowid = pl.fk_product');
                                $select->where('p.rowid', $object->lines[$i]->fk_product, '=');
                                $select->where('ps.fk_entrepot', $almacen, '=');
                                $select->where('pb.batch', $object->lines[$i]->array_options['options_pedimento'], '=');
                                $select->groupBy('idbatch');

                                // echo $select->toSql();
                                // exit;
                                $resql = $this->db->query($select->toSql());
                                $num = $this->db->num_rows($resql);
                                $j = 0;

                                while ($j < $num) {
                                    $obj = $this->db->fetch_object($resql);
                                    if ($obj) {
                                        if ($qty <= $obj->qty) {

                                            $suma = $obj->qty + $qty;
                                            $update = PO\QueryBuilder::update(MAIN_DB_PREFIX . 'product_batch');
                                            $update->set(['qty' => $suma])->where('rowid', ':idbatch');
                                            $resultado = $this->db->query($update->toSql(['idbatch' => $obj->idbatch]));

                                            // echo $resultado;
                                            // exit;

                                            //guardarlo en un registro
                                        }
                                    }
                                    $j++;

                                }
                            }

                        }

                    }
                }
                break;
            //case 'BILL_SENTBYMAIL':
            case 'BILL_CANCEL':

                $product = new Product($this->db);

                $almacen = $object->array_options["options_almacen"];

                if ($object->type == 0) {

                    if ($almacen == null || $almacen == 0) {
                        setEventMessages($object->error, 'Los Productos deben retornar a almacén de forma manual', 'errors');
                        break;
                    }

                    $lineas = count($object->lines);
                    for ($i = 0; $i < $lineas; $i++) {

                        if ($object->lines[$i]->array_options["options_pedimento"] != null) {

                            $product->fetch($object->lines[$i]->fk_product);
                            if ($product->array_options["options_batch"] == 1) {
                                $consulta = $this->consultarPedimento($object->lines[$i]->array_options['options_pedimento'], $almacen, $object->lines[$i]->fk_product);
                                $now = dol_now();

                                $result = $product->correct_stock_batch(
                                    $user,
                                    $almacen,
                                    $object->lines[$i]->qty,
                                    0,
                                    'Cancelacion de Factura  ' . $object->ref . ' Producto :' . $product->ref,
                                    0,
                                    null,
                                    null,
                                    null,
                                    $now . ' - ' . $product->ref,
                                    $origin_element = 'facture',
                                    $origin_id = $object->id
                                ); // We do not change value of stock for a correction
                                $datosActualiza = [
                                    'qty' => $consulta->qty + $object->lines[$i]->qty,
                                ];
                                $result = $this->actualizarTabla('product_batch', $consulta->idbatch, $datosActualiza);

                            }

                        } else {
                            $product->fetch(null, $object->lines[$i]->ref);
                            // echo '<pre>';
                            // var_dump($product);
                            // echo '</pre>';
                            // exit;

                            $now = dol_now();

                            $result = $product->correct_stock_batch(
                                $user,
                                $almacen,
                                $object->lines[$i]->qty,
                                0,
                                'Cancelacion de Factura  ' . $object->ref . ' Producto :' . $product->ref,
                                0,
                                null,
                                null,
                                null,
                                $now . ' - ' . $product->ref,
                                $origin_element = 'facture',
                                $origin_id = $object->id
                            ); // We do not change value of stock for a correction
                        }

                    }
                }

                break;
            //case 'BILL_DELETE':
            //case 'BILL_PAYED':

            case 'LINEBILL_INSERT':

                if ($object->desc != null) {

                    $posicion = explode(",", $object->desc);

                }

                if ($object->fk_product == null) {
                    $campos = ['rowid'];
                    $select = new PO\QueryBuilder\Statements\Select();
                    $select->select($campos);
                    $select->from(MAIN_DB_PREFIX . 'product');
                    $select->where('ref', $object->label, '=');

                    $result = $this->db->query($select->toSql());
                    $num = $this->db->num_rows($result);

                    if ($num > 0) {
                        $obj = $this->db->fetch_object($result);
                        $producto = new Product($this->db);
                        $producto->fetch($obj->rowid);
                        $linea = new FactureLigne($this->db);
                        $linea->fetch($object->id);

                        if ($posicion[1]) {
                            $linea->array_options['options_numposicionzf'] = $posicion[1];
                            if ($posicion[0] == "Pos") {
                                $linea->desc = $posicion[0] . ': ' . $posicion[1] . ' ' . $posicion[2] . ': ' . $posicion[3];
                            }

                        }

                        $linea->array_options['options_umed'] = $producto->array_options['options_umed'];
                        $linea->array_options['options_claveprodserv'] = $producto->array_options['options_claveprodserv'];
                        $linea->array_options['options_sat'] = 'umed: ' . $producto->array_options['options_umed'] . ' ClaveProdServ: ' . $producto->array_options['options_claveprodserv'];
                        if ($object->array_options['options_pedimento'] != null) {
                            $linea->array_options['options_pedimento'] = $object->array_options['options_pedimento'];
                            $desc = $linea->desc . " Pedimento: " . $linea->array_options['options_pedimento'];
                            $linea->desc = $desc;
                            $linea->array_options['options_pedimento'] = $object->array_options['options_pedimento'];
                        }

                        $this->db->begin();
                        $linea->update($user);
                        $this->db->commit();

                    }

                } else {

                    $producto = new Product($this->db);
                    $producto->fetch($object->fk_product);

                    $linea = new FactureLigne($this->db);
                    $linea->fetch($object->id);

                    if ($posicion[1]) {
                        $linea->array_options['options_numposicionzf'] = $posicion[1];
                        if ($posicion[0] == "Pos") {
                            $linea->desc = $posicion[0] . ': ' . $posicion[1] . ' ' . $posicion[2] . ': ' . $posicion[3];
                        }

                    }

                    $linea->array_options['options_umed'] = $producto->array_options['options_umed'];
                    $linea->array_options['options_claveprodserv'] = $producto->array_options['options_claveprodserv'];
                    $linea->array_options['options_sat'] = 'umed: ' . $producto->array_options['options_umed'] . ' ClaveProdServ: ' . $producto->array_options['options_claveprodserv'];

                    if ($object->array_options['options_pedimento'] != null) {
                        $linea->array_options['options_pedimento'] = $object->array_options['options_pedimento'];
                        $desc = $linea->desc . " Pedimento: " . $linea->array_options['options_pedimento'];
                        $linea->desc = $desc;
                        $linea->array_options['options_pedimento'] = $object->array_options['options_pedimento'];
                    }

                    $this->db->begin();
                    $result = $linea->update($user);
                    $this->db->commit();

                }

                break;
            // case 'LINEBILL_UPDATE':
              
            //     break;
            //case 'LINEBILL_DELETE':

            //Supplier Bill
            //case 'BILL_SUPPLIER_CREATE':
            // case 'BILL_SUPPLIER_UPDATE':
            //     exit;
            //     $invoice = new FactureFournisseur($this->db);
            //     $invoice->fetch($object->id);
            //     echo '<pre>';
            //     var_dump($invoice);
            //     echo '</pre>';
            //     exit;
            //     break;

            //case 'BILL_SUPPLIER_DELETE':
            //case 'BILL_SUPPLIER_PAYED':
            //case 'BILL_SUPPLIER_UNPAYED':
            case 'BILL_SUPPLIER_VALIDATE':
                $batch = $object->array_options['options_provpedimento'];

                if ($object->type == 0) {

                    if ($batch != null) {

                        $lineas = count($object->lines);

                        for ($i = 0; $i < $lineas; $i++) {

                            $producto = new Product($this->db);
                            $producto->fetch($object->lines[$i]->fk_product);

                            if ($producto->array_options["options_batch"] == 0 || $producto->array_options["options_batch"] == null) {
                                setEventMessages($object->error, 'El producto ' . $object->lines[$i]->ref . ' está marcado "SIN PEDIMENTO" favor de corregir', 'errors');
                                header('Location: /fourn/facture/card.php?id=' . $object->id);
                            }

                            $almacen = GETPOST('idwarehouse', 'int');

                            $campos = ['batch'];
                            $select = new PO\QueryBuilder\Statements\Select();
                            $select->select($campos);
                            $select->from(MAIN_DB_PREFIX . 'product_batch');
                            $select->innerJoin(MAIN_DB_PREFIX . 'product_stock', MAIN_DB_PREFIX . 'product_batch.fk_product_stock =' . MAIN_DB_PREFIX . 'product_stock.rowid');
                            $select->where(MAIN_DB_PREFIX . 'product_batch.batch', $batch, '=');
                            $select->where(MAIN_DB_PREFIX . 'product_batch.qty', 0, '>');
                            $select->where(MAIN_DB_PREFIX . 'product_stock.fk_entrepot', $almacen, '=');

                            $result = $this->db->query($select->toSql());
                            $num = $this->db->num_rows($result);

                            if ($num == 0) {

                                $lineas = count($object->lines);

                                for ($i = 0; $i < $lineas; $i++) {

                                    $campo = ['rowid'];
                                    $consulta = new PO\QueryBuilder\Statements\Select();
                                    $consulta->select($campo);
                                    $consulta->from(MAIN_DB_PREFIX . 'product_stock');
                                    $consulta->where('fk_product', $object->lines[$i]->fk_product, '=');
                                    $consulta->where('fk_entrepot', $almacen, '=');

                                    $rslt = $this->db->query($consulta->toSql());
                                    $numfor = $this->db->num_rows($rslt);

                                    if ($numfor > 0) {
                                        $obj = $this->db->fetch_object($rslt);

                                        // Using the factory
                                        $insert = PO\QueryBuilder::insert();
                                        $insert->into(MAIN_DB_PREFIX . 'product_batch')->values(array(
                                            'fk_product_stock' => $obj->rowid,
                                            'batch' => $batch,
                                            'qty' => $object->lines[$i]->qty,
                                        ));

                                        $result = $this->db->query($insert->toSql());
                                        $this->db->commit();

                                    }

                                }
                            }
                        }
                    } else {
                        $lineas = count($object->lines);

                        for ($i = 0; $i < $lineas; $i++) {
                            $producto = new Product($this->db);
                            $producto->fetch($object->lines[$i]->fk_product);

                            if ($producto->array_options["options_batch"] == 1) {
                                header('Location: /fourn/facture/card.php?id=' . $object->id);
                            }
                        }

                    }
                }
                if ($object->type == 2) {

                    if ($batch != null) {

                        $lineas = count($object->lines);

                        for ($i = 0; $i < $lineas; $i++) {

                            $producto = new Product($this->db);
                            $producto->fetch($object->lines[$i]->fk_product);

                            if ($producto->array_options["options_batch"] == 0 || $producto->array_options["options_batch"] == null) {
                                setEventMessages($object->error, 'El producto ' . $object->lines[$i]->ref . ' está marcado "SIN PEDIMENTO" favor de corregir', 'errors');
                                header('Location: /fourn/facture/card.php?id=' . $object->id);
                            }

                            $almacen = GETPOST('idwarehouse', 'int');

                            $campos = ['batch'];
                            $select = new PO\QueryBuilder\Statements\Select();
                            $select->select($campos);
                            $select->from(MAIN_DB_PREFIX . 'product_batch');
                            $select->innerJoin(MAIN_DB_PREFIX . 'product_stock', MAIN_DB_PREFIX . 'product_batch.fk_product_stock =' . MAIN_DB_PREFIX . 'product_stock.rowid');
                            $select->where(MAIN_DB_PREFIX . 'product_batch.batch', $batch, '=');
                            $select->where(MAIN_DB_PREFIX . 'product_batch.qty', 0, '>');
                            $select->where(MAIN_DB_PREFIX . 'product_stock.fk_entrepot', $almacen, '=');

                            $result = $this->db->query($select->toSql());
                            $num = $this->db->num_rows($result);

                            if ($num > 0) {

                                $lineas = count($object->lines);

                                for ($i = 0; $i < $lineas; $i++) {

                                    $campo = ['rowid'];
                                    $consulta = new PO\QueryBuilder\Statements\Select();
                                    $consulta->select($campo);
                                    $consulta->from(MAIN_DB_PREFIX . 'product_stock');
                                    $consulta->where('fk_product', $object->lines[$i]->fk_product, '=');
                                    $consulta->where('fk_entrepot', $almacen, '=');

                                    $rslt = $this->db->query($consulta->toSql());
                                    $numfor = $this->db->num_rows($rslt);

                                    if ($numfor > 0) {
                                        $obj = $this->db->fetch_object($rslt);

                                        $consultaPedimento = $this->consultarPedimento($batch, $almacen, $object->lines[$i]->fk_product);

                                        $datosActualiza = [
                                            'qty' => $consultaPedimento->qty - $object->lines[$i]->qty,
                                        ];
                                        $result = $this->actualizarTabla('product_batch', $consultaPedimento->idbatch, $datosActualiza);

                                        $this->db->commit();

                                    }

                                }
                            }
                        }
                    } else {
                        $lineas = count($object->lines);

                        for ($i = 0; $i < $lineas; $i++) {
                            $producto = new Product($this->db);
                            $producto->fetch($object->lines[$i]->fk_product);

                            if ($producto->array_options["options_batch"] == 1) {
                                header('Location: /fourn/facture/card.php?id=' . $object->id);
                            }
                        }

                    }

                }

                break;

            case 'BILL_SUPPLIER_UNVALIDATE':

                $batch = $object->array_options['options_provpedimento'];

                if ($object->type == 0) {
                    if ($batch != null) {

                        $lineas = count($object->lines);

                        for ($i = 0; $i < $lineas; $i++) {

                            $producto = new Product($this->db);
                            $producto->fetch($object->lines[$i]->fk_product);

                            if ($producto->array_options["options_batch"] == 0 || $producto->array_options["options_batch"] == null) {
                                setEventMessages($object->error, 'El producto ' . $object->lines[$i]->ref . ' está marcado "SIN PEDIMENTO" favor de corregir', 'errors');
                                header('Location: /fourn/facture/card.php?id=' . $object->id);
                            }

                            $almacen = GETPOST('idwarehouse', 'int');

                            $campos = ['batch'];
                            $select = new PO\QueryBuilder\Statements\Select();
                            $select->select($campos);
                            $select->from(MAIN_DB_PREFIX . 'product_batch');
                            $select->innerJoin(MAIN_DB_PREFIX . 'product_stock', MAIN_DB_PREFIX . 'product_batch.fk_product_stock =' . MAIN_DB_PREFIX . 'product_stock.rowid');
                            $select->where(MAIN_DB_PREFIX . 'product_batch.batch', $batch, '=');
                            $select->where(MAIN_DB_PREFIX . 'product_batch.qty', 0, '>');
                            $select->where(MAIN_DB_PREFIX . 'product_stock.fk_entrepot', $almacen, '=');

                            $result = $this->db->query($select->toSql());
                            $num = $this->db->num_rows($result);

                            if ($num > 0) {

                                $lineas = count($object->lines);

                                for ($i = 0; $i < $lineas; $i++) {

                                    $campo = ['rowid'];
                                    $consulta = new PO\QueryBuilder\Statements\Select();
                                    $consulta->select($campo);
                                    $consulta->from(MAIN_DB_PREFIX . 'product_stock');
                                    $consulta->where('fk_product', $object->lines[$i]->fk_product, '=');
                                    $consulta->where('fk_entrepot', $almacen, '=');

                                    $rslt = $this->db->query($consulta->toSql());
                                    $numfor = $this->db->num_rows($rslt);

                                    if ($numfor > 0) {
                                        $obj = $this->db->fetch_object($rslt);

                                        $consultaPedimento = $this->consultarPedimento($batch, $almacen, $object->lines[$i]->fk_product);

                                        $datosActualiza = [
                                            'qty' => $consultaPedimento->qty - $object->lines[$i]->qty,
                                        ];
                                        $result = $this->actualizarTabla('product_batch', $consultaPedimento->idbatch, $datosActualiza);

                                        $this->db->commit();

                                    }

                                }
                            }
                        }
                    } else {
                        $lineas = count($object->lines);

                        for ($i = 0; $i < $lineas; $i++) {
                            $producto = new Product($this->db);
                            $producto->fetch($object->lines[$i]->fk_product);

                            if ($producto->array_options["options_batch"] == 1) {
                                header('Location: /fourn/facture/card.php?id=' . $object->id);
                            }
                        }

                    }

                }
                if ($object->type == 2) {
                    if ($batch != null) {

                        $lineas = count($object->lines);

                        for ($i = 0; $i < $lineas; $i++) {

                            $producto = new Product($this->db);
                            $producto->fetch($object->lines[$i]->fk_product);

                            if ($producto->array_options["options_batch"] == 0 || $producto->array_options["options_batch"] == null) {
                                setEventMessages($object->error, 'El producto ' . $object->lines[$i]->ref . ' está marcado "SIN PEDIMENTO" favor de corregir', 'errors');
                                header('Location: /fourn/facture/card.php?id=' . $object->id);
                            }

                            $almacen = GETPOST('idwarehouse', 'int');

                            $campos = ['batch'];
                            $select = new PO\QueryBuilder\Statements\Select();
                            $select->select($campos);
                            $select->from(MAIN_DB_PREFIX . 'product_batch');
                            $select->innerJoin(MAIN_DB_PREFIX . 'product_stock', MAIN_DB_PREFIX . 'product_batch.fk_product_stock =' . MAIN_DB_PREFIX . 'product_stock.rowid');
                            $select->where(MAIN_DB_PREFIX . 'product_batch.batch', $batch, '=');
                            $select->where(MAIN_DB_PREFIX . 'product_batch.qty', 0, '>');
                            $select->where(MAIN_DB_PREFIX . 'product_stock.fk_entrepot', $almacen, '=');

                            $result = $this->db->query($select->toSql());
                            $num = $this->db->num_rows($result);

                            if ($num > 0) {

                                $lineas = count($object->lines);

                                for ($i = 0; $i < $lineas; $i++) {

                                    $campo = ['rowid'];
                                    $consulta = new PO\QueryBuilder\Statements\Select();
                                    $consulta->select($campo);
                                    $consulta->from(MAIN_DB_PREFIX . 'product_stock');
                                    $consulta->where('fk_product', $object->lines[$i]->fk_product, '=');
                                    $consulta->where('fk_entrepot', $almacen, '=');

                                    $rslt = $this->db->query($consulta->toSql());
                                    $numfor = $this->db->num_rows($rslt);

                                    if ($numfor > 0) {
                                        $obj = $this->db->fetch_object($rslt);

                                        $consultaPedimento = $this->consultarPedimento($batch, $almacen, $object->lines[$i]->fk_product);

                                        $datosActualiza = [
                                            'qty' => $consultaPedimento->qty + $object->lines[$i]->qty,
                                        ];
                                        $result = $this->actualizarTabla('product_batch', $consultaPedimento->idbatch, $datosActualiza);

                                        $this->db->commit();

                                    }

                                }
                            }
                        }
                    } else {
                        $lineas = count($object->lines);

                        for ($i = 0; $i < $lineas; $i++) {
                            $producto = new Product($this->db);
                            $producto->fetch($object->lines[$i]->fk_product);

                            if ($producto->array_options["options_batch"] == 1) {
                                header('Location: /fourn/facture/card.php?id=' . $object->id);
                            }
                        }

                    }

                }

            // case 'LINEBILL_SUPPLIER_CREATE':
            // case 'LINEBILL_SUPPLIER_UPDATE':
            //case 'LINEBILL_SUPPLIER_DELETE':
            case 'BILL_SUPPLIER_MODIFY':

                if ($object->array_options['options_tipo_cambio'] != null || $object->array_options['options_tipo_cambio'] != 0) {

                    $tipo_cambio = 1 / $object->array_options['options_tipo_cambio'];

                    $invoice = new FactureFournisseur($this->db);
                    $invoice->fetch($object->id);
                    $invoice->setMulticurrencyRate(price2num($tipo_cambio));
                    if ($object->multicurrency_code == "USD") {
                        /*
                        multicurrency_total_ht
                        multicurrency_total_tva
                        multicurrency_total_ttc

                        total_ht
                        total_ttc
                        total_tva
                         */
                        $invoice->total_ht = round($object->multicurrency_total_ht * $object->array_options['options_tipo_cambio'], 2);
                        $invoice->total_ttc = round($object->multicurrency_total_ttc * $object->array_options['options_tipo_cambio'], 2);
                        $invoice->total_tva = round($object->multicurrency_total_tva * $object->array_options['options_tipo_cambio'], 2);
                    }
                    $this->db->begin();
                    $result = $invoice->update($user);
                    $this->db->commit();

                }

                break;

            
            // Payments
            //case 'PAYMENT_CUSTOMER_CREATE':
            //case 'PAYMENT_SUPPLIER_CREATE':
            //case 'PAYMENT_ADD_TO_BANK':
            //case 'PAYMENT_DELETE':

            // Online
            //case 'PAYMENT_PAYBOX_OK':
            //case 'PAYMENT_PAYPAL_OK':
            //case 'PAYMENT_STRIPE_OK':

            // Donation
            //case 'DON_CREATE':
            //case 'DON_UPDATE':
            //case 'DON_DELETE':

            // Interventions
            //case 'FICHINTER_CREATE':
            //case 'FICHINTER_MODIFY':
            //case 'FICHINTER_VALIDATE':
            //case 'FICHINTER_DELETE':
            //case 'LINEFICHINTER_CREATE':
            //case 'LINEFICHINTER_UPDATE':
            //case 'LINEFICHINTER_DELETE':

            // Members
            //case 'MEMBER_CREATE':
            //case 'MEMBER_VALIDATE':
            //case 'MEMBER_SUBSCRIPTION':
            //case 'MEMBER_MODIFY':
            //case 'MEMBER_NEW_PASSWORD':
            //case 'MEMBER_RESILIATE':
            //case 'MEMBER_DELETE':

            // Categories
            //case 'CATEGORY_CREATE':
            //case 'CATEGORY_MODIFY':
            //case 'CATEGORY_DELETE':
            //case 'CATEGORY_SET_MULTILANGS':

            // Projects
            //case 'PROJECT_CREATE':
            //case 'PROJECT_MODIFY':
            //case 'PROJECT_DELETE':

            // Project tasks
            //case 'TASK_CREATE':
            //case 'TASK_MODIFY':
            //case 'TASK_DELETE':

            // Task time spent
            //case 'TASK_TIMESPENT_CREATE':
            //case 'TASK_TIMESPENT_MODIFY':
            //case 'TASK_TIMESPENT_DELETE':
            //case 'PROJECT_ADD_CONTACT':
            //case 'PROJECT_DELETE_CONTACT':
            //case 'PROJECT_DELETE_RESOURCE':

            // Shipping
            //case 'SHIPPING_CREATE':
            // case 'SHIPPING_MODIFY':

            //     echo '<pre>';
            //     print_r($object);
            //     echo '</pre>';

            //     break;
            // case 'SHIPPING_VALIDATE':
            //     echo '<pre>';
            //     print_r($object);
            //     echo '</pre>';

            //     break;
            //case 'SHIPPING_SENTBYMAIL':
            //case 'SHIPPING_BILLED':
            //case 'SHIPPING_CLOSED':
            //case 'SHIPPING_REOPEN':
            //case 'SHIPPING_DELETE':

            // and more...
            
            default:
                dol_syslog("Trigger '" . $this->name . "' for action '$action' launched by " . __FILE__ . ". id=" . $object->id);
                break;
        }

        return 0;
    }

    private function actualizarTabla($tabla, $id, $campovalor = [])
    {
        global $db;

        $update = new PO\QueryBuilder\Statements\Update;
        $update->table(MAIN_DB_PREFIX . $tabla);
        $update->set($campovalor)->where('rowid', ':rowid');

        $result = $db->query($update->toSql(array('rowid' => $id)));

        if ($result > 0) {

            return true;

        } else {

            return false;

        }

    }
    private function consultarPedimento($lote, $almacen, $producto)
    {
        global $db;

        $campos = ['p.rowid AS idproduct', 'ps.rowid AS idstock', 'pb.rowid AS idbatch', 'pb.batch', 'pb.qty', 'ps.reel AS stock'];
        $select = new PO\QueryBuilder\Statements\Select();
        $select->select($campos);
        $select->from(MAIN_DB_PREFIX . 'product_batch AS pb');
        $select->innerJoin(MAIN_DB_PREFIX . 'product_stock as ps', 'pb.fk_product_stock = ps.rowid');
        $select->innerJoin(MAIN_DB_PREFIX . 'product as p', 'ps.fk_product = p.rowid');
        if (!isset($lote)) {

            $select->where('ps.fk_entrepot', $almacen, '=');
            $select->where('p.rowid', $producto, '=');

            $result = $db->query($select->toSql());
            $num = $db->num_rows($result);

            //devolver la suma de los pedimentos

            $totalStock = 0;
            $totalPedimento = 0;

            if ($num > 0) {
                $i = 0;

                while ($i < $num) {

                    $obj = $db->fetch_object($result);

                    $totalPedimento += $obj->qty;
                    $totalStock = $obj->stock;
                    $i++;

                }

                return [
                    'totalstock' => intval($totalStock),
                    'totalpediment' => $totalPedimento,
                ];

            } else {

                return null;

            }

        } else {
            $select->where('pb.batch', $lote, '=');
            $select->where('ps.fk_entrepot', $almacen, '=');
            $select->where('p.rowid', $producto, '=');

            $result = $db->query($select->toSql());
            $num = $db->num_rows($result);

            //existe pedimento
            if ($num > 0) {

                return $obj = $db->fetch_object($result);

            } else {

                return null;

            }
        }

    }
}
