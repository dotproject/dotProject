<?php
require_once("gacl_admin.inc.php");

//GET takes precedence.
if ($_GET['object_type'] != '') {
	$object_type = $_GET['object_type'];
} else {
	$object_type = $_POST['object_type'];
}

switch(strtolower(trim($object_type))) {
    case 'aco':
        $object_type = 'aco';
	$object_table = $gacl_api->_db_table_prefix . 'aco';
		$object_sections_table = $gacl_api->_db_table_prefix . 'aco_sections';
        break;
    case 'aro':
        $object_type = 'aro';
	$object_table = $gacl_api->_db_table_prefix . 'aro';
		$object_sections_table = $gacl_api->_db_table_prefix . 'aro_sections';
        break;
    case 'axo':
        $object_type = 'axo';
	$object_table = $gacl_api->_db_table_prefix . 'axo';
		$object_sections_table = $gacl_api->_db_table_prefix . 'axo_sections';
        break;
    default:
        echo "ERROR: Must select an object type<br>\n";
        exit();
        break;
}

switch ($_POST['action']) {
    case 'Delete':

        if (count($_POST['delete_object']) > 0) {
            foreach($_POST['delete_object'] as $id) {
                $gacl_api->del_object($id, $object_type, TRUE);
            }
        }

        //Return page.
        $gacl_api->return_page($_POST['return_page']);

        break;
    case 'Submit':
        $gacl_api->debug_text("Submit!!");

        //Update objects
//        while (list(,$row) = @each($_POST['objects'])) {  // obsolete and deprecated in PHP 8 (gwyneth 20210424)
        foreach ($_POST['objects'] as $row) {
            list($id, $value, $order, $name) = $row;
            $gacl_api->edit_object($id, $_POST['section_value'], $name, $value, $order, 0, $object_type);
        }
        unset($id);
        unset($section_value);
        unset($value);
        unset($order);
        unset($name);

        //Insert new sections
//        while (list(,$row) = @each($_POST['new_objects'])) {  // see above
        foreach ($_POST['new_objects'] as $row) {
            list($value, $order, $name) = $row;

            if (!empty($value) AND !empty($name)) {
                $object_id= $gacl_api->add_object($_POST['section_value'], $name, $value, $order, 0, $object_type);
            }
        }
        $gacl_api->debug_text("return_page: ". $_POST['return_page']);
        $gacl_api->return_page($_POST['return_page']);

        break;
    default:
        //Grab section name
        $query = sprintf("select name from $object_sections_table where value = '%s'", $db->qstr($_GET['section_value'], function_exists('get_magic_quotes_runtime') && get_magic_quotes_runtime()));  // REMOVED in PHP 8; throws fatal error (gwyneth 20210413)
        $section_name = $db->GetOne($query);

        $query = sprintf("select
                                    id,
                                    section_value,
                                    value,
                                    order_value,
                                    name
                        from    $object_table
                        where   section_value='%s'
                        order by order_value",
			$db->qstr($_GET['section_value'], function_exists('get_magic_quotes_runtime') && get_magic_quotes_runtime()));  // REMOVED in PHP 8; throws fatal error (gwyneth 20210413)
        $rs = $db->pageexecute($query, $gacl_api->_items_per_page, $_GET['page']);
        $rows = $rs->GetRows();

//        while (list(,$row) = @each($rows)) {  // see above
        foreach ($rows as $row) {
            list($id, $section_value, $value, $order_value, $name) = $row;

                $objects[] = array(
                                                'id' => $id,
                                                'section_value' => $section_value,
                                                'value' => $value,
                                                'order' => $order_value,
                                                'name' => $name
                                            );
        }

        for($i=0; $i < 5; $i++) {
                $new_objects[] = array(
                                                'id' => $i,
                                                'section_value' => NULL,
                                                'value' => NULL,
                                                'order' => NULL,
                                                'name' => NULL
                                            );
        }

        $smarty->assign('objects', $objects);
        $smarty->assign('new_objects', $new_objects);

        $smarty->assign("paging_data", $gacl_api->get_paging_data($rs));

        break;
}

$smarty->assign('section_value', stripslashes($_GET['section_value']));
$smarty->assign('section_name', $section_name);
$smarty->assign('object_type', $object_type);
$smarty->assign('return_page', $_SERVER['REQUEST_URI']);

$smarty->assign('current','edit_'. $object_type .'s');
$smarty->assign('page_title', 'Edit '. strtoupper($object_type) .' Objects');

$smarty->assign("phpgacl_version", $gacl_api->get_version() );
$smarty->assign("phpgacl_schema_version", $gacl_api->get_schema_version() );

$smarty->display('phpgacl/edit_objects.tpl');
?>
