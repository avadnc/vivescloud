 <?php
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
dol_include_once('/vivescloud/lib/Smartie.php');
use \CfdiUtils\TimbreFiscalDigital\TfdCadenaDeOrigen;

$id = GETPOST('id');

$object = new Facture($db);
$object->fetch($id);

// echo '<pre>';
// var_dump($object);
// echo '</pre>';
$now = dol_now();

$smarty = new Smartie();

$cliente = new Societe($db);
$cliente->fetch($object->socid);
// var_dump($cliente);
// exit;
$datosTicket = [
    'empresa' => $mysoc->name,
    'cliente' => $cliente,
    'rfc' => $mysoc->idprof1,
    'direccion' => dol_nl2br(dol_format_address($mysoc)),
    'logo' => DOL_URL_ROOT . '/viewimage.php?modulepart=mycompany&amp;file=' . urlencode('logos/thumbs/' . $mysoc->logo_small),
    'ticket' => $object->ref,
    'fecha' => dol_print_date($now, 'dayhourtext'),
    'lineas' => $object->lines,
    'subtotal' => $object->total_ht,
    'iva' => $object->total_tva,
    'total' => $object->total_ttc,
    'moneda' => $object->multicurrency_code,
];
$smarty->assign('datosTicket', $datosTicket);

// $comprobante = _getxmlData($object->id);
// $usoCfdi = _getUsoCfdi($object->array_options['options_usocfdi']);
// $formaPago = _getFormaPago($object->array_options['options_formpagcfdi']);
// $regimenFiscal = _getRegimenFiscal($conf->global->MAIN_INFO_SOCIETE_FORME_JURIDIQUE);
// $qrCode = _getQrCode($object->id);
// $tipoPago = tipoPago($object->mode_reglement_code);
// $tfd = $tfd = $comprobante->searchNode('cfdi:Complemento', 'tfd:TimbreFiscalDigital');

// $datosXml = [
//     'comprobante' => $comprobante,
//     'usoCfdi' => $usoCfdi,
//     'formaPago' => $formaPago,
//     'regimenFiscal' => $regimenFiscal,
//     'qrCode' => $qrCode,
//     'tipoPago' => $tipoPago,
//     'tfd' => $tfd
// ];

// $smarty->assign('datosXml', $datosXml);
$smarty->display('ticket.tpl');

function _getxmlData($idFactura)
{
    global $db;

    $campos = ['xml'];
    $select = PO\QueryBuilder::factorySelect($campos);
    $select->from(MAIN_DB_PREFIX . 'cfdimx');
    $select->where('fk_facture', $idFactura, '=');

    $result = $db->query($select->toSql());
    $num = $db->num_rows($result);

    if ($num > 0) {
        $xmlContents = $db->fetch_object($result);
        $xmlContents = \CfdiUtils\Cleaner\Cleaner::staticClean($xmlContents->xml);

        $cfdi = \CfdiUtils\Cfdi::newFromString($xmlContents);

        $comprobante = $cfdi->getNode(); // Nodo de trabajo del nodo cfdi:Comprobante

        return $comprobante;

    } else {

        return null;
    }

}

function _getQrCode($idFactura)
{
    global $db;

    $campos = ['xml'];
    $select = PO\QueryBuilder::factorySelect($campos);
    $select->from(MAIN_DB_PREFIX . 'cfdimx');
    $select->where('fk_facture', $idFactura, '=');

    $result = $db->query($select->toSql());
    $num = $db->num_rows($result);

    if ($num > 0) {
        $xmlContents = $db->fetch_object($result);
        $cfdi = \CfdiUtils\Cfdi::newFromString($xmlContents->xml);

        $comprobante = $cfdi->getNode(); // Nodo de trabajo del nodo cfdi:Comprobante
        $tfd = $comprobante->searchNode('cfdi:Complemento', 'tfd:TimbreFiscalDigital');
        $emisor = $comprobante->searchNode('cfdi:Emisor');
        $receptor = $comprobante->searchNode('cfdi:Receptor');

        $data_cbb = 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=' . $tfd["UUID"] . '&re=' . $emisor['Rfc'] . '&rr=' . $receptor["Rfc"] . '&tt=' . $comprobante['Total'] . '&fe=' . substr($comprobante['NoCertificado'], -8);

        return $data_cbb;
    } else {
        return null;
    }

}

function _getUsoCfdi($usocfdi)
{

    switch ($usocfdi) {
        case 'G01':
            return "Adquisición de mercancias";
            break;
        case 'G02':
            return 'Devoluciones, descuentos o bonificaciones';
            break;
        case 'G03':
            return 'Gastos en general';
            break;
        case 'I01':
            return 'Construcciones';
            break;
        case 'I02':
            return 'Mobilario y equipo de oficina por inversiones';
            break;
        case 'I03':
            return 'Equipo de transporte';
            break;
        case 'I04':
            return 'Equipo de computo y accesorios';
            break;
        case 'I05':
            return 'Dados, troqueles, moldes, matrices y herramental';
            break;
        case 'I06':
            return 'Comunicaciones telefónicas';
            break;
        case 'I07':
            return 'Comunicaciones satelitales';
            break;
        case 'I08':
            return 'Otra maquinaria y equipo';
            break;
        case 'D01':
            return 'Honorarios médicos, dentales y gastos hospitalarios.';
            break;
        case 'D02':
            return 'Gastos médicos por incapacidad o discapacidad';
            break;
        case 'D03':
            return 'Gastos funerales.';
            break;
        case 'D04':
            return 'Donativos.';
            break;
        case 'D05':
            return 'Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación).';
            break;
        case 'D06':
            return 'Aportaciones voluntarias al SAR.';
            break;
        case 'D07':
            return 'Primas por seguros de gastos médicos.';
            break;
        case 'D08':
            return 'Gastos de transportación escolar obligatoria.';
            break;
        case 'D09':
            return 'Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones.';
            break;
        case 'D10':
            return 'Pagos por servicios educativos (colegiaturas)';
            break;
        case 'P01':
            return 'Por definir';
            break;

    }

}

function _getFormaPago($formapago)
{
    switch ($formapago) {
        case 'PUE':
            return "PAGO EN UNA SOLA EXHIBICIÓN";
            break;
        case 'PPD':
            return "PAGO EN PARCIALIDADES DIFERIDO";
            break;

    }

}

function _getRegimenFiscal($regimen)
{
    switch ($regimen) {
        case 605:
            return 'Sueldos y Salarios e Ingresos Asimilados a Salarios';
            break;
        case 606:
            return 'Arrendamiento';
            break;
        case 608:
            return 'Demás ingresos';
            break;
        case 611:
            return 'Ingresos por Dividendos (socios y accionistas)';
            break;
        case 612:
            return 'Personas Físicas con Actividades Empresariales y Profesionales';
            break;
        case 614:
            return 'Ingresos por intereses';
            break;
        case 615:
            return 'Régimen de los ingresos por obtención de premios';
            break;
        case 616:
            return 'Sin obligaciones fiscales';
            break;
        case 621:
            return 'Incorporación Fiscal';
            break;
        case 622:
            return 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras';
            break;
        case 629:
            return 'De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales';
            break;
        case 630:
            return 'Enajenación de acciones en bolsa de valores';
            break;
        case 601:
            return 'General de Ley Personas Morales';
            break;
        case 603:
            return 'Personas Morales con Fines no Lucrativos';
            break;
        case 607:
            return 'Régimen de Enajenación o Adquisición de Bienes';
            break;
        case 609:
            return 'Consolidación';
            break;
        case 620:
            return 'Sociedades Cooperativas de Producción que optan por Diferir sus Ingresos';
            break;
        case 622:
            return 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras';
            break;
        case 623:
            return 'Opcional para Grupos de Sociedades';
            break;
        case 624:
            return 'Coordinados';
            break;
        case 628:
            return 'Hidrocarburos';
            break;
    }

}

function tipoPago($tipoPago)
{

    switch ($tipoPago) {
        case 'LIQ':
            return 'Efectivo';
            break;
        case 28:
            return 'Tarjeta de Débito';
            break;
        case '99':
            return 'Por Definir';
            break;

        case 'CB':
            return 'Tarjeta de Crédito';
            break;
        case 'CHQ':
            return 'cheque';
            break;

    }
}

?>