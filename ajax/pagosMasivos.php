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

require_once DOL_DOCUMENT_ROOT . '/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT . '/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT . '/compta/facture/class/facture-rec.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/invoice.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';

//Llamando al Autoload
dol_include_once('/vivescloud/lib/Smartie.php');
dol_include_once('/vivescloud/vendor/autoload.php');

// Load translation files required by the page
$langs->loadLangs(array("vivescloud@vivescloud"));

$action = GETPOST('action', 'alpha');
//Manejo Smarty
$smarty = new Smartie();
$smarty->assign('dol_url_root', DOL_URL_ROOT);

// Security check
//if (! $user->rights->vivescloud->myobject->read) accessforbidden();
$socid = GETPOST('socid', 'int');
$pago = GETPOST('tipopago', 'alpha');
$sucursal = GETPOST('sucursal', 'int');
$fechaInicio = GETPOST('fechainicio');
$fechaFin = GETPOST('fechafin');

if (isset($user->socid) && $user->socid > 0) {
    $action = '';
    $socid = $user->socid;
}

$tipoPago = dol_getIdFromCode($db, $pago, 'c_paiement', 'code', 'id', 1);

function getFacturasPendientes($tipoPago, $fechaInicio, $fechaFin, $entity)
{

    global $db;
    $campos = ['rowid', 'ref', 'date_valid', 'fk_soc', 'fk_mode_reglement', 'multicurrency_code','multicurrency_total_ttc'];
    $select = new PO\QueryBuilder\Statements\Select();
    $select->select($campos);
    $select->from(MAIN_DB_PREFIX . 'facture');
    $select->where('paye', 0, '=');
    $select->where('type', 0, '=');
    $select->where('fk_mode_reglement', $tipoPago, '=');
    $select->where('date_valid BETWEEN "' . $fechaInicio . '" AND "' . $fechaFin . '"');

    $select->where('entity', $entity, '=');
    $result = $db->query($select->toSql());
    $num = $db->num_rows($result);

    if ($num > 0) {

        $i = 0;
        $facturas = [];
        while ($i < $num) {

            $obj = $db->fetch_object($result);

            $cliente = new Societe($db);
            $cliente->fetch($obj->fk_soc);

            $datosFacturas = [
                'idfactura' => $obj->rowid,
                'ref' => $obj->ref,
                'fechaEmision' => $obj->date_valid,
                'cliente' => $cliente->name,
                'rfc' => $cliente->idprof1,
                'tipoPago' => $obj->fk_mode_reglement,
                'totalFactura' => $obj->multicurrency_total_ttc,
            ];

            array_push($facturas, $datosFacturas);

            $i++;

        }

        return $facturas;

    } else {
        return null;
    }

}

// echo '<pre>';
// var_dump(getFacturasPendientes($pagoEfectivo,'2021-01-07','2021-01-07',1));
// exit;

/*
 * Actions
 */

if ($action == 'obtenerFacturas') {

    echo json_encode(getFacturasPendientes($pago, $fechaInicio, $fechaFin, $sucursal));
    exit;
}