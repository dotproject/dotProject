<style>
    #header,
    #footer {
        position: fixed;
        left: 0;
        right: 0px;
        color: silver/*#aaa*/;
        font-size: 0.9em;
    }

    #header {
        top: 0;
        border-bottom: 0.1pt solid #aaa;
    }

    #footer {
        bottom: 0;
        border-top: 0.1pt solid #aaa;
    }

    #header table,
    #footer table {
        width: 95%;
        border-collapse: collapse;
        border: none;
    }

    #header td,
    #footer td {
        padding: 0;
        width: 50%;
        color:silver/*#C0C0C0*/;
    }

    .page-number {
        text-align: center;
    }

    .page-number:before {
        content: counter(page);
    }


</style>


<div id="header">
    <table>
        <tr>
            <td>
                <?php echo $AppUI->_("LBL_PROJECT_PROJECT",UI_OUTPUT_HTML); ?> : <?php echo $projectName ?>
                <br />
                <?php echo $AppUI->_("LBL_PROJECT_COMPANY",UI_OUTPUT_HTML); ?>: <?php echo $companyName ?>
            </td>
            <td style="text-align: right">
                    <?php echo $AppUI->_("LBL_PROJECT_PROJECT_MANAGER",UI_OUTPUT_HTML); ?>: <?php echo ucwords($projectManager) ?>
                    
            </td>
        </tr>
    </table>
</div>

<div id="footer">
    <div class="page-number"></div>
</div>