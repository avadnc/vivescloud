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
dol_include_once('/vivescloud/vendor/autoload.php');

// Load translation files required by the page
$langs->loadLangs(array("vivescloud@vivescloud"));

$action = GETPOST('action', 'alpha');

// Security check
//if (! $user->rights->vivescloud->myobject->read) accessforbidden();
$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0) {
    $action = '';
    $socid = $user->socid;
}

//Funcion escanear directorio
function dirToArray($dir)
{
    $result = array();

    $cdir = scandir($dir);
    foreach ($cdir as $key => $value) {
        if (!in_array($value, array(".", ".."))) {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
            } else {
                $result[] = $value;
            }
        }
    }

    return $result;
}

function dibujaTabla($datos)
{
    $html = '<tr class="liste-titre">';
    foreach ($datos as $key => $value) {
        $html .= '<td colspan="2">' . $key . '</td>';
        if (is_array($value)) {
            $html .= dibujaTabla($value);
        } else {
            $html .= '<tr><td>' . $value . '</td></tr>';
        }
    }
    $html .= '</tr>';
    return $html;
}

$max = 5;
$now = dol_now();

function insertaXML($directorio)
{
    global $db, $user, $conf;
    $resultado = dirToArray($directorio);
    $r = 0;

    foreach ($resultado as $year => $content) {
        $yearXML = $year;

        foreach ($content as $month => $contentXML) {

            foreach ($contentXML as $fileXML) {

                $ruta = $directorio . '/' . $year . '/' . $month . '/';

                $ext = end(explode('.', $fileXML));

                if ($ext == 'xml' || $ext == 'XML') {

                    $xml = file_get_contents($ruta . $fileXML);
                    // clean cfdi
                    $xml = \CfdiUtils\Cleaner\Cleaner::staticClean($xml);

                    // create the main node structure
                    $comprobante = \CfdiUtils\Nodes\XmlNodeUtils::nodeFromXmlString($xml);

                    // create the CfdiData object, it contains all the required information
                    $cfdiData = (new \PhpCfdi\CfdiToPdf\CfdiDataBuilder())
                        ->build($comprobante);

                    $tfd = $cfdiData->timbreFiscalDigital();
                    // create the converter
                    $converter = new \PhpCfdi\CfdiToPdf\Converter(
                        new \PhpCfdi\CfdiToPdf\Builders\Html2PdfBuilder()
                    );

                    $emisor = $cfdiData->emisor();
                    $receptor = $cfdiData->receptor();
                    $conceptos = $comprobante->searchNodes('cfdi:Conceptos', 'cfdi:Concepto');

                    $query = "SELECT uuid from " . MAIN_DB_PREFIX . "cfdimx where uuid ='" . $tfd['UUID'] . "';";
                    $resql = $db->query($query);

                    $num = $db->num_rows($resql);

                    if ($num == 0) {

                        if ($emisor["Rfc"] == $conf->global->MAIN_INFO_SIREN) {

                            $societe = new Societe($db);

                            $societe->fetch(null, null, null, null, $receptor["Rfc"]);

                            if ($societe->idprof1 != null) {

                                //Comprobante Tipo Ingreso | Factura
                                if ($comprobante['TipoDeComprobante'] == "I") {

                                    /***************************/
                                    /* Creación de la factura */
                                    /**************************/

                                    // Cambiamos la fecha a Milisengundos para insertarlo en la factura
                                    $fecha = date("m/d/Y", strtotime($comprobante['Fecha']));
                                    $fechaExplode = explode("/", $fecha);
                                    $dateinvoice = dol_mktime(12, 0, 0, $fechaExplode[0], $fechaExplode[1], $fechaExplode[2]);

                                    // Si facture standard
                                    $factura = new Facture($db);
                                    $factura->socid = $societe->id;
                                    $factura->type = 0;
                                    $factura->ref = $comprobante['Serie'] . "-" . $comprobante['Folio'];
                                    $factura->date = $dateinvoice;

                                    // $factura->remise_absolue = $_POST['remise_absolue'];
                                    // $factura->remise_percent = $_POST['remise_percent'];
                                    // $factura->multicurrency_code = GETPOST('multicurrency_code', 'alpha');
                                    // $factura->multicurrency_tx = GETPOST('originmulticurrency_tx', 'int');

                                    $factura->retained_warranty = 0;
                                    $factura->retained_warranty_fk_cond_reglement = 0;

                                    $factura->fetch_thirdparty();

                                    /* Lineas */

                                    $id = $factura->create($user);

                                    foreach ($conceptos as $concepto) {

                                        $conceptoTraslados = $concepto->searchNodes('cfdi:Impuestos', 'cfdi:Traslados', 'cfdi:Traslado');
                                        foreach ($conceptoTraslados as $impuesto) {
                                            $vat_rate = $impuesto['TasaOCuota'] * 100;
                                        }
                                        //Hacer Foreach para cargar productos
                                        $result = $factura->addline(
                                            $concepto['Descripcion'],
                                            $concepto['ValorUnitario'], // subprice
                                            $concepto['Cantidad'], // quantity
                                            $vat_rate, // vat rate
                                            0, // localtax1_tx
                                            0, // localtax2_tx
                                            0, // fk_product
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
                                            0
                                            //,$langs->trans('Deposit') //Deprecated
                                        );
                                    }
                                    // echo "<h2>" . $result . "</h2>";

                                    $factura->fetch($id);
                                    $factura->ref = $comprobante['Serie'] . "-" . $comprobante['Folio'];
                                    $factura->validate($user);

                                    mkdir($conf->facture->dir_output . "/" . $factura->ref, 0700);
                                    $file_xml = fopen($conf->facture->dir_output . "/" . $factura->ref . "/" . $tfd['UUID'] . ".xml", "w");
                                    fwrite($file_xml, utf8_encode($xml));
                                    fclose($file_xml);

                                    $fecha_emision = explode("T", $comprobante['Fecha']);
                                    $query = PO\QueryBuilder::insert();

                                    $query->into(MAIN_DB_PREFIX . 'cfdimx')->values([
                                        'tipo_timbrado' => 1,
                                        'factura_serie' => $comprobante['Serie'],
                                        'factura_folio' => $comprobante['Folio'],
                                        'factura_seriefolio' => $comprobante['Serie'] . "-" . $comprobante['Folio'],
                                        'xml' => $xml,
                                        'cadena' => $cfdiData->tfdSourceString(),
                                        'version' => $comprobante['Version'],
                                        'selloCFD' => $tfd['SelloCFD'],
                                        'fechaTimbrado' => $tfd['FechaTimbrado'],
                                        'uuid' => $tfd['UUID'],
                                        'certificado' => $tfd['NoCertificadoSAT'],
                                        'sello' => $tfd['SelloSAT'],
                                        'certEmisor' => $comprobante['NoCertificado'],
                                        'cancelado' => 0,
                                        'u4dig' => 0,
                                        'fk_facture' => $id,
                                        'fecha_emision' => $fecha_emision[0],
                                        'hora_emision' => $fecha_emision[1],
                                        'fecha_timbrado' => $fecha_emision[0],
                                        'hora_timbrado' => $fecha_emision[1],
                                        'divisa' => $comprobante['Moneda'],
                                        'entity_id' => $conf->entity,
                                    ]);

                                    $db->begin();
                                    $result = $db->query($query->toSql());

                                    if ($result > 0) {

                                        $db->commit();
                                        $r++;

                                    } else {

                                        $db->rollback();
                                        $r = 0;

                                    }

                                }
                                //Comprobante Tipo Pago | REP
                                if ($comprobante['TipoDeComprobante'] == "P") {
                                   
                                    $pagos = $comprobante->searchNodes('cfdi:Complemento', 'pago10:Pagos', 'pago10:Pago');
                                    foreach ($pagos as $pago) {
                                        $doctoRelacionados = $pago->searchNodes('pago10:DoctoRelacionado');

                                        if ($doctoRelacionados->count() > 0) {
                                            foreach ($doctoRelacionados as $doctoRelacionado) {
                                                //Consulta para obtener el fk_facture a través del UUID en la tabla CFDIMX
                                                // Selecting via the select method
                                                $fields = [
                                                    'uuid',
                                                    'fk_facture',
                                                ];
                                                $select = PO\QueryBuilder::factorySelect()->select($fields);
                                                $select->from(MAIN_DB_PREFIX . 'cfdimx');
                                                $select->where('uuid', $doctoRelacionado['IdDocumento']);

                                                echo $select->toSql();
                        
                                            }

                                        }
                                    }
                                }

                            } else {

                                //Creación de un Nuevo Tercero si no existe

                                // Load object modCodeTiers
                                $module = (!empty($conf->global->SOCIETE_CODECLIENT_ADDON) ? $conf->global->SOCIETE_CODECLIENT_ADDON : 'mod_codeclient_leopard');
                                if (substr($module, 0, 15) == 'mod_codeclient_' && substr($module, -3) == 'php') {
                                    $module = substr($module, 0, dol_strlen($module) - 4);
                                }
                                $dirsociete = array_merge(array('/core/modules/societe/'), $conf->modules_parts['societe']);
                                foreach ($dirsociete as $dirroot) {
                                    $res = dol_include_once($dirroot . $module . '.php');
                                    if ($res) {
                                        break;
                                    }

                                }

                                $modCodeClient = new $module;
                                $altaEmpresa = new Societe($db);
                                $modCodeClient = new $module($db);

                                $altaEmpresa->entity = getEntity($conf->global->MAIN_INFO_SIREN);
                                $altaEmpresa->nom = $receptor["Nombre"];
                                $altaEmpresa->name = $receptor["Nombre"];
                                $altaEmpresa->client = 1;
                                $altaEmpresa->idprof1 = $receptor["Rfc"];
                                $altaEmpresa->country = "Mexico";
                                $altaEmpresa->country_id = 154;
                                $altaEmpresa->country_code = "MX";
                                $altaEmpresa->code_client = $modCodeClient->getNextValue($altaEmpresa, 0);

                                $db->begin();
                                $result = $altaEmpresa->create($user);
                                $db->commit();

                                if ($result > 0) {

                                    if ($comprobante['TipoDeComprobante'] == "I") {

                                        /***************************/
                                        /* Creación de la factura */
                                        /**************************/

                                        // Cambiamos la fecha a Milisengundos para insertarlo en la factura
                                        $fecha = date("m/d/Y", strtotime($comprobante['Fecha']));
                                        $fechaExplode = explode("/", $fecha);
                                        $dateinvoice = dol_mktime(12, 0, 0, $fechaExplode[0], $fechaExplode[1], $fechaExplode[2]);

                                        // Si facture standard
                                        $factura = new Facture($db);
                                        $factura->socid = $result;
                                        $factura->type = 0;
                                        $factura->ref = $comprobante['Serie'] . "-" . $comprobante['Folio'];
                                        $factura->date = $dateinvoice;

                                        // $factura->remise_absolue = $_POST['remise_absolue'];
                                        // $factura->remise_percent = $_POST['remise_percent'];
                                        // $factura->multicurrency_code = GETPOST('multicurrency_code', 'alpha');
                                        // $factura->multicurrency_tx = GETPOST('originmulticurrency_tx', 'int');

                                        $factura->retained_warranty = 0;
                                        $factura->retained_warranty_fk_cond_reglement = 0;

                                        $factura->fetch_thirdparty();

                                        /* Lineas */

                                        $id = $factura->create($user);

                                        foreach ($conceptos as $concepto) {

                                            $conceptoTraslados = $concepto->searchNodes('cfdi:Impuestos', 'cfdi:Traslados', 'cfdi:Traslado');
                                            foreach ($conceptoTraslados as $impuesto) {
                                                $vat_rate = $impuesto['TasaOCuota'] * 100;
                                            }
                                            //Hacer Foreach para cargar productos
                                            $result = $factura->addline(
                                                $concepto['Descripcion'],
                                                $concepto['ValorUnitario'], // subprice
                                                $concepto['Cantidad'], // quantity
                                                $vat_rate, // vat rate
                                                0, // localtax1_tx
                                                0, // localtax2_tx
                                                0, // fk_product
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
                                                0
                                                //,$langs->trans('Deposit') //Deprecated
                                            );
                                        }

                                        $factura->fetch($id);
                                        $factura->ref = $comprobante['Serie'] . "-" . $comprobante['Folio'];

                                        $factura->validate($user);

                                        mkdir($conf->facture->dir_output . "/" . $factura->ref, 0700);
                                        $file_xml = fopen($conf->facture->dir_output . "/" . $factura->ref . "/" . $tfd['UUID'] . ".xml", "w");
                                        fwrite($file_xml, utf8_encode($xml));
                                        fclose($file_xml);

                                        $fecha_emision = explode("T", $comprobante['Fecha']);
                                        $query = PO\QueryBuilder::insert();

                                        $query->into(MAIN_DB_PREFIX . 'cfdimx')->values([
                                            'tipo_timbrado' => 1,
                                            'factura_serie' => $comprobante['Serie'],
                                            'factura_folio' => $comprobante['Folio'],
                                            'factura_seriefolio' => $comprobante['Serie'] . "-" . $comprobante['Folio'],
                                            'xml' => $xml,
                                            'cadena' => $cfdiData->tfdSourceString(),
                                            'version' => $comprobante['Version'],
                                            'selloCFD' => $tfd['SelloCFD'],
                                            'fechaTimbrado' => $tfd['FechaTimbrado'],
                                            'uuid' => $tfd['UUID'],
                                            'certificado' => $tfd['NoCertificadoSAT'],
                                            'sello' => $tfd['SelloSAT'],
                                            'certEmisor' => $comprobante['NoCertificado'],
                                            'cancelado' => 0,
                                            'u4dig' => 0,
                                            'fk_facture' => $id,
                                            'fecha_emision' => $fecha_emision[0],
                                            'hora_emision' => $fecha_emision[1],
                                            'fecha_timbrado' => $fecha_emision[0],
                                            'hora_timbrado' => $fecha_emision[1],
                                            'divisa' => $comprobante['Moneda'],
                                            'entity_id' => $conf->entity,
                                        ]);

                                        $db->begin();
                                        $result = $db->query($query->toSql());

                                        if ($result > 0) {

                                            $db->commit();
                                            $r++;

                                        } else {

                                            $db->rollback();
                                            $r = 0;

                                        }

                                    } else {
                                        $r = 0;
                                    }
                                }
                            }
                        }
                        if ($receptor["Rfc"] == $conf->global->MAIN_INFO_SIREN) {
                            echo "son facturas proveedor";
                        }
                    }
                }
            }
        }
    }
    if ($r > 0) {
        return true;
    } else {
        return false;
    }
}

/*
 * Actions
 */

if ($action == 'clientes') {

    $directorio = DOL_DATA_ROOT . '/xml/clientes';

    $resultado = insertaXML($directorio);

    if ($resultado == true) {

        $xmls = dirToArray($directorio);

    }

}

if ($action == 'pagos_clientes') {

    $directorio = DOL_DATA_ROOT . '/xml/clientes';

    $resultado = insertaXML($directorio);

    if ($resultado == true) {

        $xmls = dirToArray($directorio);

    }

}


$campos = ['rowid'];
$select = new PO\QueryBuilder\Statements\Select();
$select->select($campos);
$select->from(MAIN_DB_PREFIX.'societe');
$select->where('nom','%mostrador%','like');

$result = $db->query($select->toSql());
$num = $db->num_rows($result);

if($num > 0){
    $obj = $db->fetch_object($result);
}


// None

/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);

llxHeader("", $langs->trans("VivescloudArea"));

print load_fiche_titre($langs->trans("VivescloudArea"), '', 'vivescloud.png@vivescloud');

print '<div class="fichecenter">';


?>



 <a class="butAction" class="background-color:grey;"
                            href="<?php echo DOL_URL_ROOT ?>/custom/vivescloud/facturaglobal.php?action=create&date=<?php echo date('Y-m-d') ?>&socid=<?php echo $obj->rowid ?>&tipopago=LIQ">Crear
                            Factura Global Efectivo</a>
                        <a class="butAction" style="background-color:yellow;"
                            href="<?php echo DOL_URL_ROOT ?>/custom/vivescloud/facturaglobal.php?action=create&date=<?php echo date('Y-m-d') ?>&socid=<?php echo $obj->rowid ?>&tipopago=CB">Crear
                            Factura Global TDC</a>
                        <a class="butAction" style="background-color:blue;color:white;"
                            href="<?php echo DOL_URL_ROOT ?>/custom/vivescloud/facturaglobal.php?action=create&date=<?php echo date('Y-m-d') ?>&socid=<?php echo $obj->rowid ?>&tipopago=28">Crear
                            Factura Global TDD</a>

<!-- <div class="tabBar">
    <div class="opacitymedium">Utilidades XML</div>
    <div class="div-table-responsive-no-min">
        <table class="border centpercent">

            <tr class="oddeven">
                <td>Cargar XML Clientes</td>
                <td><a class="button" href="<?php $_SERVER["PHP_SELF"]?>?action=clientes">Cargar Archivos</a></td>
            </tr>

             <tr class="oddeven">
             <form action='$_SERVER["PHP_SELF"]' type="GET">
                <td>Cargar XML Pagos Clientes</td>
                <td></td>
                <td><button class="button">Cargar Archivos</buttons></td>
            </form>
            </tr>
            <?php if ($xmls): ?>
            <table>
                <?php foreach ($xmls as $ano => $carpeta): ?>

                <tr colspan="2">
                    <td>
                        <h3><?=$ano?></h3>
                    </td>
                </tr>
                <?php if (is_array($carpeta)): ?>
                <?php foreach ($carpeta as $contenido => $xml): ?>

                <?php if (is_array($xml)): ?>
                <?php foreach ($xml as $carpeta => $fichero): ?>
                <tr>
                    <td><?=$fichero?></td>
                </tr>
                <?php endforeach?>
                <?php else: ?>
                <tr>
                    <td><?=$xml?></td>
                </tr>
                <?php endif?>
                <?php endforeach?>
                <?php else: ?>
                <tr>
                    <td>
                        <?=$carpeta?>
                    </td>
                </tr>
                <?php endif?>
                <?php endforeach?>
            </table>
            <?php endif?>
        </table>
    </div>
</div> -->
<?php

/***********************/
/* Carga XML Proveedor */
/**********************/

/* FIN */

$NBMAX = 3;
$max = 3;
print '</div><div class="fichetwothirdright">';

print '<div class="fichethirdleft"><div class="ficheaddleft"></div></div></div>';

// End of page
llxFooter();
$db->close();