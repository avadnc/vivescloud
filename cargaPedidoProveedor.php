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
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/modules/supplier_order/modules_commandefournisseur.php';
require_once DOL_DOCUMENT_ROOT . '/fourn/class/fournisseur.commande.class.php';
require_once DOL_DOCUMENT_ROOT . '/fourn/class/fournisseur.product.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/fourn.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/doleditor.class.php';
if (!empty($conf->supplier_proposal->enabled)) {
    require_once DOL_DOCUMENT_ROOT . '/supplier_proposal/class/supplier_proposal.class.php';
}

if (!empty($conf->product->enabled)) {
    require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';
}

if (!empty($conf->projet->enabled)) {
    require_once DOL_DOCUMENT_ROOT . '/projet/class/project.class.php';
    require_once DOL_DOCUMENT_ROOT . '/core/class/html.formprojet.class.php';
}

if (!empty($conf->variants->enabled)) {
    require_once DOL_DOCUMENT_ROOT . '/variants/class/ProductCombination.class.php';
}

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

$pedido = GETPOST('pedido', 'int');

/*
 * Actions
 */


 $object = new CommandeFournisseur($db);
 $object->fetch($pedido);
 $smarty->assign('datosPedido',$object);

// None

/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);

$smarty->assign('socidform', $form->select_company($soc->id, 'socid', '((s.client = 1 OR s.client = 3) AND s.status=1)', 'SelectThirdParty', 0, 0, null, 0, 'minwidth300'));

llxHeader("", "Pedido Proveedor");

print load_fiche_titre("Carga Masiva de Partidas", '', 'vivescloud.png@vivescloud');

if (!$pedido) {
   echo '<script>window.location.replace("/fourn/commande/list.php");</script>';
} else {
    $smarty->display('cargaPedidoProveedor.tpl');
}

/* FIN */

$NBMAX = 3;
$max = 3;
print '</div><div class="fichetwothirdright">';

print '<div class="fichethirdleft"><div class="ficheaddleft"></div></div></div>';

// End of page
llxFooter();
$db->close();
