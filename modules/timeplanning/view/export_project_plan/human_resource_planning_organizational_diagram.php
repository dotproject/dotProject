<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_company_role.class.php");
require_once DP_BASE_DIR . "/modules/system/roles/roles.class.php";
$controllerCompanyRole = new ControllerCompanyRole();
?>
<table class="printTable">
    <tr>
        <td style="padding-left: 10px;">
            
                <?php
                $roles = $controllerCompanyRole->getCompanyRoles($companyId);
                foreach ($roles as $role) {
                    $id = $role->getId();
                    $name = $role->getDescription();
                    $identation = $role->getIdentation();
                    echo "<span style='color:white'>";
                    for ($index=0;$index<strlen($identation)/3;$index++){
                        echo "-";
                    }
                    echo "</span>";
                    echo $name;
                    echo "<br />";
                }
                ?>
            
        </td>
    </tr>
</table>