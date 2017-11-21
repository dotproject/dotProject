<?php
require_once DP_BASE_DIR . "/modules/human_resources/configuration_functions.php";
require_once DP_BASE_DIR . "/modules/human_resources/allocation_functions.php";
require_once DP_BASE_DIR . "/modules/human_resources/human_resources.class.php";

function cal_work_day_conv($val) {
    global $locale_char_set;
    setlocale(LC_ALL, "en_AU" . (($locale_char_set) ? ("." . $locale_char_set) : ".utf8"));
    $wk = Date_Calc::getCalendarWeek(null, null, null, "%a", LOCALE_FIRST_DAY);
    setlocale(LC_ALL, $AppUI->user_lang);
    $day_name = $wk[($val - LOCALE_FIRST_DAY) % 7];
    if ($locale_char_set == "utf-8" && function_exists("utf8_encode")) {
        $day_name = utf8_encode($day_name);
    }
    return htmlentities($day_name, ENT_COMPAT, $locale_char_set);
}

$res =  getDetailedUsersByCompanyId($companyId);

$cwd = array();
$cwd[0] = "0";
$cwd[1] = "1";
$cwd[2] = "2";
$cwd[3] = "3";
$cwd[4] = "4";
$cwd[5] = "5";
$cwd[6] = "6";
$cwd_conv = array_map("cal_work_day_conv", $cwd);
//translation to portuguese
$cwd_conv[0]="Domingo";
$cwd_conv[1]="Segunda-feira";
$cwd_conv[2]="Terça-feira";
$cwd_conv[3]="Quarta-feira";
$cwd_conv[4]="Quinta-feira";
$cwd_conv[5]="Sexta-feira";
$cwd_conv[6]="Sábado";
?>
<table class="printTable" >
    <tr>
        <th style="vertical-align: top" >
            <?php echo $AppUI->_("User username",UI_OUTPUT_HTML); ?>
        </th>
        <th style="vertical-align: top" >
            <?php echo $AppUI->_("Role competence" ,UI_OUTPUT_HTML); ?>
        </th>
        <th style="text-wrap: normal; vertical-align: top">
            <?php echo $AppUI->_("Lattes URL",UI_OUTPUT_HTML); ?>
        </th>
        <th align="center" valign="top"> <?php echo $cwd_conv[0]; ?> </th>
        <th align="center" valign="top"> <?php echo $cwd_conv[1]; ?> </th>
        <th align="center" valign="top"> <?php echo $cwd_conv[2]; ?> </th>
        <th align="center" valign="top"> <?php echo $cwd_conv[3]; ?> </th>
        <th align="center" valign="top"> <?php echo $cwd_conv[4]; ?> </th>
        <th align="center" valign="top"> <?php echo $cwd_conv[5]; ?> </th>
        <th align="center" valign="top"> <?php echo $cwd_conv[6]; ?> </th>
    </tr>
    <?php
    for ($res; !$res->EOF; $res->MoveNext()) {
        $user_id = $res->fields["user_id"];
        $user_has_human_resource = userHasHumanResource($user_id);
        $style = $user_has_human_resource ? "" : "background-color:#ED9A9A; font-weight:bold";
        $contact_name = $res->fields["contact_first_name"] . " ". $res->fields["contact_last_name"];
        $query = new DBQuery();
        $query->addTable("human_resource", "h");
        $query->addQuery("human_resource_id");
        $query->addWhere("h.human_resource_user_id = " . $user_id);
        $resHR = $query->exec();
        $human_resource_id = $resHR->fields["human_resource_id"];
        $query->clear();
        $obj = new CHumanResource();
        $existsHumanResource = $obj->load($human_resource_id);
        $concat_roles_names = "";
        if ($existsHumanResource) {
            $query = new DBQuery();
            $query->addTable("human_resource_roles", "r");
            $query->addQuery("h.human_resources_role_name, h.human_resources_role_id");
            $query->innerJoin("human_resources_role", "h", "h.human_resources_role_id = r.human_resources_role_id");
            $query->addWhere("r.human_resource_id = " . $human_resource_id);
            $sql = $query->prepare();
            $roles = db_loadList($sql);
            $roles_array = array();
            foreach ($roles as $role) {
                array_unshift($roles_array, $role["human_resources_role_name"]);
            }
            $concat_roles_names = implode(", ", $roles_array);
        }
        ?>  

        <tr style="<?php echo $style; ?>">
            <td>
                <?php echo $contact_name; ?>
            </td>
            <td>
                <?php echo $concat_roles_names; ?>
            </td>
            <td style="text-wrap: normal; word-break:break-all"><?php echo $obj->human_resource_lattes_url; ?></td>   
            
            <?php if( $obj->eventual==0){ ?>
            <td><?php echo $obj->human_resource_mon; ?></td>
            <td><?php echo $obj->human_resource_tue; ?></td>
            <td><?php echo $obj->human_resource_wed; ?></td>
            <td><?php echo $obj->human_resource_thu; ?></td>
            <td><?php echo $obj->human_resource_fri; ?></td>
            <td><?php echo $obj->human_resource_sat; ?></td>
            <td><?php echo $obj->human_resource_sun; ?></td>
            <?php }else{ ?>
                <td colspan="7" style="text-align: center;text-transform: lowercase;font-style: italic">-- <?php echo $AppUI->_("LBL_EVENTUAL_INVOLVIMENT",UI_OUTPUT_HTML); ?> --</td>
            <?php }?>
        </tr>
        <?php
    }
    $query->clear();
    ?>
</table>