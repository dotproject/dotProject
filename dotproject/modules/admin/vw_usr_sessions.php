<?php /* ADMIN  $Id$ */ 
GLOBAL $dPconfig, $canEdit, $canDelete, $stub, $where, $orderby;

/*
 * Flag value to determine if "logout user" button should show. 
 * Could be determined by a configuration value in the future.
 */
$logoutUserFlag = true;

if ($_GET["out_user_id"] && $_GET["out_name"] && $canEdit && $canDelete) {
    $boot_user_id = $_GET["out_user_id"];
    $boot_user_name = $_GET["out_name"];
    $details = $boot_user_name . " by " . $AppUI->user_first_name . " " . $AppUI->user_last_name;

    // one session or many?
    if ($_GET["out_session"] && $_GET["out_user_log_id"]) {
        $boot_user_session = $_GET["out_session"];
        $boot_user_log_id = $_GET["out_user_log_id"];
        $boot_query_row = false;
    } else if ($canEdit && $canDelete && $logoutUserFlag) {
        // query for all sessions open for a given user
        $r = new DBQuery;
        $r->addTable('sessions', 's');
        $r->addQuery('DISTINCT(session_id), user_access_log_id');
        $r->addJoin('user_access_log', 'ual', 'session_user = user_access_log_id');
        $r->addWhere("user_id = $boot_user_id");
        $r->addOrder('user_access_log_id');
        
        //execute query and fetch results
        $r->exec();
        $boot_query_row = $r->fetchRow();
        if ($boot_query_row) {
            $boot_user_session = $boot_query_row["session_id"];
            $boot_user_log_id = $boot_query_row["user_access_log_id"];
        }
    }
    
    do{
        if ($boot_user_id == $AppUI->user_id && $boot_user_session == $_COOKIE["PHPSESSID"]) {
            $AppUI->resetPlace();
            $AppUI->redirect("logout=-1");
        } else {
            addHistory('login', $boot_user_id, 'logout', $details);
            dPsessionDestroy( $boot_user_session, $boot_user_log_id);
        }
        
        if ($boot_query_row) {
            $boot_query_row = $r->fetchRow();
            if($boot_query_row) {
                $boot_user_session = $boot_query_row["session_id"];
                $boot_user_log_id = $boot_query_row["user_access_log_id"];
            } else {
                $r->clear();
            }
        }
        
    } while ($boot_query_row);
    
    $msg = $boot_user_name . " logged out by " . $AppUI->user_first_name . " " . $AppUI->user_last_name;
    $AppUI->setMsg( $msg, UI_MSG_OK );
    $AppUI->redirect("m=admin&tab=3");
}

$q  = new DBQuery;
$q->addTable('sessions', 's');
$q->addQuery('DISTINCT(session_id), user_access_log_id, u.user_id as u_user_id, user_username, contact_last_name, contact_first_name, company_name, contact_company, date_time_in, user_ip');

$q->addJoin('user_access_log', 'ual', 'session_user = user_access_log_id');
$q->addJoin('users', 'u', 'ual.user_id = u.user_id');
$q->addJoin('contacts', 'con', 'u.user_contact = contact_id');
$q->addJoin('companies', 'com', 'contact_company = company_id');
$q->addOrder($orderby);
$rows = $q->loadList();
$q->clear();

$tab = dPgetParam($_REQUEST, "tab", 0);

?>

<table cellpadding="2" cellspacing="1" border="0" width="100%" class="tbl">
  <tr>
    <th colspan="2">&nbsp; <?php echo $AppUI->_('sort by'); ?>:&nbsp;</th>
    <th width="150"><a href="?m=admin&a=index&orderby=user_username" class="hdr"><?php echo $AppUI->_('Login Name'); ?></a></th>
    <th><a href="?m=admin&a=index&orderby=contact_last_name" class="hdr"><?php echo $AppUI->_('Real Name'); ?></a></th>
    <th><a href="?m=admin&a=index&orderby=contact_last_name" class="hdr"><?php echo $AppUI->_('Company');?></a></th>
    <th><a href="?m=admin&a=index&orderby=date_time_in" class="hdr"><?php echo $AppUI->_('Date Time IN');?></a></th>
    <th><a href="?m=admin&a=index&orderby=user_ip" class="hdr"><?php echo $AppUI->_('Internet Address');?></a></th>
  </tr>

<?php 
    foreach ($rows as $row) { 
        echo ("  <tr>\n");
        echo ("    <td align=\"center\" nowrap=\"nowrap\">\n") ;
        if ($canEdit && $canDelete) {
            echo ('<input type="button" class="button" value="'.$AppUI->_('logout_session')
                  ."\" onClick=\"javascript:window.location='./index.php?m=admin&tab=3&out_session=".$row['session_id']
                  .'&out_user_log_id='.$row['user_access_log_id'].'&out_user_id='.$row['u_user_id']
                  .'&out_name='.$row['contact_first_name'].'%20'.$row['contact_last_name']."';\"></input>\n") ;
        }
        echo ("    </td>\n");
        echo ("    <td align=\"center\" nowrap=\"nowrap\">\n") ;
        if ($canEdit && $canDelete && $logoutUserFlag) {
            echo ('<input type="button" class=button value="'.$AppUI->_('logout_user')
                  ."\" onClick=\"javascript:window.location='./index.php?m=admin&tab=3&out_user_id=".$row['u_user_id']
                  .'&out_name='.$row['contact_first_name'].'%20'.$row['contact_last_name']."';\"></input>\n") ;
        }
        echo ("    </td>\n");
        echo ('    <td><a href="./index.php?m=admin&a=viewuser&user_id='.$row['u_user_id'].'">'.$row['user_username']
              ."</a></td>\n");
        echo ('    <td>');
        
        if ($row['contact_first_name'] && $row['contact_last_name']) {
            echo ($row['contact_last_name'] .', '.$row['contact_first_name']);
        }
        else {
            echo ('<span style="font-style: italic">unknown</span>');
        }
        
        echo ("</td>\n");
        echo ('    <td><a href="./index.php?m=companies&a=view&company_id='.$row['contact_company'].'">'.$row['company_name']
              ."</a></td>\n");
        echo ('    <td>'.$row['date_time_in']."</td>\n");
        echo ('    <td>'.$row['user_ip']."</td>\n");
        echo ("  </tr>\n");
    }
?>
</table>