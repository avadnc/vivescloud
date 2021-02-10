<?php
/* Smarty version 3.1.34-dev-7, created on 2021-02-09 11:09:57
  from 'D:\laragon\www\davila\custom\vivescloud\templates\cobranza.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_6022c1e5a874b9_91680260',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9cab657129723b2f2d502a34943982a5c4814131' => 
    array (
      0 => 'D:\\laragon\\www\\davila\\custom\\vivescloud\\templates\\cobranza.tpl',
      1 => 1612890512,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6022c1e5a874b9_91680260 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['action']->value == 'create') {?>

    <?php echo $_smarty_tpl->tpl_vars['load_fiche_titre']->value;?>


    <form method="POST" action="<?php echo $_smarty_tpl->tpl_vars['formaction']->value;?>
">
        <input type="hidden" name="token" value="<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
">
        <input type="hidden" name="action" value="add">
        <?php if ((isset($_smarty_tpl->tpl_vars['backtopage']->value))) {?><input type="hidden" name="backtopage" value="<?php echo $_smarty_tpl->tpl_vars['backtopage']->value;?>
"><?php }?>
        <?php if ((isset($_smarty_tpl->tpl_vars['backtopageforcancel']->value))) {?><input type="hidden" name="backtopageforcancel" value="<?php echo $_smarty_tpl->tpl_vars['backtopageforcancel']->value;?>
">
        <?php }?>

        <?php echo $_smarty_tpl->tpl_vars['cabecera']->value;?>



        <table class="border centpercent tableforfieldcreate">

            <?php echo var_dump($_smarty_tpl->tpl_vars['commonfields']->value);?>

            <?php echo $_smarty_tpl->tpl_vars['extrafields']->value;?>


        </table>

        <?php echo $_smarty_tpl->tpl_vars['fincabecera']->value;?>


        <div class="center">
            <input type="submit" class="button" name="add" value="<?php echo $_smarty_tpl->tpl_vars['create']->value;?>
">

            <input type=<?php if ((isset($_smarty_tpl->tpl_vars['backtopage']->value))) {?> "submit" <?php } else { ?> "button" <?php }?> class="button" name="cancel"
                value="<?php echo $_smarty_tpl->tpl_vars['cancel']->value;?>
" <?php if (!(isset($_smarty_tpl->tpl_vars['backtopage']->value))) {?> onclick="javascript:history.go(-1)" <?php }?>>
        </div>


    </form>
<?php }?>


    <?php echo '<script'; ?>
 type="text/javascript" language="javascript">
        jQuery(document).ready(function() {
                    function init_myfunc() {
                        jQuery("#myid").removeAttr(\'disabled\');
                            jQuery("#myid").attr(\'disabled\',\'disabled\');
                            }
                            init_myfunc(); jQuery("#mybutton").click(function() {
                                init_myfunc();
                            });
                        });
    <?php echo '</script'; ?>
>
<?php }
}
