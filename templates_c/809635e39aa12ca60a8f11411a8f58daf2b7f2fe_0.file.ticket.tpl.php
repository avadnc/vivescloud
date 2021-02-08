<?php
/* Smarty version 3.1.34-dev-7, created on 2021-02-04 12:40:07
  from 'D:\laragon\www\davila\custom\vivescloud\templates\ticket.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_601c3f879f0ac2_42339585',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '809635e39aa12ca60a8f11411a8f58daf2b7f2fe' => 
    array (
      0 => 'D:\\laragon\\www\\davila\\custom\\vivescloud\\templates\\ticket.tpl',
      1 => 1612464005,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_601c3f879f0ac2_42339585 (Smarty_Internal_Template $_smarty_tpl) {
?><html>

<head>
    <title>
        Imprimir Tikcket
    </title>
    <link rel="stylesheet" type="text/css" href="/custom/vivescloud/css/ticket.css">
</head>

<body>
    <pre>
</pre>
    <div class="entete">
        <div class="logo">
            <img src="<?php echo $_smarty_tpl->tpl_vars['datosTicket']->value['logo'];?>
">
        </div>
        <div class="infos">
            <p class="address">
                <?php echo $_smarty_tpl->tpl_vars['datosTicket']->value['empresa'];?>
<br>
                RFC: <?php echo $_smarty_tpl->tpl_vars['datosTicket']->value['rfc'];?>
<br>
                Dirección: <?php echo $_smarty_tpl->tpl_vars['datosTicket']->value['direccion'];?>

            </p>
            <p class="address">
                Cliente: <?php echo $_smarty_tpl->tpl_vars['datosTicket']->value['cliente']->name;?>
<br>
                RFC: <?php echo $_smarty_tpl->tpl_vars['datosTicket']->value['cliente']->idprof1;?>

            </p>

            <p style="align:right;" class="date_heure">
                Fecha: <?php echo $_smarty_tpl->tpl_vars['datosTicket']->value['fecha'];?>
<br>
                Comprobante: <?php echo $_smarty_tpl->tpl_vars['datosTicket']->value['ticket'];?>


            </p>

        </div>
    </div>

    <br>

    <table class="liste_articles">
        <thead>
            <tr class="titres">
                <th>
                    Código
                </th>
                <th>
                    Descripción
                </th>
                <th>
                    Cantidad
                </th>

                <th>
                    Total
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['datosTicket']->value['lineas'], 'i', false, 'k');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
                <tr>
                    <td>
                        <?php echo $_smarty_tpl->tpl_vars['i']->value->product_ref;?>

                    </td>
                    <td>
                        <?php echo $_smarty_tpl->tpl_vars['i']->value->product_label;?>

                    </td>
                    <td>
                        <?php echo $_smarty_tpl->tpl_vars['i']->value->qty;?>

                    </td>
                    <td class="total">
                        $ <?php echo sprintf("%.2f",$_smarty_tpl->tpl_vars['i']->value->total_ht);?>

                    </td>
                </tr>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </tbody>
    </table>

    <table class="totaux">
        <tr>
            <th class="nowrap">
                SubTotal
            </th>
            <td class="nowrap">
                $ <?php echo sprintf("%.2f",$_smarty_tpl->tpl_vars['datosTicket']->value['subtotal']);?>

                            </td>
        </tr>
        <tr>
            <th class="nowrap">
                IVA</th>
            <td class="nowrap">
                $ <?php echo sprintf("%.2f",$_smarty_tpl->tpl_vars['datosTicket']->value['iva']);?>

                            </td>
        </tr>
        <tr>
            <th class="nowrap">
                Total
            </th>
            <td class="nowrap">
                $
                <?php echo sprintf("%.2f",$_smarty_tpl->tpl_vars['datosTicket']->value['total']);?>
            </td>
        </tr>
    </table>

            <div>
            <p>Gracias Por Su Compra</p>
        </div>
            </body>

    </html><?php }
}
