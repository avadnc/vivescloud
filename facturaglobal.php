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
if (isset($user->socid) && $user->socid > 0) {
    $action = '';
    $socid = $user->socid;
}

$fechainicio = GETPOST('date');
$fechafin = GETPOST('date');
$action = GETPOST('action', 'aZ09');
$tipopago = GETPOST('tipopago', 'aZ09');

$tickets = GETPOST('rowidFactura');

/*
 * Actions
 */
$thirdparty = new Societe($db);
$thirdparty->fetch($socid);

if ($action == 'create') {

    //obtener facturas

    $tipopago = dol_getIdFromCode($db, $tipopago, 'c_paiement', 'code', 'id', 1);

    if ($tipopago != null) {
        $campos = array('rowid', 'ref', 'date_valid', 'total', 'total_ttc', 'fk_mode_reglement', 'multicurrency_code');
        $consulta = PO\QueryBuilder::factorySelect();
        $consulta->select($campos);
        $consulta->from(MAIN_DB_PREFIX . 'facture');

        //1 para guadalajara donde usan FActuración C para venta mostrador
        if ($conf->entity == 1) {
            $consulta->where('ref', 'C-%', 'LIKE');
        }
        //2 para vallarta que usan VAL- para mostrador
        if ($conf->entity == 2) {
            $consulta->where('ref', 'VAL-%', 'LIKE');
        }

        $consulta->where('entity', $conf->entity, '=');
        /*
        TODO: Hacer la validación de los tickets de forma dinámica desde la administración del módulo
         */
        $consulta->where('fk_mode_reglement', $tipopago, '=');
        $consulta->where('fk_statut', 1, '=');
        $consulta->where('date_valid BETWEEN "' . $fechainicio . '" AND "' . $fechafin . '"');
        $consulta->where('entity', $conf->entity, "=");
        $resql = $db->query($consulta->toSql());
        $num = $db->num_rows($resql);

        $tabla = [];
        $i = 0;
        if ($num > 0) {
            while ($i < $num) {
                $tabla_array = $db->fetch_object($resql);

                if ($tabla_array) {
                    array_push($tabla, $tabla_array);
                }

                $i++;
            }

        }
        $datosFactura = [
            'thirdparty' => [
                $thirdparty,
            ],
            'fechainicio' => $fechainicio,
            'fechafin' => $fechafin,
        ];
        $smarty->assign('tabla', $tabla);
        $smarty->assign('datosFactura', $datosFactura);

    } else {

        llxHeader("", "Error Factura Global");

        print load_fiche_titre("Error Factura Global", '', 'vivescloud.png@vivescloud');

        if ($user->rights->facture->creer) {
            $smarty->display('errorfacturaglobal.tpl');
        }
/* FIN */

        $NBMAX = 3;
        $max = 3;
        print '</div><div class="fichetwothirdright">';

        print '<div class="fichethirdleft"><div class="ficheaddleft"></div></div></div>';

// End of page
        llxFooter();
        $db->close();
        exit;

    }

}

if ($action == 'validate') {

    $tipopago = dol_getIdFromCode($db, $tipopago, 'c_paiement', 'code', 'id', 1);

    $factura = new Facture($db);
    $ticketFactura = new Facture($db);

    $factura->socid = $socid;
    $factura->type = 0;
    $factura->date_lim_reglement = 0;
    $factura->date = dol_now();
    $factura->date_pointoftax = dol_now();
    $factura->retained_warranty = 0;
    $factura->retained_warranty_fk_cond_reglement = 0;
    // $factura->array_options['options_serie'] = 1;
    $factura->array_options['options_formpagcfdi'] = "PUE";
    $factura->array_options['options_usocfdi'] = "G03";
    $factura->cond_reglement_id = 6;
    $factura->mode_reglement_id = $tipopago;

    $factura->note_public = "Factura correspondiente a los tickets: ";

    foreach ($tickets as $ticket) {

        $ticketFactura->fetch($ticket);
        $factura->note_public .= $ticketFactura->ref . ", ";
    }

    $factura->fetch_thirdparty();
    $idfactura = $factura->create($user);

    foreach ($tickets as $ticket) {

        $ticketFactura->fetch($ticket);
        $factura->note_public .= $ticketFactura->ref . ", ";

        foreach ($ticketFactura->lines as $line) {

            $producto = new Product($db);
            $producto->fetch($line->fk_product);
            //    echo $line->subprice;
            $result = $factura->addline(
                $producto->label,
                $line->subprice, // subprice
                $line->qty, // quantity
                $line->tva_tx, // vat rate
                $line->localtax1_tx, // localtax1_tx
                $line->localtax2_tx, // localtax2_tx
                null, // fk_product
                0, // remise_percent
                0, // date_start
                0, // date_end
                0,
                0, // info_bits
                0,
                'HT',
                0,
                0, // product_type
                1,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                $line->array_options
                //,$langs->trans('Deposit') //Deprecated
            );

        }
        $ticketFactura->set_canceled($user, 'abandon', 'se crea factura global a fecha: ' . date('Y-m-d'));

    }

    $factura->fetch($idfactura);

    $rslt = $db->commit();
    if ($rslt > 0) {
        echo $idfactura;
        exit;

    }
}

// None

/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);

$smarty->assign('socidform', $form->select_company($soc->id, 'socid', '((s.client = 1 OR s.client = 3) AND s.status=1)', 'SelectThirdParty', 0, 0, null, 0, 'minwidth300'));

llxHeader("", "Crear Factura Global");

print load_fiche_titre("Crear Factura Global", '', 'vivescloud.png@vivescloud');

if ($user->rights->facture->creer) {
    $smarty->display('facturaglobal.tpl');
}
/* FIN */

$NBMAX = 3;
$max = 3;
print '</div><div class="fichetwothirdright">';

print '<div class="fichethirdleft"><div class="ficheaddleft"></div></div></div>';

// End of page
llxFooter();
$db->close();
