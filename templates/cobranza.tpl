{if $action eq 'create'}

    {$load_fiche_titre}

    <form method="POST" action="{$formaction}">
        <input type="hidden" name="token" value="{$token}">
        <input type="hidden" name="action" value="add">
        {if isset($backtopage)}<input type="hidden" name="backtopage" value="{$backtopage}">{/if}
        {if isset($backtopageforcancel) }<input type="hidden" name="backtopageforcancel" value="{$backtopageforcancel}">
        {/if}

        {$cabecera}


        <table class="border centpercent tableforfieldcreate">

            {$commonfields|@var_dump}
            {$extrafields}

        </table>

        {$fincabecera}

        <div class="center">
            <input type="submit" class="button" name="add" value="{$create}">

            <input type={if isset($backtopage)} "submit" {else} "button" {/if} class="button" name="cancel"
                value="{$cancel}" {if !isset($backtopage)} onclick="javascript:history.go(-1)" {/if}>
        </div>


    </form>
{/if}

{literal}
    <script type="text/javascript" language="javascript">
        jQuery(document).ready(function() {
                    function init_myfunc() {
                        jQuery("#myid").removeAttr(\'disabled\');
                            jQuery("#myid").attr(\'disabled\',\'disabled\');
                            }
                            init_myfunc(); jQuery("#mybutton").click(function() {
                                init_myfunc();
                            });
                        });
    </script>
{/literal}