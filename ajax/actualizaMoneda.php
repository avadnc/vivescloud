<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015      Jean-François Ferry    <jfefe@aternatik.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *    \file       vivescloud/vivescloudindex.php
 *    \ingroup    vivescloud
 *    \brief      Home page of vivescloud top menu
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
    $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"] . "/main.inc.php";
}

// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME'];
$tmp2 = realpath(__FILE__);
$i = strlen($tmp) - 1;
$j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {$i--;
    $j--;}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1)) . "/main.inc.php")) {
    $res = @include substr($tmp, 0, ($i + 1)) . "/main.inc.php";
}

if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php")) {
    $res = @include dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php";
}

// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
    $res = @include "../main.inc.php";
}

if (!$res && file_exists("../../main.inc.php")) {
    $res = @include "../../main.inc.php";
}

if (!$res && file_exists("../../../main.inc.php")) {
    $res = @include "../../../main.inc.php";
}

if (!$res) {
    die("Include of main fails");
}
require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT . '/fourn/class/fournisseur.product.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/price.lib.php';
require_once DOL_DOCUMENT_ROOT . '/product/dynamic_price/class/price_expression.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/multicurrency.lib.php';
require_once DOL_DOCUMENT_ROOT . '/multicurrency/class/multicurrency.class.php';

if (!empty($conf->categorie->enabled)) {
    require_once DOL_DOCUMENT_ROOT . '/categories/class/categorie.class.php';
}
require_once DOL_DOCUMENT_ROOT . '/product/dynamic_price/class/price_parser.class.php';

if (!empty($conf->global->PRODUIT_CUSTOMER_PRICES)) {
    require_once DOL_DOCUMENT_ROOT . '/product/class/productcustomerprice.class.php';

    $prodcustprice = new Productcustomerprice($db);
}

//Llamando al Autoload
dol_include_once('/vivescloud/vendor/autoload.php');

$moneda = GETPOST('moneda');
$valor_moneda = GETPOST('valor_moneda');
$cargarhistorico = GETPOST('cargarhistorico');

if ($cargarhistorico == 'dolar') {

    $campos = array(
        MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi.label',
        MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi.code',
        MAIN_DB_PREFIX . 'vivescloud_tipocambio_registro.fecha_modificacion');
    $consulta = PO\QueryBuilder::factorySelect();
    $consulta->select($campos);
    $consulta->from(MAIN_DB_PREFIX . 'vivescloud_tipocambio_registro');
    $consulta->innerJoin(MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi', 'fk_moneda = ' . MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi.rowid');
    $consulta->where([
        [MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi.code', 'USD', '='],
        [MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi.active', 1, '='],
        // [MAIN_DB_PREFIX.'product.tosell', 1, '='],
    ]);
    $consulta->orderBy(MAIN_DB_PREFIX . 'vivescloud_tipocambio_registro.rowid DESC');
    $consulta->limit(1);

    $resql = $db->query($consulta->toSql());
    $num = $db->num_rows($resql);

    if ($num) {
        $respuestamonda = $db->fetch_object($resql);

        echo json_encode($respuestamonda);
    }

}

if ($cargarhistorico == 'euro') {

    $campos = array(
        MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi.label',
        MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi.code',
        MAIN_DB_PREFIX . 'vivescloud_tipocambio_registro.fecha_modificacion');
    $consulta = PO\QueryBuilder::factorySelect();
    $consulta->select($campos);
    $consulta->from(MAIN_DB_PREFIX . 'vivescloud_tipocambio_registro');
    $consulta->innerJoin(MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi', 'fk_moneda = ' . MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi.rowid');
    $consulta->where([
        [MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi.code', 'EUR', '='],
        [MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi.active', 1, '='],
        // [MAIN_DB_PREFIX.'product.tosell', 1, '='],
    ]);
    $consulta->orderBy(MAIN_DB_PREFIX . 'vivescloud_tipocambio_registro.rowid DESC');
    $consulta->limit(1);

    $resql = $db->query($consulta->toSql());
    $num = $db->num_rows($resql);

    if ($num) {
        $respuestamonda = $db->fetch_object($resql);

        echo json_encode($respuestamonda);
    }

}

if ($moneda == 'dolar') {

    /* Actualizar Tipo Cambio*/
    $campos = [
        'rowid',
    ];
    $consulta = PO\QueryBuilder::factorySelect();
    $consulta->select($campos);
    $consulta->from(MAIN_DB_PREFIX . 'multicurrency');
    $consulta->where(MAIN_DB_PREFIX . 'multicurrency.code', 'USD', '=');
    $consulta->where(MAIN_DB_PREFIX . 'multicurrency.entity', 1, '=');

    $resql = $db->query($consulta->toSql());

    $resultado = $db->fetch_array($resql);

    $tipocambioDoli = 1 / $valor_moneda;

    $fk_multicurrency = $resultado["rowid"];
    $rate = price2num($tipocambioDoli);
    $currency = new MultiCurrency($db);

    if (empty($rate)) {
        setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv("Rate")), null, 'errors');
        $error++;
    }
    if (!$error) {
        if ($currency->fetch($fk_multicurrency) > 0) {
            $result = $currency->updateRate($rate);
            if ($result < 0) {
                setEventMessages(null, $currency->errors, 'errors');
            }
        }
    }

    $campos = [
        'rowid',
    ];
    $consulta = PO\QueryBuilder::factorySelect();
    $consulta->select($campos);
    $consulta->from(MAIN_DB_PREFIX . 'multicurrency');
    $consulta->where(MAIN_DB_PREFIX . 'multicurrency.code', 'USD', '=');
    $consulta->where(MAIN_DB_PREFIX . 'multicurrency.entity', 2, '=');
    $resql = $db->query($consulta->toSql());

    $resultado = $db->fetch_array($resql);

    $tipocambioDoli = 1 / $valor_moneda;
    $rate = price2num($tipocambioDoli);
    $fk_multicurrency = $resultado["rowid"];
    date_default_timezone_set('America/Mexico_City');

    $insert = PO\QueryBuilder::insert();
    $insert->into(MAIN_DB_PREFIX . 'multicurrency_rate')->values(array(
        'date_sync' => date("Y-m-d H:i:s"),
        'rate' => $rate,
        'fk_multicurrency' => $resultado["rowid"],
        'entity' => 2,
    ));

    $result = $db->query($insert->toSql());

    /* Fin Tipo Cambio */

    $campos = [
        'rowid',
    ];
    $consulta = PO\QueryBuilder::factorySelect();
    $consulta->select($campos);
    $consulta->from(MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi');
    $consulta->where([
        [MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi.code', 'USD', '='],
        [MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi.active', 1, '='],

    ]);

    $resql = $db->query($consulta->toSql());
    $num = $db->num_rows($resql);

    if ($num) {
        $id = $db->fetch_object($resql);
    }

    $update = PO\QueryBuilder::update(MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi');
    $update->set(array(
        'label' => $valor_moneda,
    ))->where('code', 'USD');

    $insert = PO\QueryBuilder::insert();
    $insert->into(MAIN_DB_PREFIX . 'vivescloud_tipocambio_registro')->values(array(
        'fk_moneda' => $id->rowid,
        'fecha_modificacion' => date('Y-m-d H:i:s'),
        'valor' => $valor_moneda,
        'entity' => $conf->entity,
        'fk_user_creat' => $user->id,
        'fk_user_modif' => $user->id,
    ));

    $db->begin();
    $db->query($insert->toSql());
    $db->commit();

    $update = PO\QueryBuilder::update(MAIN_DB_PREFIX . 'const');
    $update->set(array(
        'value' => "$ " . $valor_moneda . " a fecha " . date('Y-m-d'),
    ))->where('name', 'TIPO_CAMBIO_USD');

    $db->begin();
    $db->query($update->toSql());
    $db->commit();

    $campos = ['fk_object'];
    $consulta = null;
    $consulta = PO\QueryBuilder::factorySelect();
    $consulta->select($campos);
    $consulta->from(MAIN_DB_PREFIX . 'product_extrafields');
    $consulta->where('moneda', 'USD', '=');

    $resql = $db->query($consulta->toSql() . 'AND precio_moneda IS NOT NULL');

    $num = $db->num_rows($resql);

    if ($num > 0) {
        $j = 0;
        while ($j < $num) {

            $obj = $db->fetch_object($resql);
            if ($obj) {

                $producto = new Product($db);

                $producto->fetch($obj->fk_object);

                if ($producto->array_options["options_precio_moneda"] != null && $producto->status == 1) {
                    $precioCompra = $producto->array_options["options_precio_moneda"];
                    $refe = $producto->ref;

                    $margen1 = $producto->array_options["options_margen1"];
                    $margen2 = $producto->array_options["options_margen2"];
                    $margen3 = $producto->array_options["options_margen3"];

                    /*
                    Aqui empieza el juego
                     */

                    $precioCosto = round(price2num($precioCompra) * price2num($valor_moneda), 2);
                    $producto->cost_price = $precioCosto;
                    $producto->update($producto->id, $user);
                    if ($resultado > 0) {
                        $db->commit();
                    } else {
                        echo $producto->ref;
                        $db->rollback();
                    }

                    $producto = new Product($db);

                    $producto->fetch($obj->fk_object);

                    if ($margen1 > 8) {

                        $precio1 = price2num($precioCosto * (1 + ($margen1 / 100)));

                    } else {

                        $precio1 = price2num($precioCosto * (1 + (8 / 100)));

                    }
                    if ($margen2 > 8) {

                        $precio2 = price2num($precioCosto * (1 + ($margen2 / 100)));

                    } else {

                        $precio2 = price2num($precioCosto * (1 + (8 / 100)));

                    }

                    if ($margen3 > 8) {

                        $precio3 = price2num($precioCosto * (1 + ($margen3 / 100)));

                    } else {

                        $precio3 = price2num($precioCosto * (1 + (8 / 100)));

                    }

                    $preciominimo = price2num($precioCosto * (1 + (8 / 100)));

                    //precio minimo basado en el 8 de felipe davila

                    for ($i = 1; $i <= $conf->global->PRODUIT_MULTIPRICES_LIMIT; $i++) {

                        switch ($i) {

                            case 1:

                                $pricestoupdate[1] = array(
                                    'price' => $precio1,
                                    'price_min' => $preciominimo,
                                    'price_base_type' => $producto->price_base_type,
                                    'vat_tx' => $producto->tva_tx,
                                    'npr' => $producto->tva_npr,
                                    'localtaxes_array' => array('0' => 0, '1' => 0, '2' => 0, '3' => 0),
                                );
                                break;
                            case 2:

                                $pricestoupdate[2] = array(
                                    'price' => $precio2,
                                    'price_min' => $preciominimo,
                                    'price_base_type' => $producto->price_base_type,
                                    'vat_tx' => $producto->tva_tx,
                                    'npr' => $producto->tva_npr,
                                    'localtaxes_array' => array('0' => 0, '1' => 0, '2' => 0, '3' => 0),
                                );
                                break;

                            case 3:

                                $pricestoupdate[3] = array(
                                    'price' => $precio3,
                                    'price_min' => $preciominimo,
                                    'price_base_type' => $producto->price_base_type,
                                    'vat_tx' => $producto->tva_tx,
                                    'npr' => $producto->tva_npr,
                                    'localtaxes_array' => array('0' => 0, '1' => 0, '2' => 0, '3' => 0),
                                );
                                break;

                        }
                    }
                }

                $db->begin();

                foreach ($pricestoupdate as $key => $val) {

                    $newprice = $val['price'];

                    // if ($val['price'] < $val['price_min'] && !empty($producto->fk_price_expression)) {
                    //     $newprice = $val['price_min']; //Set price same as min, the user will not see the
                    // }

                    $newprice = price2num($newprice, 'MU');
                    $newprice_min = price2num($val['price_min'], 'MU');

                    $res = $producto->updatePrice($newprice, $val['price_base_type'], $user, $val['vat_tx'], $newprice_min, $key, $val['npr'], 0, 0, $val['localtaxes_array']);

                    if ($res < 0) {
                        echo "error";
                        echo $producto->error;
                        exit;
                        $error++;
                        setEventMessages($producto->error, $producto->errors, 'errors');
                        break;
                    }
                }

                $resultado = $producto->update($producto->id, $user);
                if ($resultado > 0) {
                    $db->commit();
                } else {
                    echo $producto->ref;
                    $db->rollback();
                }
            }
            $j++;
        }
        echo "ok";
        exit;
    }

}

if ($moneda == 'euro') {

    /* Actualizar Tipo Cambio*/
    $campos = [
        'rowid',
    ];
    $consulta = PO\QueryBuilder::factorySelect();
    $consulta->select($campos);
    $consulta->from(MAIN_DB_PREFIX . 'multicurrency');
    $consulta->where([
        [MAIN_DB_PREFIX . 'multicurrency.code', 'EUR', '='],

    ]);

    $resql = $db->query($consulta->toSql());

    $resultado = $db->fetch_array($resql);

    $tipocambioDoli = 1 / $valor_moneda;

    $fk_multicurrency = $resultado["rowid"];
    $rate = price2num($tipocambioDoli);
    $currency = new MultiCurrency($db);

    if (empty($rate)) {
        setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv("Rate")), null, 'errors');
        $error++;
    }
    if (!$error) {
        if ($currency->fetch($fk_multicurrency) > 0) {
            $result = $currency->updateRate($rate);
            if ($result < 0) {
                setEventMessages(null, $currency->errors, 'errors');
            }
        }
    }

    /* Fin Tipo Cambio */

    $campos = [
        'rowid',
    ];
    $consulta = PO\QueryBuilder::factorySelect();
    $consulta->select($campos);
    $consulta->from(MAIN_DB_PREFIX . 'multicurrency');
    $consulta->where(MAIN_DB_PREFIX . 'multicurrency.code', 'EUR', '=');
    $consulta->where(MAIN_DB_PREFIX . 'multicurrency.entity', 1, '=');

    $resql = $db->query($consulta->toSql());

    $resultado = $db->fetch_array($resql);

    $tipocambioDoli = 1 / $valor_moneda;

    $fk_multicurrency = $resultado["rowid"];
    $rate = price2num($tipocambioDoli);
    $currency = new MultiCurrency($db);

    if (empty($rate)) {
        setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv("Rate")), null, 'errors');
        $error++;
    }
    if (!$error) {
        if ($currency->fetch($fk_multicurrency) > 0) {
            $result = $currency->updateRate($rate);
            if ($result < 0) {
                setEventMessages(null, $currency->errors, 'errors');
            }
        }
    }

    $campos = [
        'rowid',
    ];
    $consulta = PO\QueryBuilder::factorySelect();
    $consulta->select($campos);
    $consulta->from(MAIN_DB_PREFIX . 'multicurrency');
    $consulta->where(MAIN_DB_PREFIX . 'multicurrency.code', 'EUR', '=');
    $consulta->where(MAIN_DB_PREFIX . 'multicurrency.entity', 2, '=');
    $resql = $db->query($consulta->toSql());

    $resultado = $db->fetch_array($resql);

    $tipocambioDoli = 1 / $valor_moneda;
    $rate = price2num($tipocambioDoli);
    $fk_multicurrency = $resultado["rowid"];
    date_default_timezone_set('America/Mexico_City');

    $insert = PO\QueryBuilder::insert();
    $insert->into(MAIN_DB_PREFIX . 'multicurrency_rate')->values(array(
        'date_sync' => date("Y-m-d H:i:s"),
        'rate' => $rate,
        'fk_multicurrency' => $resultado["rowid"],
        'entity' => 2,
    ));

    $result = $db->query($insert->toSql());

    /* Fin Tipo Cambio */

    $campos = [
        'rowid',
    ];
    $consulta = PO\QueryBuilder::factorySelect();
    $consulta->select($campos);
    $consulta->from(MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi');
    $consulta->where([
        [MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi.code', 'EUR', '='],
        [MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi.active', 1, '='],

    ]);

    $resql = $db->query($consulta->toSql());
    $num = $db->num_rows($resql);

    if ($num) {
        $id = $db->fetch_object($resql);
    }

    $update = PO\QueryBuilder::update(MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi');
    $update->set(array(
        'label' => $valor_moneda,
    ))->where('code', 'EUR');

    $insert = PO\QueryBuilder::insert();
    $insert->into(MAIN_DB_PREFIX . 'vivescloud_tipocambio_registro')->values(array(
        'fk_moneda' => $id->rowid,
        'fecha_modificacion' => date('Y-m-d H:i:s'),
        'valor' => $valor_moneda,
        'entity' => $conf->entity,
        'fk_user_creat' => $user->id,
        'fk_user_modif' => $user->id,
    ));

    $db->begin();
    $db->query($update->toSql());
    $db->commit();

    $campos = ['fk_object'];
    $consulta = null;
    $consulta = PO\QueryBuilder::factorySelect();
    $consulta->select($campos);
    $consulta->from(MAIN_DB_PREFIX . 'product_extrafields');
    $consulta->where('moneda', 'EUR', '=');

    $update = PO\QueryBuilder::update(MAIN_DB_PREFIX . 'const');
    $update->set(array(
        'value' => "€ " . $valor_moneda . " a fecha " . date('Y-m-d'),
    ))->where('name', 'TIPO_CAMBIO_EURO');

    $db->begin();
    $db->query($update->toSql());
    $db->commit();

    $resql = $db->query($consulta->toSql() . 'precio_moneda IS NOT NULL');
    $num = $db->num_rows($resql);
    $insertar = [];
    $updatePrecioCosto = [];

    if ($num > 0) {
        $j = 0;
        while ($j < $num) {

            $obj = $db->fetch_object($resql);
            if ($obj) {

                $producto = new Product($db);

                $producto->fetch($obj->fk_object);

                if ($producto->array_options["options_precio_moneda"] != null && $producto->status == 1) {
                    $precioCompra = $producto->array_options["options_precio_moneda"];
                    $refe = $producto->ref;

                    $margen1 = $producto->array_options["options_margen1"];
                    $margen2 = $producto->array_options["options_margen2"];
                    $margen3 = $producto->array_options["options_margen3"];

                    /*
                    Aqui empieza el juego
                     */

                    $precioCosto = round(price2num($precioCompra) * price2num($valor_moneda), 2);
                    $producto->cost_price = $precioCosto;
                    $producto->update($producto->id, $user);
                    if ($resultado > 0) {
                        $db->commit();
                    } else {
                        echo $producto->ref;
                        $db->rollback();
                    }

                    $producto = new Product($db);

                    $producto->fetch($obj->fk_object);

                    if ($margen1 > 8) {

                        $precio1 = price2num($precioCosto * (1 + ($margen1 / 100)));

                    } else {

                        $precio1 = price2num($precioCosto * (1 + (8 / 100)));

                    }
                    if ($margen2 > 8) {

                        $precio2 = price2num($precioCosto * (1 + ($margen2 / 100)));

                    } else {

                        $precio2 = price2num($precioCosto * (1 + (8 / 100)));

                    }

                    if ($margen3 > 8) {

                        $precio3 = price2num($precioCosto * (1 + ($margen3 / 100)));

                    } else {

                        $precio3 = price2num($precioCosto * (1 + (8 / 100)));

                    }

                    $preciominimo = price2num($precioCosto * (1 + (8 / 100)));

                    //precio minimo basado en el 8 de felipe davila

                    for ($i = 1; $i <= $conf->global->PRODUIT_MULTIPRICES_LIMIT; $i++) {

                        switch ($i) {

                            case 1:

                                $pricestoupdate[1] = array(
                                    'price' => $precio1,
                                    'price_min' => $preciominimo,
                                    'price_base_type' => $producto->price_base_type,
                                    'vat_tx' => $producto->tva_tx,
                                    'npr' => $producto->tva_npr,
                                    'localtaxes_array' => array('0' => 0, '1' => 0, '2' => 0, '3' => 0),
                                );
                                break;
                            case 2:

                                $pricestoupdate[2] = array(
                                    'price' => $precio2,
                                    'price_min' => $preciominimo,
                                    'price_base_type' => $producto->price_base_type,
                                    'vat_tx' => $producto->tva_tx,
                                    'npr' => $producto->tva_npr,
                                    'localtaxes_array' => array('0' => 0, '1' => 0, '2' => 0, '3' => 0),
                                );
                                break;

                            case 3:

                                $pricestoupdate[3] = array(
                                    'price' => $precio3,
                                    'price_min' => $preciominimo,
                                    'price_base_type' => $producto->price_base_type,
                                    'vat_tx' => $producto->tva_tx,
                                    'npr' => $producto->tva_npr,
                                    'localtaxes_array' => array('0' => 0, '1' => 0, '2' => 0, '3' => 0),
                                );
                                break;

                        }
                    }
                }

                $db->begin();

                foreach ($pricestoupdate as $key => $val) {

                    $newprice = $val['price'];

                    // if ($val['price'] < $val['price_min'] && !empty($producto->fk_price_expression)) {
                    //     $newprice = $val['price_min']; //Set price same as min, the user will not see the
                    // }

                    $newprice = price2num($newprice, 'MU');
                    $newprice_min = price2num($val['price_min'], 'MU');

                    $res = $producto->updatePrice($newprice, $val['price_base_type'], $user, $val['vat_tx'], $newprice_min, $key, $val['npr'], 0, 0, $val['localtaxes_array']);

                    if ($res < 0) {
                        echo "error";
                        echo $producto->error;
                        exit;
                        $error++;
                        setEventMessages($producto->error, $producto->errors, 'errors');
                        break;
                    }
                }

                $resultado = $producto->update($producto->id, $user);
                if ($resultado > 0) {
                    $db->commit();
                } else {
                    echo $producto->ref;
                    $db->rollback();
                }
            }
            $j++;
        }
        echo "ok";
        exit;
    }

}
