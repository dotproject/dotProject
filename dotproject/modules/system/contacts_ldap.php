<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$AppUI->savePlace();

$canEdit = getPermission($m, 'edit');
$canRead = getPermission($m, 'view');
if (!$canRead) {
	$AppUI->redirect('m=public&a=access_denied');
}

// NOTE: Probably should look at replacing deprecated db_* functions.
$sql_table = dPgetConfig('dbprefix','').'contacts';

//Modify this mapping to match your LDAP->contact structure
//For instance, of you want the contact_phone2 field to be populated out of, say telephonenumber2 then you would just modify
//	'physicaldeliveryofficename' => 'contact_phone2',
// or 
//	'telephonenumber2' => 'contact_phone2',

$sql_ldap_mapping = array('givenname' => 'contact_first_name',
                          'sn' => 'contact_last_name',
                          'title' => 'contact_title',
                          'companyname' => 'contact_company',
                          'department' => 'contact_department',
                          'employeeid' => 'contact_type',
                          'mail' => 'contact_email',
                          'telephonenumber' => 'contact_phone',
                          'physicaldeliveryofficename' => 'contact_phone2',
                          'postaladdress' => 'contact_address1',
                          'l' => 'contact_city',
                          'st' => 'contact_state',
                          'postalcode' => 'contact_zip',
                          'c' => 'contact_country');

$titleBlock = new CTitleBlock('Import Contacts from LDAP Directory', '', 'admin', '');
$titleBlock->addCrumb('?m=system', 'system admin');
$titleBlock->show();


if (isset($_POST['server'])) {
	$AppUI->setState('LDAPServer', $_POST['server']);
}
$server = $AppUI->getState('LDAPServer', '');
//$server = 'KMP00';

if (isset($_POST['bind_name'])) {
	$AppUI->setState('LDAPBindName', $_POST['bind_name']);
}
$bind_name = $AppUI->getState('LDAPBindName', '');
//$bind_name = 'dcordes';

$bind_password = dPgetParam($_POST,'bind_password', '');

if (isset($_POST['port'])) {
	$AppUI->setState('LDAPPort', $_POST['port']);
}
$port = $AppUI->getState('LDAPPort', '389');

if (isset($_POST['dn'])) {
	$AppUI->setState('LDAPDN', $_POST['dn']);
}
$dn = $AppUI->getState('LDAPDN', '');
//$dn = 'OU=USA,O=MINEBEA';

if (isset($_POST['filter'])) {
	$AppUI->setState('LDAPFilter', $_POST['filter']);
}
$filter = $AppUI->getState('LDAPFilter',  '(objectclass=Person)');
//$filter = '(objectclass=dominoPerson)'; 

$import = dPgetParam($_POST,'import');
$test = dPgetParam($_POST,'test');

$AppUI->setState('LDAPProto', dPgetParam($_POST, 'ldap_proto'));
$proto = $AppUI->getState('LDAPProto', '3');

?>
<form method="post" action="">
<table border="0" cellpadding="2" cellspacing="1" width="600" class="std">
	<tr>
		<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Server'); ?>:</td>
		<td><input type="text" name="server" value="<?php echo $server; ?>" size="50" /></td>
	</tr>
	<tr>
		<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Port'); ?>:</td>
		<td><input type="text" name="port" value="<?php echo $port; ?>" size="4" /></td>
	</tr>
	<tr>
		<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Protocol'); ?>:</td>
		<td><?php
echo $AppUI->_('Version 2') . ' <input type="radio" name="ldap_proto" value="2"';
if ($proto == '2') {
	echo ' checked="checked"';
}
echo ' /> ' . $AppUI->_('Version 3') . ' <input type="radio" name="ldap_proto" value="3"';
if ($proto == '3') {
	echo ' checked="checked"';
}
echo ' />';
?></td>
	</tr>
	<tr>
		<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Bind Name'); ?>:</td>
		<td><input type="text" name="bind_name" value="<?php echo $bind_name; ?>" size="50" /></td>
	</tr>
	<tr>
		<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Bind Password'); ?>:</td>
		<td><input type="password" name="bind_password" value="<?php echo $bind_password; ?>" size="25" /></td>
	</tr>
	<tr>
		<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Base DN'); ?>:</td>
		<td><input type="text" name="dn" value="<?php echo $dn; ?>" size="100" /></td>
	</tr>
	<tr>
		<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Filter'); ?>:</td>
		<td><input type="text" name="filter" value="<?php echo $filter; ?>" size="100" /></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="submit" name="test" value="<?php 
echo $AppUI->_('Test Connection and Query'); ?>" /><input type="submit" name="import" value="<?php 
echo $AppUI->_('Import Users'); ?>" /></td>
	</tr>
</table>
</form>
<pre>
<?php
echo '<b>';
if (isset($test)) {
	echo $test;
}
if (isset($import)) {
	echo $import;
}
echo "</b></pre>\n<hr />";
if (isset($test) || isset($import)) {

	$ds = @ldap_connect($server, $port);

	if (!$ds) {
	    if (function_exists('ldap_error')) {
		print ldap_error($ds)."\n"; 
	    } else {
		print '<span style="color:red;font-weight:bold;">ldap_connect failed.</span>' . "\n";
	    }
	} else {
		print "ldap_connect succeeded.\n";
	}

	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $proto);

	if (!@ldap_bind($ds,$bind_name,$bind_password)) {
	    print '<span style="color:red;font-weight:bold;">ldap_bind failed.</span>' . "\n";
	    if (function_exists('ldap_error')) {
		print ldap_error($ds)."\n"; 
	    }
	} else {
		print "ldap_bind successful.\n";
	}

	$return_types = array();
	foreach ($sql_ldap_mapping as $ldap => $sql) {
		$return_types[] = $ldap;
	}

print 'basedn: '.$dn.'<br />';
print 'expression: '.$filter.'<br />';

	$sr = @ldap_search($ds,$dn,$filter,$return_types);
	
	if ($sr) {
		print "Search completed Sucessfully.\n";
	} else {
		print 'Search Error: ['.ldap_errno($ds).'] '.ldap_error($ds)."\n";
	}


?>
</pre>
<?php

//	print 'Result Count:'.(ldap_count_entries($ds,$sr))."\n";
	$info = ldap_get_entries($ds, $sr);
	if (!$info['count']) {
		print "No users were found.\n";
	} else {
		print 'Total Users Found:'.$info['count']."\n<hr />";
?>
<table border="0" cellpadding="1" cellspacing="0" width="98%" class="std">
<?php
		if (isset($test)) {
			foreach ($sql_ldap_mapping as $ldap => $sql) {
				print '<th>'.$sql.'</th>';
			}
		} else {
			$contacts = db_loadList("SELECT contact_id, contact_first_name, contact_last_name FROM $sql_table");
			foreach ($contacts as $contact) {
				$contact_list[$contact['contact_first_name'].' '.$contact['contact_last_name']] = $contact['contact_id'];
			}
			unset($contacts);
		}
		
		for ($i = 0; $i<$info['count']; $i++) {
			$pairs = array();
			print "<tr>\n";
			foreach ($sql_ldap_mapping as $ldap_name => $sql_name) {
				unset($val);
				if (isset($info[$i][$ldap_name][0])) {
					$val = clean_value($info[$i][$ldap_name][0]);
				} 
				if (isset($val)) {
					//if an email address is not specified in Domino you get a crazy value for this field that looks like FOO/BAR%NAME@domain.com  This'll filter those values out.
					if (isset($test) && $sql_name=='contact_email' && mb_substr_count($val,'%')>0) {
					?>
						<td><span style="color:#880000;"><?php echo $AppUI->_('bad email address')?></span></td>
					<?php
						continue;
					}
					$pairs[$sql_name] = $val;
					if (isset($test)) {
					?>
						<td><?php echo $val?></td>
					<?php
					}
				} else {
					?>
						<td>-</td>
					<?php
				}
			}

			if (isset($import)) {
				$pairs['contact_order_by'] = $pairs['contact_last_name'].' '.$pairs['contact_first_name'];
				//Check to see if this value already exists.
				if (isset($contact_list[$pairs['contact_first_name'].' '.$pairs['contact_last_name']])) {
					//if it does, remove the old one.
					$pairs['contact_id'] = $contact_list[$pairs['contact_first_name'].' '.$pairs['contact_last_name']];
					db_updateArray($sql_table, $pairs, 'contact_id');
					echo ('<td><span style="color:#880000;">There is a duplicate record for ' 
						  . $pairs['contact_first_name'] .' ' .$pairs['contact_last_name'] 
						  . ", the record has been updated.</span></td>\n");
				} else {
					echo '<td>Adding '.$pairs['contact_first_name'].' '.$pairs['contact_last_name'].".</td>\n";
					db_insertArray($sql_table,$pairs);
				}
			}
			print '</tr>\n';

	/*
			for ($ii=0; $ii<$info[$i]['count']; $ii++) {
				$data = $info[$i][$ii];
				for ($iii=0; $iii<$info[$i][$data]['count']; $iii++) {
					echo $data.':&nbsp;&nbsp;'.$info[$i][$data][$iii]."\n";
				}
			}
	*/
			echo "\n";
		}
	}
echo '</table>';
	ldap_close($ds);
}

function clean_value($str) {
	$bad_values = array("'");
	return str_replace($bad_values,'',$str);
}
?>
