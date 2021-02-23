<?php
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
require_once DOL_DOCUMENT_ROOT . '/product/stock/class/productstockentrepot.class.php';

if (!empty($conf->categorie->enabled)) {
    require_once DOL_DOCUMENT_ROOT . '/categories/class/categorie.class.php';
}
require_once DOL_DOCUMENT_ROOT . '/product/dynamic_price/class/price_parser.class.php';

if (!empty($conf->global->PRODUIT_CUSTOMER_PRICES)) {
    require_once DOL_DOCUMENT_ROOT . '/product/class/productcustomerprice.class.php';

    $prodcustprice = new Productcustomerprice($db);
}

//

require_once DOL_DOCUMENT_ROOT . '/product/stock/class/entrepot.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formcategory.class.php';

if (!empty($conf->categorie->enabled)) {
    require_once DOL_DOCUMENT_ROOT . '/categories/class/categorie.class.php';
}

//Llamando al Autoload
dol_include_once('/vivescloud/vendor/autoload.php');
dol_include_once('/vivescloud/class/ConsultasStockProductos.class.php');

$producto = GETPOST('producto'); //para listado
$consultaAlmacen = GETPOST('consultaAlmacen', 'int');
$marca = GETPOST('marca', 'aZ09'); //para listado
$productoId = GETPOST('productoid', 'int');
$idProducto = GETPOST('idproducto', 'int');
$productoLote = GETPOST('productoLote');
$productoStock = GETPOST('productostock','int');

$consulta = new ConsultaStockProductos();

if ($consultaAlmacen == "0") {

    $result = $consulta->cargarAlmacen(false);
    echo json_encode($result);
}

if ($consultaAlmacen == "1") {

    $almacenes = $consulta->cargarAlmacen(true);
    echo json_encode($result);
}

if ($producto != null) {

    $result = $consulta->cargarProducto(null, $producto, null);
    echo json_encode($result);
}

if ($marca != null) {

    $result = $consulta->cargarProducto(null, null, $marca);
    echo json_encode($result);
}

if ($productoId != null) {
    $result = $consulta->cargarStockAlmacen($productoId);
    echo json_encode($result);
}

if ($idProducto != null) {
    $result = $consulta->cargarProducto($idProducto, null, null);
    echo json_encode($result);

}

if($productoLote != null){
    $result = $consulta->cargarLoteProducto($productoLote,$almacen);
echo json_encode($result);

}
if($productoStock != null){
    $result = $consulta->cargarStockProducto($productoStock);
echo json_encode($result);

}
